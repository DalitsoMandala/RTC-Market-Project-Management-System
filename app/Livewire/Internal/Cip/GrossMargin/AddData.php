<?php

namespace App\Livewire\Internal\Cip\GrossMargin;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\GrossMargin;
use Livewire\Attributes\On;
use App\Models\GrossSubmission;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use App\Models\GrossMarginCategory;
use Illuminate\Support\Facades\Log;
use App\Models\GrossMarginItemValue;
use App\Models\GrossMarginItemOption;
use Illuminate\Support\Facades\Route;
use App\Models\GrossMarginCategoryItem;
use App\Models\GrossMarginVariety;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddData extends Component
{
    use LivewireAlert;



    // Dropdowns
    public $existingTitles = [];
    public $selectedTitle;
    public $newTitle = null;

    // Farmer Metadata
    public $name_of_producer = null;
    public $season = 'Rainfed';
    public $seasonDates = [];
    public $seasonDate;
    public $district = null;
    public $gender = null;
    public $phone_number = null;
    public $gps_s = null;
    public $gps_e = null;
    public $elevation = null;
    public $type_of_produce = 'Seed';
    public $epa = null;
    public $section = null;
    public $ta = null;
    public $enterprise;
    public $routePrefix = null;
    // Items table
    public $items = [];
    public $summaryItems = [];
    public $categoryOptions = [];
    public $varietyOptions = [];
    public $itemOptions = [];
    public float $sellingPrice;
    public  $sellingPriceDesc = 'Kg/bundle';
    public float $sellingPriceQty = 1;
    public float $sellingPriceUnit;
    public float $yield;
    public  $yieldDesc;
    public float $yieldQty = 1;
    public float $yieldUnit;
    public float $income;
    public  $incomeDesc = 'Kg/bundle';
    public float $incomeQty = 1;
    public float $incomeUnit;
    public float $breakEvenYield;
    public float $breakEvenPrice;
    public float $grossMargin;
    public float $totalValuableCost;
    public float $totalHarvestQty;
    public $totalHarvestDesc = 'Kg/bundle';
    public float $totalHarvest;
    public $sex = 'Male';
    public $date;
    public $village;
    protected function rules()
    {
        return [
            'name_of_producer' => 'required|string|max:255',
            'sex' => 'required|string|in:Male,Female',
            'date' => 'required|date',
            'season' => 'required|string|in:Rainfed,Irrigated',
            'enterprise' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'type_of_produce' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'gps_s' => 'nullable|string|max:255',
            'gps_e' => 'nullable|string|max:255',
            'elevation' => 'nullable|string|max:255',
            'items.*.item' => 'required|string|distinct|max:255',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.custom_item' => function ($attribute, $value, $fail) {
                $index = explode('.', $attribute)[1];
                if ($this->items[$index]['item'] === 'Other' && empty($value)) {
                    $fail('Custom item name is required when "Other" is selected.');
                }
            },

            'varietyOptions.*.variety' => 'required|string|distinct|max:255',
            'varietyOptions.*.qty' => 'required|numeric|min:0',
            'varietyOptions.*.unit_price' => 'required|numeric|min:0',

            'sellingPrice' => 'required|numeric|min:0',
            'sellingPriceDesc' => 'nullable|string|max:255',
            'sellingPriceQty' => 'required|numeric|min:0',
            'sellingPriceUnit' => 'required|numeric|min:0',
            'yield' => 'required|numeric|min:0',
            'income' => 'required|numeric|min:0',
            'incomeDesc' => 'nullable|string|max:255',
            'incomeQty' => 'required|numeric|min:0',
            'incomeUnit' => 'required|numeric|min:0',
            'breakEvenYield' => 'required|numeric',
            'breakEvenPrice' => 'required|numeric',
            'grossMargin' => 'required|numeric',
            'totalHarvestQty' => 'required|numeric',

        ];
    }
    protected function validationAttributes()
    {
        return [

            'items.*.item' => 'Item',
            'items.*.qty' => 'Quantity',
            'items.*.unit_price' => 'Unit Price',
            'items.*.custom_item' => 'Custom Item Name',

            'varietyOptions.*.variety' => 'Variety',
            'varietyOptions.*.qty' => 'Quantity',
            'varietyOptions.*.unit_price' => 'Unit Price',

            'totalHarvestQty' => 'Total Harvest Value',

        ];
    }

    public function seasonDates()
    {
        $startYear = 2010;
        $currentYear = date('Y');

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $nextYear = $year + 1;
            $this->seasonDates[] = "$year/$nextYear";
        }
    }
    public function mount()
    {
        $this->load();
    }
    public function load()
    {
        // Example: You can later fetch this from DB
        $this->categoryOptions = GrossMarginCategory::whereNot('name', 'Seed (Variety)')->pluck('name')->toArray();
        $this->itemOptions = GrossMarginCategoryItem::with('category')->get()->toArray();
        $this->varietyOptions[] = [
            'variety' => null,
            'unit' => 'Kg/bundle',
            'qty' => null,
            'unit_price' => null,

        ];
        foreach ($this->itemOptions as $option) {
            $this->items[] =
                [
                    'item' => $option['item_name'],
                    'unit' => $option['unit'],
                    'qty' => null,
                    'unit_price' => null,
                    'category' => $option['category']['name'],
                    'category_id' => $option['category']['id'],
                    'item_id' => $option['id'],

                ];
        }

        $this->existingTitles = GrossMargin::get()->toArray();
        $this->selectedTitle = 'Other';

        $this->seasonDates();
    }

    public function clear()
    {

        $this->reset([

            'name_of_producer',
            'season',
            'sex',
            'district',
            'gender',
            'phone_number',
            'gps_s',
            'gps_e',
            'elevation',
            'type_of_produce',
            'epa',
            'section',
            'ta',
            'items',
            'enterprise',
            'sellingPrice',
            'sellingPriceDesc',
            'sellingPriceQty',
            'sellingPriceUnit',
            'yield',
            'yieldDesc',
            'yieldQty',
            'yieldUnit',
            'income',
            'incomeDesc',
            'incomeQty',
            'incomeUnit',
            'breakEvenYield',
            'breakEvenPrice',
            'grossMargin',
            'totalHarvestQty',
        ]);

        $this->resetErrorBag();
        $this->resetValidation();

        $this->load();
    }
    public function addItem()
    {
        $this->items[] = [
            'item' => null,
            'custom_item' => null,
            'description' => null,
            'qty' => 1,
            'unit_price' => null
        ];
    }

    public function addVariety()
    {
        $this->varietyOptions[] = [
            'varitey' => null,
            'unit' => 'Kg/bundle',
            'qty' => null,
            'unit_price' => null,

        ];
    }

    public function redirectUsers()
    {
        $user = User::find(auth()->user()->id);

        if ($user->hasAnyRole('admin')) {

            return route('admin-gross-margin-manage-data');;
        } else if ($user->hasAnyRole('manager')) {
            return route('cip-gross-margin-manage-data');
        } else if ($user->hasAnyRole('project_manager')) {
            return route('project_manager-gross-margin-manage-data');
        }
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function removeVariety($index)
    {
        unset($this->varietyOptions[$index]);
        $this->varietyOptions = array_values($this->varietyOptions);
    }
    public function save()
    {

        $this->validate();

        DB::beginTransaction();

        try {

            $uuid = Uuid::uuid4()->toString(); // Generate a new UUID for the new information;

            GrossSubmission::create([
                'batch_type' => 'manual',
                'batch_no' => $uuid,
                'submitted_user_id' => auth()->user()->id,
                'status' => 'approved',
                'table_name' => 'gross_margin_details',
                'file_link' => null
            ]);

            $grossMargin = GrossMargin::create([
                'uuid' => $uuid,
                'name' => $this->name_of_producer,
                'sex' => $this->sex,
                'phone_number' => $this->phone_number,
                'date' => $this->date,
                'district' => $this->district,
                'ta' => $this->ta,
                'village' => $this->village,
                'epa' => $this->epa,
                'section' => $this->section,
                'gps_s' => $this->gps_s,
                'gps_e' => $this->gps_e,
                'elevation' => $this->elevation,
                'enterprise' => $this->enterprise,
                'type_of_produce' => $this->type_of_produce,
                'season' => $this->season,
                'total_variable_cost' => $this->totalValuableCost,
                'total_harvest' => $this->totalHarvest,
                'selling_price' => $this->sellingPrice,
                'income' => $this->income,
                'yield' => $this->yield,
                'break_even_yield' => $this->breakEvenYield,
                'break_even_price' => $this->breakEvenPrice,
                'gross_margin' => $this->grossMargin,
                'user_id' => auth()->user()->id,
                'organisation_id' => auth()->user()->organisation->id,
            ]);




            // add for each item
            foreach ($this->items as $item) {


                GrossMarginItemValue::create([
                    'gross_margin_id' => $grossMargin->id,
                    'gross_margin_category_item_id' => $item['item_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['qty'] * $item['unit_price'],
                ]);
            }

            //add for each variety
            foreach ($this->varietyOptions as $variety) {
                GrossMarginVariety::create([
                    'gross_margin_id' => $grossMargin->id,
                    'variety' => $variety['variety'],
                    'qty' => $variety['qty'],
                    'unit_price' => $variety['unit_price'],
                    'total' => $variety['qty'] * $variety['unit_price'],
                ]);
            }

            $this->clear();
            session()->flash('success', 'Gross Margin data saved successfully. <a href="' . $this->redirectUsers() . '">View Submission here</a>');

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gross Margin Save Error: ', ['error' => $e]);
            session()->flash('error', 'An error occurred while saving. Please try again.');
            throw $e; // Optional: You can remove this if you don't want to bubble the error
        }
    }





    public function render()
    {

        $this->routePrefix = Route::current()->getPrefix();
        return view('livewire.internal.cip.gross-margin.add-data');
    }
}
