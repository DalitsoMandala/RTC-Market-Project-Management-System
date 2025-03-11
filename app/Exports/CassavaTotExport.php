<?php

namespace App\Exports;

use App\Models\CassavaTot;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CassavaTotExport implements FromCollection, WithTitle, WithHeadings
{
    public $template = false;
    public function __construct($template = false)
    {

        $this->template = $template;
    }



    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
        return CassavaTot::select([
            'name',
            'gender',
            'age_group',
            'district',
            'epa',
            'position',
            'phone_numbers',
            'email_address'
        ]);
    }

    public function headings(): array
    {
        return [
            'Name',
            'Gender',
            'Age Group',
            'District',
            'EPA',
            'Position',
            'Phone Number',
            'Email Address'
        ];
    }

    public function title(): string
    {
        return 'Cassava Tots';
    }
}