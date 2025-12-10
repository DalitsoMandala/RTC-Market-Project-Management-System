<?php

namespace App\Exports\Reports;

use App\Models\Crop;
use App\Models\User;
use App\Models\Organisation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportSheet implements WithMultipleSheets

{


    public array $sheets;
    public  $user;
    public function __construct($user = null)
    {


        $this->user = User::find($user?->id);

        if ($this->user === null) {
            Log::error('User not found');
            throw new \Exception('User not found');
            return;
        }
    }


    public function getSheetClasses(): array
    {
        if (!isset($this->user)) {
            Log::error('User not found');
            throw new \Exception('User not found');
        }
        return [
            'Consolidated' => new ReportExport('Consolidated'),
            'Sweet potato' => new ReportExport('Sweet potato'),
            'Potato' => new ReportExport('Potato'),
            'CIP' => new ReportExport('CIP'),
            'IITA' => new ReportExport('IITA'),
            'ACE' => new ReportExport('ACE'),
            'DCD' => new ReportExport('DCD'),
            'DAES' => new ReportExport('DAES'),
            'MINISTRY OF TRADE' => new ReportExport('MINISTRY OF TRADE'),
            'TRADELINE' => new ReportExport('TRADELINE'),
            'DARS' => new ReportExport('DARS'),
            'RTCDT' => new ReportExport('RTCDT'),
            'LUANAR' => new ReportExport('LUANAR'),


        ];
    }
    public function sheets(): array
    {
        if ($this->user && $this->user->hasAnyRole('external')) {
            $organisation = $this->user->organisation;

            return [$this->getSheetClasses()[$organisation->name]];
        }

        return $this->getSheetClasses();
    }
}
