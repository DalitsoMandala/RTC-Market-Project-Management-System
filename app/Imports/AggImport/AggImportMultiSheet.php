<?php
namespace App\Imports\AggImport;

use App\Imports\AggImport\AggImportSheet;
use App\Models\JobProgress;
use App\Models\User;
use App\Notifications\ImportFailureNotification;
use App\Notifications\ImportSuccessNotification;
use App\Traits\ChecksBlankSheets;
use App\Traits\FormEssentials;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;
use Throwable;

class AggImportMultiSheet implements WithMultipleSheets, WithChunkReading, WithEvents, ShouldQueue, WithBatchInserts
{
    use FormEssentials, ChecksBlankSheets;

    public $filePath;
    public $expectedSheetNames = [
        "Aggregated Report",
    ];

    protected $totalRows       = 0;
    protected $expectedHeaders = [];

    public ?string $organisation_id;
    public ?string $user_id;
    public ?string $uuid;
    public ?string $file_link;
    public ?string $table_name;
    public ?string $description;
    public ?string $crop            = null;
    public array $submissionDetails = [];

    /**
     * Max tries for queue execution
     */
    public $tries = 1;

    public function __construct($filePath, $user_id, $organisation_id, $uuid, array $submissionDetails)
    {
        $this->filePath = $filePath;

        $this->user_id         = $user_id;
        $this->organisation_id = $organisation_id;
        $this->uuid            = $uuid;

        $this->table_name        = $submissionDetails['table_name'] ?? null;
        $this->description       = $submissionDetails['description'] ?? null;
        $this->crop              = $submissionDetails['crop'] ?? null;
        $this->submissionDetails = $submissionDetails;

        foreach ($this->expectedSheetNames as $sheetName) {
            $this->expectedHeaders[$sheetName] = $this->AggregatedReportColumns();
        }
    }

    public function sheets(): array
    {
        return [
            'Aggregated Report' => new AggImportSheet(
                user_id: $this->user_id,
                organisation_id: $this->organisation_id,
                uuid: $this->uuid,
                crop: $this->crop
            ),
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                try {
                    $rowCounts = $event->reader->getTotalRows();

                    // Perform assertions for blank/invalid sheets
                    $this->assertBlankSheetRules(
                        rowCounts: $rowCounts,
                        required: [
                            'Aggregated Report' => 1,
                        ],
                        optional: [],
                        expectedHeaders: $this->expectedHeaders
                    );

                    $this->totalRows = array_reduce($this->expectedSheetNames, function ($sum, $sheetName) use ($rowCounts) {
                        return $sum + (max(0, ($rowCounts[$sheetName] ?? 1) - 1)); // excluding headers safely
                    }, 0);

                    JobProgress::updateOrCreate(
                        ['cache_key' => $this->uuid],
                        [
                            'total_rows'     => $this->totalRows,
                            'processed_rows' => 0,
                            'progress'       => 0,
                            'status'         => 'processing',
                            'user_id'        => $this->user_id,
                            'form_name'      => 'Aggregated Report Import',
                            'error'          => null,
                        ]
                    );

                    Cache::put("{$this->uuid}", 0, now()->addMinutes(30));

                } catch (Throwable $e) {
                    $this->handleFailure($e, 'BeforeImport Event Failure');
                    throw $e; // Re-throw so Laravel Excel marks import as failed
                }
            },

            AfterImport::class  => function (AfterImport $event) {
                try {
                    $user = User::find($this->user_id);

                    if ($user) {
                        if ($user->hasAnyRole('manager')) {
                            $user->notify(
                                new ImportSuccessNotification(
                                    uuid: $this->uuid,
                                    link: route('cip-aggregated-reports')
                                )
                            );
                        } elseif ($user->hasAnyRole('admin')) {
                            $user->notify(
                                new ImportSuccessNotification(
                                    uuid: $this->uuid,
                                    link: route('admin-aggregated-reports')
                                )
                            );
                        }
                    }

                    JobProgress::updateOrCreate(
                        ['cache_key' => $this->uuid],
                        [
                            'status'   => 'completed',
                            'progress' => 100,
                        ]
                    );
                } catch (Throwable $e) {
                    Log::error('Failed during AfterImport event notification', [
                        'uuid'  => $this->uuid,
                        'error' => $e->getMessage(),
                    ]);
                }
            },

            ImportFailed::class => function (ImportFailed $event) {
                $this->handleFailure($event->getException(), 'Excel Event ImportFailed');
            }
        ];
    }

    /**
     * Unified error handling method for catching lifecycle failures.
     */
    protected function handleFailure(Throwable $exception, string $context = ''): void
    {
        $errorMessage = $exception->getMessage();

        Log::error("Import Failed [{$context}]: " . $errorMessage, [
            'file'    => $this->filePath,
            'uuid'    => $this->uuid,
            'user_id' => $this->user_id,
            'trace'   => $exception->getTraceAsString(),
        ]);

        // Update Job progress table state safely
        JobProgress::updateOrCreate(
            ['cache_key' => $this->uuid],
            [
                'status' => 'failed',
                'error'  => $errorMessage,
            ]
        );

        // Notify user if reachable
        try {
            $user = User::find($this->user_id);
            if ($user) {
                $route = $this->submissionDetails['route'] ?? route('dashboard');
                $user->notify(new ImportFailureNotification(
                    $errorMessage,
                    $route,
                    $this->uuid
                ));
            }
        } catch (Throwable $notificationException) {
            Log::error("Could not send ImportFailureNotification: " . $notificationException->getMessage(), [
                'uuid' => $this->uuid,
            ]);
        }
    }

    /**
     * Laravel Queue native fallback handler in case of worker failure.
     */
    public function failed(Throwable $exception): void
    {
        $this->handleFailure($exception, 'Queue Job Failed');
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
