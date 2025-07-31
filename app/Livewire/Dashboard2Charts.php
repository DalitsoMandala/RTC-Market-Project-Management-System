<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\MarketDataReport;
use Livewire\Attributes\Validate;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Dashboard2Charts extends Component
{
    use LivewireAlert;
    public $data = [];
    public $showContent = false;
    public $name = null;
    public $financialYears = [];
    public $selectedReportYear = 'All';
    public $marketData = [];


    #[On('showCharts2')]
    public function showVisuals()
    {

        $this->showContent = true;
    }

    public function save() {}

    public function mount()
    {
        $years = $this->builder()->distinct('date')->pluck('date');
        $formatYears = [];
        foreach ($years as $key => $year) {
            $formatYears[] = [
                'id' => $key + 1,
                'number' => $year
            ];
        }

        $this->financialYears = count($formatYears) > 0 ? $formatYears : [
            [
                'id' => 1,
                'number' => 'All'
            ]
        ];
        $this->selectedReportYear = 'All';

        $data = $this->load();


        $this->marketData = $this->filterByDate($data, 'All');

    }


    #[On('updateReportYear2')]
    public function sendData($year)
    {
         $this->showContent = false;
        $this->selectedReportYear = $year;
        $data = $this->load();
        $this->marketData = $this->filterByDate($data, $year);
$this->refreshData();

    }

public function refreshData(){

    $this->dispatch('update-chart',data: $this->marketData);
}
    public function filterByDate($array, $year)
    {


        return collect($array)->get($year, []);
    }

    private function load()
    {
        return $this->builder()
            ->select(['id', 'name', 'date', 'data'])
            ->get()->map(function ($item) {
                return [
                    'name' => $item->name,
                    'date' => $item->date,
                    'data' => json_decode($item->data,true)
                ];
            })
            ->groupBy('date')
            ->toArray();
    }

    private function builder(): Builder
    {
        return MarketDataReport::query();
    }


    public function render()
    {
        return view('livewire.dashboard2-charts');
    }
}
