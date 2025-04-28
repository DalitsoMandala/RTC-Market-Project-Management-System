<?php

namespace App\Jobs;

use ZipArchive;
use Illuminate\Bus\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Exports\SeedBeneficiariesExport;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Exports\RtcConsumption\RtcConsumptionExport;
use App\Exports\AttendanceExport\AttendanceRegistersExport;
use App\Exports\ExportFarmer\RtcProductionFarmersMultiSheetExport;
use App\Exports\ExportProcessor\RtcProductionProcessorsMultiSheetExport;
use Illuminate\Bus\Batchable;

class GenerateFormTemplatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    public $path;
    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId; // optional: if you want to personalize the file name
    }

    public function handle()
    {
        ini_set('memory_limit', '1024M');
        $files = [
            'attendance_register_template.xlsx' => new AttendanceRegistersExport(true),
            'rtc_production_marketing_farmers_template.xlsx' => new RtcProductionFarmersMultiSheetExport(true),
            'rtc_production_marketing_processors_template.xlsx' => new RtcProductionProcessorsMultiSheetExport(true),
            'rtc_consumption_template.xlsx' => new RtcConsumptionExport(true),
            'seed_beneficiaries_template.xlsx' => new SeedBeneficiariesExport(true),
        ];

        $zipFileName = "form_templates_user_{$this->userId}.zip";
        $zipPath = storage_path('app/public/exports/' . $zipFileName);

        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $fileName => $export) {
                $filePath = storage_path("app/temp/{$fileName}");
                Excel::store($export, "temp/{$fileName}");
                $zip->addFile($filePath, $fileName);
            }
            $zip->close();
        }

        // clean up temp files
        foreach (array_keys($files) as $fileName) {
            Storage::delete("temp/{$fileName}");
        }
    }
}
