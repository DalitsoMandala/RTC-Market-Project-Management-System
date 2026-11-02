<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use ZipArchive;
use Throwable;

trait DownloadImportTrait
{
    public string $location = 'imports';

    public string $batchType = 'batch';



    public array $checkboxValues = [];

    public bool $checkboxAll = false;

    /**
     * Set the import storage directory.
     */
    public function setLocation(string $location): void
    {
        $location = trim($location, '/\\');

        if (
            $location === '' ||
            str_contains($location, '..') ||
            str_contains($location, ':')
        ) {
            throw new \InvalidArgumentException('Invalid storage location.');
        }

        $this->location = $location;
    }

    /**
     * Set the batch type.
     */
    public function setBatchType(string $type): void
    {
        $this->batchType = $type;
    }

    /**
     * Resolve a safe relative file path on the public disk.
     */
    protected function importFilePath($fileLink): ?string
    {
        if (!is_string($fileLink) || trim($fileLink) === '') {
            return null;
        }

        $fileLink = str_replace('\\', '/', trim($fileLink));

        if (
            str_starts_with($fileLink, '/') ||
            preg_match('/^[a-zA-Z]:/', $fileLink) ||
            in_array('..', explode('/', $fileLink), true)
        ) {
            return null;
        }

        return trim($this->location, '/') . '/' . ltrim($fileLink, '/');
    }

    /**
     * Download an individual submission.
     */
    public function download($id)
    {
        $submission = Submission::find($id);

        if (!$submission) {
            $this->alert('error', 'Submission not found.', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false,
            ]);

            return;
        }

        $disk = Storage::disk('public');

        $filePath = $this->importFilePath($submission->file_link);

        if (!$filePath || !$disk->exists($filePath)) {
            $this->alert('error', 'File not found.', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false,
            ]);

