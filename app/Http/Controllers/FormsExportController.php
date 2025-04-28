<?php

namespace App\Http\Controllers;

use ZipArchive;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SeedBeneficiariesExport;
use App\Exports\RtcConsumption\RtcConsumptionExport;
use App\Exports\AttendanceExport\AttendanceRegistersExport;
use App\Exports\ExportFarmer\RtcProductionFarmersMultiSheetExport;
use App\Exports\ExportProcessor\RtcProductionProcessorsMultiSheetExport;

class FormsExportController extends Controller
{
    //

    public function export()
    {
        ini_set('memory_limit', '1024M');
        $files = [
            'attendance_register_template.xlsx' => new AttendanceRegistersExport(true),
            'rtc_production_marketing_farmers_template.xlsx' => new RtcProductionFarmersMultiSheetExport(true),
            'rtc_production_marketing_processors_template.xlsx' => new RtcProductionProcessorsMultiSheetExport(true),
            'rtc_consumption_template.xlsx' => new RtcConsumptionExport(true),
            'seed_beneficiaries_template.xlsx' => new SeedBeneficiariesExport(true),
        ];

        $zipFileName = 'form_templates.zip';
        $zipPath = storage_path('app/public/exports/' . $zipFileName);
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $fileName => $export) {
                $filePath = storage_path("app/temp/{$fileName}");
                Excel::store($export, "temp/{$fileName}");
                $zip->addFile($filePath, $fileName);
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}