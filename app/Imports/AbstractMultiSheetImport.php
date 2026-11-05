<?php
namespace App\Imports;

use App\Models\JobProgress;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\ImportFailureNotification;
use App\Notifications\ImportSuccessNotification;
use App\Traits\ChecksBlankSheets;
use App\Traits\FormEssentials;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\ImportFailed;

abstract class AbstractMultiSheetImport implements WithMultipleSheets, WithChunkReading, WithEvents, ShouldQueue, WithBatchInserts
{
    use Importable, RegistersEventListeners;
    use FormEssentials, ChecksBlankSheets;

    protected array $expectedSheetNames = [];
    protected array $expectedHeaders = [];
    protected string $cacheKey;
    protected string $filePath;
    protected array $submissionDetails = [];
    protected int $totalRows = 0;

    /**
     * Database table associated with this import.
     */
    abstract protected function getTableName(): string;

    /**
     * Form name used in FormEssentials traits and JobProgress tracking.
     */
    abstract protected function getFormName(): string;

    /**
     * Target Model class (used to delete records on import failure).
     */
    abstract protected function getModelClass(): string;

    /**
     * Define the database column name for the submission period ID.
     * Defaults to 'submission_period_new_id'. Override as needed.
     */
    protected function getSubmissionPeriodColumnName(): string
    {
        return 'submission_period_new_id';
    }

    /**
     * Map sheet requirements for blank sheet assertions.
     */
    protected function getSheetRules(): array
    {
        $required = [];
        foreach ($this->expectedSheetNames as $sheetName) {
            $required[$sheetName] = 2;
        }

        return [
            'required' => $required,
            'optional' => [],
        ];
    }

    public function __construct(string $cacheKey, string $filePath, array $submissionDetails)
    {
        // Enforce synchronization between cacheKey and batch_no
        $this->submissionDetails = $submissionDetails;
        $this->cacheKey          = $this->submissionDetails['batch_no'] ?? $cacheKey;
        $this->filePath          = $filePath;

        $formKey = $this->getFormName();
        foreach ($this->expectedSheetNames as $sheetName) {
            if (isset($this->forms[$formKey][$sheetName])) {
                $this->expectedHeaders[$sheetName] = array_keys($this->forms[$formKey][$sheetName]);
            }
        }
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => fn (BeforeImport $event) => $this->handleBeforeImport($event),
            AfterImport::class  => fn (AfterImport $event)  => $this->handleAfterImport($event),
            ImportFailed::class => fn (ImportFailed $event) => $this->handleImportFailed($event),
        ];
    }

    protected function handleBeforeImport(BeforeImport $event): void
    {
        $rowCounts = $event->reader->getTotalRows();
        $rules     = $this->getSheetRules();

        $this->assertBlankSheetRules(
            rowCounts: $rowCounts,
            required: $rules['required'] ?? [],
            optional: $rules['optional'] ?? [],
            expectedHeaders: $this->expectedHeaders
        );

        $this->totalRows = array_reduce($this->expectedSheetNames, function ($sum, $sheetName) use ($rowCounts) {
            return $sum + max(0, ($rowCounts[$sheetName] ?? 0) - 2);
        }, 0);

        JobProgress::updateOrCreate(
            ['cache_key' => $this->cacheKey],
            [
                'total_rows'     => $this->totalRows,
                'processed_rows' => 0,
                'progress'       => 0,
                'user_id'        => $this->submissionDetails['user_id'],
                'form_name'      => $this->getFormName(),
            ]
        );

        Cache::put("{$this->cacheKey}_import_progress", 0, now()->addMinutes(30));
    }

    protected function handleAfterImport(AfterImport $event): void
    {
        $user = User::find($this->submissionDetails['user_id']);

        $roleConfig = [
            'manager' => ['status' => 'approved', 'route' => 'cip-submissions',       'batch_no' => $this->cacheKey],
            'admin'   => ['status' => 'approved', 'route' => 'admin-submissions',     'batch_no' => $this->cacheKey],
            'staff'   => ['status' => 'pending',  'route' => 'cip-staff-submissions', 'batch_no' => $this->cacheKey],
            'default' => ['status' => 'pending',  'route' => 'external-submissions',  'batch_no' => $this->cacheKey],
        ];

        $config = $roleConfig['default'];
        foreach (['manager', 'admin', 'staff'] as $role) {
            if ($user->hasAnyRole($role)) {
                $config = $roleConfig[$role];
                break;
            }
        }

        Submission::create([
            'batch_no'                             => $config['batch_no'],
            'form_id'                              => $this->submissionDetails['form_id'],
            $this->getSubmissionPeriodColumnName() => $this->submissionDetails['submission_period_new_id'],
            'user_id'                              => $this->submissionDetails['user_id'],
            'status'                               => $config['status'],
            'batch_type'                           => 'batch',
            'is_complete'                          => 1,
            'table_name'                           => $this->getTableName(),
            'file_link'                            => $this->submissionDetails['file_link'],
            'description'                          => $this->submissionDetails['description'],
        ]);

        $user->notify(
            new ImportSuccessNotification(
                $this->cacheKey,
                route($config['route'], ['batch' => $this->cacheKey], true) . '#batch-submission'
            )
        );

        JobProgress::updateOrCreate(
            ['cache_key' => $this->cacheKey],
            [
                'status'   => 'completed',
                'progress' => 100,
            ]
        );
    }

    protected function handleImportFailed(ImportFailed $event): void
    {
        $exception = $event->getException();

        $errorMessage = ($exception instanceof \App\Exceptions\UserErrorException)
            ? $exception->getMessage()
            : "Something went wrong. Please try again.";

        $user = User::find($this->submissionDetails['user_id']);
        $user->notify(new ImportFailureNotification(
            $errorMessage,
            $this->submissionDetails['route'],
            $this->cacheKey
        ));

        JobProgress::updateOrCreate(
            ['cache_key' => $this->cacheKey],
            [
                'status' => 'failed',
                'error'  => $errorMessage,
            ]
        );

        $modelClass = $this->getModelClass();
        if (class_exists($modelClass)) {
            $modelClass::where('uuid', $this->cacheKey)->delete();
        }

        Submission::where('batch_no', $this->cacheKey)->delete();

        Log::error($exception->getMessage());
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
