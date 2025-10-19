<?php

namespace App\Traits;

use App\Models\Submission;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

trait DownloadImportTrait
{
    //
    public $location = 'imports';
public $batchType = 'batch';

    public function batchType($type)
    {
        $this->batchType = $type;

    }
    public function download($id)
    {
        $submission = Submission::find($id);


        if (!Storage::exists('public/' . $this->location . '/' . $submission->file_link)) {
            $this->alert('error', 'File not found', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false
            ]);

            return;
        }
        $this->clearChecks();

        // Return the public URL for browser download
        return redirect(Storage::url('public/' . $this->location . '/' . $submission->file_link));
    }

    public function header(): array
    {


        return [
            \PowerComponents\LivewirePowerGrid\Button::add('bulk-download')

                ->bladeComponent('bulk-button', [
                    'tableName' => $this->tableName
                ])

        ];
    }
    public function downloadZip($ids)
    {

        $submissions = Submission::with(['user.organisation', 'period.financialYears'])->whereIn('id', $ids)->get();

        if ($submissions->isEmpty()) {
            $this->alert('error', 'No submissions found', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false
            ]);
            return;
        }

        $zipName = 'submissions_' . now()->format('Y-m-d_H-i-s') . '.zip';
        $zipPath = 'public/temp/' . $zipName;

        // Create temp directory if it doesn't exist
        if (!Storage::exists('public/temp')) {
            Storage::makeDirectory('public/temp');
        }

        $zip = new \ZipArchive();
        $fullPath = Storage::path($zipPath);

        if ($zip->open($fullPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($submissions as $submission) {
                $filePath = 'public/' . $this->location . '/' . $submission->file_link;
                if (Storage::exists($filePath)) {
                    // Use addFile for better performance

                    // Get file extension
                    $extension = pathinfo($submission->file_link, PATHINFO_EXTENSION);

                    // Create new filename
                    $newFilename = "file_{$submission->table_name}_{$submission->user->organisation->name}_year{$submission->period->financialYears->number}.{$extension}";
                    $zip->addFile(Storage::path($filePath), $newFilename);
                }
            }
            $zip->close();

            // Return redirect to the temporary ZIP file URL
            $this->clearChecks();
            return redirect()->to(Storage::url($zipPath));
        } else {
            $this->alert('error', 'Failed to create zip file', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false
            ]);
        }
    }

    #[\Livewire\Attributes\On('bulkDownload.{tableName}')]
    public function bulkDownload(): void
    {

        if ($this->checkboxValues) {
            if (count($this->checkboxValues) > 1) {
                $this->downloadZip($this->checkboxValues);
                return;
            }
            foreach ($this->checkboxValues as $id) {
                $this->download($id);
            }
        }
    }


    public function setLocation($location)
    {
        $this->location = $location;
    }
    #[\Livewire\Attributes\On('clearChecks.{tableName}')]
    public function clearChecks(): void
    {
        $this->js('window.pgBulkActions.clearAll()');
        $this->checkboxValues = [];
        $this->checkboxAll = false;
    }
}
