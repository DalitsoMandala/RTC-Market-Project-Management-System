<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\MotherPlot;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class MotherPlotsExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{


    protected $season;
    public $template = false;

    public function __construct(string $season, $template = false)
    {
        $this->season = $season;
        $this->template = $template;
    }

    public function collection()
    {
        // If this is a template export, return an empty collection
        if ($this->template) {
            return collect([]);
        }

        // Fetch data from the MotherPlot model
        $data = MotherPlot::select(
            'district',
            'epa',
            'section',
            'village',
            'gps_s',
            'gps_e',
            'elevation',
            'season',
            'date_of_planting',
            'name_of_farmer',
            'sex',
            'nat_id_phone_number',
            'variety_received'
        )->get();

        // Transform the data (e.g., format dates)
        $data->transform(function ($row) {
            $row->date_of_planting = Carbon::parse($row->date_of_planting)->format('d-m-Y');
            return $row;
        });

        return $data;
    }

    public function headings(): array
    {
        return [
            'District',
            'EPA',
            'Section',
            'Village',
            'GPS S',
            'GPS E',
            'Elevation',
            'Season',
            'Date of Planting',
            'Name of Farmer',
            'Sex',
            'Nat ID / Phone #',
            'Variety Received'
        ];
    }

    public function title(): string
    {
        return 'Mother Plot Hosts';
    }
}
