<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CropVariety;
use App\Models\GrossMargin;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\GrossMarginVariety;
use Illuminate\Support\Facades\DB;
use App\Models\GrossMarginCategory;
use App\Models\GrossMarginItemValue;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Dashboard3Charts extends Component
{
    use LivewireAlert;
    public $data = [];
    public $showContent = false;
    public $name = null;
    public $financialYears = [];
    public $selectedReportYear = 'All';
    public $grossData = [];
    public $crops = [];
    public $seasons = [];
    public $districts = [];
    public $genders = [];
    public $varieties = [];
    public $typeOfProduces = [];
    public $epas = [];
    public $sections = [];
    public $selectedCrop = null;
    public $selectedSeason = null;
    public $selectedDistrict = null;
    public $selectedTypeOfProduce = null;
    public $selectedEPA = null;
    public $selectedSection = null;
    public $selectedSex = null;
    public $grossMarginData = [];
    public $grossMarginCalculations = [
        'total_valuable_cost' => 0,
        'total_harvest' => 0,
        'prevailing_selling_price' => 0,
        'income' => 0,
        'yield' => 0,
        'break_even_yield' => 0,
        'break_even_price' => 0,
        'gross_margin' => 0,
        'unit_price_total' => 0

    ];

      public $farmingCostCalculations = [
        'total_valuable_cost' => 0,
        'total_harvest' => 0,
        'prevailing_selling_price' => 0,
        'income' => 0,
        'yield' => 0,
        'break_even_yield' => 0,
        'break_even_price' => 0,
        'gross_margin' => 0,
        'unit_price_total' => 0

    ];
    public $grossCategories = [];
    public $grossMarginVarieties =  [];
    public $farmingCostsArray = [];
    public $farmingCostData = [];
    public $farmingCostVarieties = [];

    #[On('showCharts3')]
    public function showVisuals()
    {

        $this->showContent = true;
    }

public function load(){
     $this->grossCategories = GrossMarginCategory::with('grossItems')
            ->get()
            ->toArray();

        $this->varieties = CropVariety::whereNot('name', 'other')
            ->pluck('name')
            ->toArray();

        $this->crops = GrossMargin::distinct('enterprise')
            ->pluck('enterprise')
            ->toArray();

        $this->seasons = GrossMargin::distinct('season')
            ->pluck('season')
            ->toArray();

        $this->districts = GrossMargin::distinct('district')
            ->pluck('district')
            ->toArray();

        $this->genders = GrossMargin::distinct('sex')
            ->pluck('sex')
            ->toArray();


        $this->typeOfProduces = GrossMargin::distinct('type_of_produce')
            ->pluck('type_of_produce')
            ->toArray();

        $this->epas = GrossMargin::distinct('epa')
            ->pluck('epa')
            ->toArray();

        $this->sections = GrossMargin::distinct('section')
            ->pluck('section')
            ->toArray();

        $this->grossMarginData      = $this->loadGrossMargins();
        $this->grossMarginVarieties = $this->loadVarieties();
        $this->farmingCostVarieties = $this->loadFarmVarieties();
        $this->farmingCostData = $this->loadUnitPrices();
}

    public function mount()
    {


       $this->load();
    }

    public function loadGrossMargins()
    {
        $data = GrossMarginItemValue::select([
            'gross_margin_category_items.item_name',
            'gross_margin_category_items.unit',
            'gross_margin_categories.name as category',
            'gross_margin_categories.id as category_id',
            DB::raw('ROUND(AVG(gross_margin_item_values.qty), 2) as avg_qty'),
            DB::raw('ROUND(AVG(gross_margins.total_harvest), 2) as total_harvest'),
            DB::raw('ROUND(AVG(gross_margins.selling_price), 2) as prevailing_selling_price'),
            DB::raw('ROUND(AVG(gross_margins.income), 2) as income'),
            DB::raw('ROUND(AVG(gross_margin_item_values.unit_price), 2) as avg_unit_price'),
            DB::raw('ROUND((AVG(gross_margin_item_values.qty) * AVG(gross_margin_item_values.unit_price)), 2) as avg_total'),
            DB::raw('COUNT(*) as entry_count'),
        ])
            ->join('gross_margins', 'gross_margin_item_values.gross_margin_id', '=', 'gross_margins.id')
            ->join('gross_margin_category_items', 'gross_margin_item_values.gross_margin_category_item_id', '=', 'gross_margin_category_items.id')
            ->join('gross_margin_categories', 'gross_margin_category_items.gross_margin_category_id', '=', 'gross_margin_categories.id')
            // ✅ Apply filters dynamically
            ->when($this->selectedCrop, fn($q) => $q->where('gross_margins.enterprise', $this->selectedCrop))
            ->when($this->selectedSeason, fn($q) => $q->where('gross_margins.season', $this->selectedSeason))
            ->when($this->selectedDistrict, fn($q) => $q->where('gross_margins.district', $this->selectedDistrict))
            ->when($this->selectedTypeOfProduce, fn($q) => $q->where('gross_margins.type_of_produce', $this->selectedTypeOfProduce))
            ->when($this->selectedEPA, fn($q) => $q->where('gross_margins.epa', $this->selectedEPA))
            ->when($this->selectedSection, fn($q) => $q->where('gross_margins.section', $this->selectedSection))
            ->when($this->selectedSex, fn($q) => $q->where('gross_margins.sex', $this->selectedSex))

            ->groupBy(
                'gross_margin_category_items.item_name',
                'gross_margin_category_items.unit',
                'gross_margin_categories.name',
                'gross_margin_categories.id'
            )
            ->orderBy('gross_margin_categories.id', 'ASC')
            ->get();

        // Aggregate calculations
        $this->grossMarginCalculations = [
            'total_valuable_cost'      => round($data->sum('avg_total'), 2),
            'total_harvest'            => round($data->sum('total_harvest'), 2),
            'prevailing_selling_price' => round($data->sum('prevailing_selling_price'), 2),
            'income'                   => round($data->sum('income'), 2),
            'unit_price_total'         => round($data->sum('avg_unit_price'), 2),
        ];

        // Derived calculations
        $this->grossMarginCalculations['yield'] = $this->grossMarginCalculations['income'] > 0
            ? round(($this->grossMarginCalculations['total_valuable_cost'] / $this->grossMarginCalculations['income']) * 100, 2)
            : 0;

        $this->grossMarginCalculations['break_even_yield'] = $this->grossMarginCalculations['prevailing_selling_price'] > 0
            ? round(($this->grossMarginCalculations['total_valuable_cost'] / $this->grossMarginCalculations['prevailing_selling_price']) * 100, 2)
            : 0;

        $this->grossMarginCalculations['break_even_price'] = $this->grossMarginCalculations['yield'] > 0
            ? round($this->grossMarginCalculations['total_valuable_cost'] / $this->grossMarginCalculations['yield'], 2)
            : 0;

        $this->grossMarginCalculations['gross_margin'] =
            round($this->grossMarginCalculations['income'] - $this->grossMarginCalculations['total_valuable_cost'], 2);

        return $data->toArray();
    }


     public function loadUnitPrices()
    {
        $data = GrossMarginItemValue::select([
            'gross_margin_category_items.item_name',
            'gross_margin_category_items.unit',
            'gross_margin_categories.name as category',
            'gross_margin_categories.id as category_id',
            DB::raw('ROUND(AVG(gross_margin_item_values.qty), 2) as avg_qty'),
            DB::raw('ROUND(AVG(gross_margins.total_harvest), 2) as total_harvest'),
            DB::raw('ROUND(AVG(gross_margins.selling_price), 2) as prevailing_selling_price'),
            DB::raw('ROUND(AVG(gross_margins.income), 2) as income'),
            DB::raw('ROUND(AVG(gross_margin_item_values.unit_price), 2) as avg_unit_price'),
            DB::raw('ROUND((AVG(gross_margin_item_values.qty) * AVG(gross_margin_item_values.unit_price)), 2) as avg_total'),
            DB::raw('COUNT(*) as entry_count'),
        ])
            ->join('gross_margins', 'gross_margin_item_values.gross_margin_id', '=', 'gross_margins.id')
            ->join('gross_margin_category_items', 'gross_margin_item_values.gross_margin_category_item_id', '=', 'gross_margin_category_items.id')
            ->join('gross_margin_categories', 'gross_margin_category_items.gross_margin_category_id', '=', 'gross_margin_categories.id')
            // ✅ Apply filters dynamically
            // ->when($this->selectedCrop, fn($q) => $q->where('gross_margins.enterprise', $this->selectedCrop))
            // ->when($this->selectedSeason, fn($q) => $q->where('gross_margins.season', $this->selectedSeason))
            // ->when($this->selectedDistrict, fn($q) => $q->where('gross_margins.district', $this->selectedDistrict))
            // ->when($this->selectedTypeOfProduce, fn($q) => $q->where('gross_margins.type_of_produce', $this->selectedTypeOfProduce))
            // ->when($this->selectedEPA, fn($q) => $q->where('gross_margins.epa', $this->selectedEPA))
            // ->when($this->selectedSection, fn($q) => $q->where('gross_margins.section', $this->selectedSection))
            // ->when($this->selectedSex, fn($q) => $q->where('gross_margins.sex', $this->selectedSex))

            ->groupBy(
                'gross_margin_category_items.item_name',
                'gross_margin_category_items.unit',
                'gross_margin_categories.name',
                'gross_margin_categories.id'
            )
            ->orderBy('gross_margin_categories.id', 'ASC')
            ->get();

        // Aggregate calculations
        $this->farmingCostCalculations = [

            'unit_price_total'         => round($data->sum('avg_unit_price'), 2),
        ];


        return $data->toArray();
    }
    public function loadVarieties()
    {
        $var = GrossMarginVariety::join('gross_margins', 'gross_margin_varieties.gross_margin_id', '=', 'gross_margins.id')
            ->join('crop_varieties', 'crop_varieties.name', '=', 'gross_margin_varieties.variety')
            ->when($this->selectedCrop, fn($q) => $q->where('gross_margins.enterprise', $this->selectedCrop))
            ->when($this->selectedSeason, fn($q) => $q->where('gross_margins.season', $this->selectedSeason))
            ->when($this->selectedDistrict, fn($q) => $q->where('gross_margins.district', $this->selectedDistrict))
            ->when($this->selectedTypeOfProduce, fn($q) => $q->where('gross_margins.type_of_produce', $this->selectedTypeOfProduce))
            ->when($this->selectedEPA, fn($q) => $q->where('gross_margins.epa', $this->selectedEPA))
            ->when($this->selectedSection, fn($q) => $q->where('gross_margins.section', $this->selectedSection))
            ->when($this->selectedSex, fn($q) => $q->where('gross_margins.sex', $this->selectedSex))

            ->select([
                'gross_margin_varieties.variety',
                'gross_margin_varieties.unit',
                DB::raw('ROUND(AVG(gross_margin_varieties.unit_price), 2) as avg_unit_price'),
                DB::raw('ROUND(AVG(gross_margin_varieties.qty), 2) as avg_qty'),
                DB::raw('ROUND((AVG(gross_margin_varieties.qty) * AVG(gross_margin_varieties.unit_price)), 2) as avg_total'),
                DB::raw('COUNT(*) as entry_count'),
            ])

            ->groupBy(
                'gross_margin_varieties.variety',
                'gross_margin_varieties.unit',
                'gross_margin_varieties.unit_price'
            )
            ->get();

        $this->grossMarginCalculations['unit_price_total'] += $var->sum('avg_unit_price');

        return $var->toArray();
    }

    public function loadFarmVarieties()
    {
        $var = GrossMarginVariety::join('gross_margins', 'gross_margin_varieties.gross_margin_id', '=', 'gross_margins.id')
            ->join('crop_varieties', 'crop_varieties.name', '=', 'gross_margin_varieties.variety')
            // ->when($this->selectedCrop, fn($q) => $q->where('gross_margins.enterprise', $this->selectedCrop))
            // ->when($this->selectedSeason, fn($q) => $q->where('gross_margins.season', $this->selectedSeason))
            // ->when($this->selectedDistrict, fn($q) => $q->where('gross_margins.district', $this->selectedDistrict))
            // ->when($this->selectedTypeOfProduce, fn($q) => $q->where('gross_margins.type_of_produce', $this->selectedTypeOfProduce))
            // ->when($this->selectedEPA, fn($q) => $q->where('gross_margins.epa', $this->selectedEPA))
            // ->when($this->selectedSection, fn($q) => $q->where('gross_margins.section', $this->selectedSection))
            // ->when($this->selectedSex, fn($q) => $q->where('gross_margins.sex', $this->selectedSex))

            ->select([
                'gross_margin_varieties.variety',
                'gross_margin_varieties.unit',
                DB::raw('ROUND(AVG(gross_margin_varieties.unit_price), 2) as avg_unit_price'),
                DB::raw('ROUND(AVG(gross_margin_varieties.qty), 2) as avg_qty'),
                DB::raw('ROUND((AVG(gross_margin_varieties.qty) * AVG(gross_margin_varieties.unit_price)), 2) as avg_total'),
                DB::raw('COUNT(*) as entry_count'),
            ])

            ->groupBy(
                'gross_margin_varieties.variety',
                'gross_margin_varieties.unit',
                'gross_margin_varieties.unit_price'
            )
            ->get();

        $this->farmingCostCalculations['unit_price_total'] += $var->sum('avg_unit_price');
        return $var->toArray();
    }



    #[On('updateReport')]
    public function sendData($crop, $district, $season, $typeOfProduce, $epa, $section, $gender)
    {
        $this->showContent = false;
        $this->selectedCrop = $crop;
        $this->selectedSeason = $season;
        $this->selectedDistrict = $district;

        $this->selectedTypeOfProduce = $typeOfProduce;
        $this->selectedEPA = $epa;
        $this->selectedSection = $section;
        $this->selectedSex = $gender;

        $this->grossMarginData      = $this->loadGrossMargins();
        $this->grossMarginVarieties = $this->loadVarieties();
    }

    public function refreshData($data)
    {

        $this->dispatch('update-chart', data: $data);
    }
    private function distinctValues($column)
    {
        return GrossMargin::query()
            ->select($column)
            ->distinct()
            ->pluck($column);
    }



    #[On('resetReport')]
    public function resetFilters()
    {
        $this->showContent = false;
        $this->selectedCrop = null;
        $this->selectedSeason = null;
        $this->selectedDistrict = null;

        $this->selectedTypeOfProduce = null;
        $this->selectedEPA = null;
        $this->selectedSection = null;
        $this->selectedSex = null;
        $this->load();

    }



    private function builder(): Builder
    {
        return GrossMargin::query()->with([
            'items',
            'varieties',
            'items.categoryItem'
        ])->select([
            'id',
            'name',
            'district',
            'enterprise',
            'season',
            'total_variable_cost',
            'total_harvest',
            'selling_price',
            'income',
            'yield',
            'break_even_yield',
            'break_even_price',
            'gross_margin',
        ]);
    }



    public function render()
    {
        return view('livewire.dashboard3-charts');
    }
}
