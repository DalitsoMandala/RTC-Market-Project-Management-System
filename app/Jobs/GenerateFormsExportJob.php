<?php

namespace App\Jobs;

use App\Exports\AttendanceExport\AttendanceRegistersExport;
use App\Exports\ExportFarmer\RtcProductionFarmersMultiSheetExport;
use App\Exports\ExportProcessor\RtcProductionProcessorsMultiSheetExport;
use App\Exports\GrossMargin\GrossMarginExport;
use App\Exports\MarketingExport\MarketingDataExport;
use App\Exports\RtcRecruitment\RtcRecruitmentExport;
use App\Exports\SeedBeneficiariesExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class GenerateFormsExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        ini_set('memory_limit', '2040M');

        $files = [
            'attendance_register_template.xlsx' => new AttendanceRegistersExport(true),
            'rtc_production_marketing_farmers_template.xlsx' => new RtcProductionFarmersMultiSheetExport(true),
            'rtc_production_marketing_processors_template.xlsx' => new RtcProductionProcessorsMultiSheetExport(true),
            'recruitment_template.xlsx' => new RtcRecruitmentExport(true),
            'seed_beneficiaries_template.xlsx' => new SeedBeneficiariesExport(true),
            'gross_margin_template.xlsx' => new GrossMarginExport(),
            'marketing_template.xlsx' => new MarketingDataExport(true),
        ];

        $zipFileName = 'form_templates_' . date('d-m-Y') . '.zip';
        $zipPath = storage_path('app/public/exports/' . $zipFileName);
        $zip = new ZipArchive;

        // Ensure directory exists
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $fileName => $export) {
                $filePath = storage_path("app/temp/{$fileName}");

                // Ensure temp directory exists
                if (!file_exists(dirname($filePath))) {
                    mkdir(dirname($filePath), 0755, true);
                }

                Excel::store($export, "temp/{$fileName}");
                $zip->addFile($filePath, $fileName);
            }
            $zip->close();

            // Clean up temp files
            foreach (array_keys($files) as $fileName) {
                $tempPath = storage_path("app/temp/{$fileName}");
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
            }
        }

        // Store the file path in cache for the user
        $cacheKey = "forms_export_{$this->userId}";
        cache([$cacheKey => $zipFileName], now()->addMinutes(1));

        return $zipFileName;
    }
}