            return;
        }

        $this->clearChecks();

        return response()->download(
            $disk->path($filePath),
            basename($submission->file_link)
        );
    }

    /**
     * PowerGrid bulk download button.
     */
   public function header(): array
{
    return [
        Button::add('bulk-download')
            ->slot("
                <span>
                    <i class='bx bx-download'></i>
                    Download files (
                    <span x-text=\"window.pgBulkActions.count('{$this->tableName}')\"></span>
                    )
                </span>
            ")
            ->class('btn btn-light btn-md bulk-download-button')
            ->dispatch('bulkDownload.' . $this->tableName, []),
    ];
}

    /**
     * Download multiple submissions as a ZIP archive.
     */
    public function downloadZip($ids)
    {
        $zip = null;
        $zipPath = null;
        $disk = Storage::disk('public');

        try {
            // Ensure PHP ZIP extension is installed.
            if (!class_exists(ZipArchive::class)) {
                throw new \RuntimeException('PHP ZIP extension is not installed.');
            }

            // Normalize selected IDs.
            $ids = collect($ids)
                ->filter(fn ($id) => is_numeric($id))
                ->unique()
                ->values();

            if ($ids->isEmpty()) {
                $this->alert('error', 'No submissions selected.', [
                    'position' => 'center',
                    'timer' => 5000,
                    'toast' => false,
                ]);

                return;
            }

            // Retrieve selected submissions.
            $submissions = Submission::whereIn('id', $ids)->get();

            if ($submissions->isEmpty()) {
                $this->alert('error', 'No submissions found.', [
                    'position' => 'center',
                    'timer' => 5000,
                    'toast' => false,
                ]);

                return;
            }

            // Generate a unique ZIP filename.
            $zipName = 'submissions_' .
                now()->format('Y-m-d_H-i-s_u') .
                '_' . uniqid() . '.zip';

            // Store ZIP in storage/app/public/temp.
            $zipPath = 'temp/' . $zipName;

            // Create temporary directory if necessary.
            $disk->makeDirectory('temp');

            // Resolve absolute filesystem path.
            $fullPath = $disk->path($zipPath);

            // Initialize ZIP archive.
            $zip = new ZipArchive();

            $result = $zip->open(
                $fullPath,
                ZipArchive::CREATE | ZipArchive::OVERWRITE
            );

            if ($result !== true) {
                throw new \RuntimeException('Unable to create ZIP archive. Error code: ' . $result);
            }

            // Retrieve users in one query.
            $users = User::whereIn(
                'id',
                $submissions->pluck('user_id')->filter()->unique()
            )->pluck('name', 'id');

            $addedFiles = 0;

            // Add submission files to ZIP.
            foreach ($submissions as $submission) {
                $filePath = $this->importFilePath($submission->file_link);

                // Skip missing or invalid files.
                if (!$filePath || !$disk->exists($filePath)) {
                    continue;
                }

                $extension = pathinfo(
                    $submission->file_link,
                    PATHINFO_EXTENSION
                );

                $userName = $users->get(
                    $submission->user_id,
                    'Unknown_User'
                );

                // Sanitize ZIP filename components.
                $safeTable = preg_replace(
                    '/[^A-Za-z0-9_-]/',
                    '_',
                    $submission->table_name ?? 'table'
                );

                $safeBatch = preg_replace(
                    '/[^A-Za-z0-9_-]/',
                    '_',
                    $submission->batch_no ?? 'batch'
                );

                $safeUser = preg_replace(
                    '/[^A-Za-z0-9_-]/',
                    '_',
                    $userName
                );

                // Generate unique filename.
                $newFilename = "file_{$safeTable}_{$safeBatch}_{$safeUser}_{$submission->id}";

                if ($extension !== '') {
                    $newFilename .= '.' . strtolower($extension);
                }

                // Add physical file to ZIP archive.
                if ($zip->addFile(
                    $disk->path($filePath),
                    $newFilename
                )) {
                    $addedFiles++;
                }
            }

            // Prevent downloading empty ZIP archives.
            if ($addedFiles === 0) {
                $zip->close();
                $zip = null;

                throw new \RuntimeException('No existing files were available for download.');
            }

            // Finalize ZIP archive.
            if (!$zip->close()) {
                $zip = null;

                throw new \RuntimeException('Failed to finalize ZIP archive.');
            }

            $zip = null;

            // Clear selected PowerGrid rows.
            $this->clearChecks();

            // Stream ZIP to browser and delete after sending.
            return response()
                ->download($fullPath, $zipName)
                ->deleteFileAfterSend(true);

        } catch (Throwable $e) {
            // Close archive if still open.
            if ($zip instanceof ZipArchive) {
                $zip->close();
            }

            // Remove incomplete ZIP archive.
            if ($zipPath && $disk->exists($zipPath)) {
                $disk->delete($zipPath);
            }

            // Log technical details.
            Log::error('Submission ZIP download failed.', [
                'error' => $e->getMessage(),
                'location' => $this->location,
                'ids' => $ids ?? [],
            ]);

            // Show a user-friendly error.
            $this->alert('error', 'Unable to prepare the download. Please try again.', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false,
            ]);

            return;
        }
    }

    /**
     * Handle bulk download event.
     */
    #[On('bulkDownload.{tableName}')]
    public function bulkDownload()
    {
        $ids = collect($this->checkboxValues ?? [])
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            $this->alert('warning', 'Please select at least one submission.', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false,
            ]);

            return;
        }

        // Multiple submissions: download ZIP.
        if ($ids->count() > 1) {
            return $this->downloadZip($ids);
        }

        // Single submission: download original file.
        return $this->download($ids->first());
    }

    /**
     * Clear selected PowerGrid rows.
     */
    #[On('clearChecks.{tableName}')]
    public function clearChecks(): void
    {
        $this->js('window.pgBulkActions.clearAll()');

        $this->checkboxValues = [];
        $this->checkboxAll = false;
    }
}
