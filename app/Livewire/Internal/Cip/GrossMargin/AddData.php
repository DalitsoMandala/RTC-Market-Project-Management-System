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
use Illuminate\Support\Facades\Log;
use App\Models\GrossMarginItemOption;
use Illuminate\Support\Facades\Route;
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
    public $itemOptions = [];
    public float $sellingPrice;
    public  $sellingPriceDesc;
    public float $sellingPriceQty = 1;
    public float $sellingPriceUnit;
    public float $yield;
    public  $yieldDesc;
    public float $yieldQty = 1;
    public float $yieldUnit;
    public float $income;
    public  $incomeDesc;
    public float $incomeQty = 1;
    public float $incomeUnit;
    public float $breakEvenYield;
    public float $breakEvenPrice;
    public float $grossMargin;
    public float $totalValuableCost;
    protected function rules()
    {
        return [
            'selectedTitle' => 'required|string|max:255',
            'newTitle' => $this->selectedTitle === 'Other' ? 'required|string|unique:gross_margins,title' : 'nullable',
            'name_of_producer' => 'required|string|max:255',
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

        ];
    }
    protected function validationAttributes()
    {
        return [

            'items.*.item' => 'Item',
            'items.*.qty' => 'Quantity',
            'items.*.unit_price' => 'Unit Price',
            'items.*.custom_item' => 'Custom Item Name',

        ];
    }

public function seasonDates(){
     $startYear = 2010;
    $currentYear = date('Y');

    for ($year = $startYear; $year <= $currentYear; $year++) {
        $nextYear = $year + 1;
        $this->seasonDates[] = "$year/$nextYear";
    }
}
    public function mount()
    {
        // Example: You can later fetch this from DB
        $this->itemOptions = GrossMarginItemOption::pluck('name')->toArray();
        $this->items[] =
            ['item' => null, 'custom_item' => null, 'description' => null, 'qty' => 1, 'unit_price' => null];
        $this->existingTitles = GrossMargin::get()->toArray();
        $this->selectedTitle = 'Other';

        $this->seasonDates();
    }


    public function clear()
    {

        $this->reset([

            'name_of_producer',
            'season',
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
        ]);

        $this->resetErrorBag();
        $this->resetValidation();

        $this->items = [[
            'item' => null,
            'custom_item' => null,
            'description' => null,
            'qty' => 1,
            'unit_price' => null
        ]];
        $this->type_of_produce = 'Seed';
        $this->season = 'Rainfed';
        $this->seasonDates();
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
            // Determine the final title
            $finalTitle = $this->selectedTitle === 'Other'
                ? trim($this->newTitle)
                : $this->selectedTitle;


                $grossMarginExists = GrossMargin::where('title', $finalTitle)->exists();
                $grossMargin = null;
                if(!$grossMarginExists){
                  $grossMargin =  GrossMargin::create([
                        'title' => $finalTitle,
                        'enterprise' => $this->enterprise
                    ]);
                }else{
                    $grossMargin = GrossMargin::where('title', $finalTitle)->first();
                }


            // Create Gross Margin Detail
            $grossMarginDetail = $grossMargin->details()->create([
                'name_of_producer' => $this->name_of_producer,
                'season' => $this->season,
                'district' => $this->district,
                'gender' => $this->gender,
                'phone_number' => $this->phone_number,
                'gps_s' => $this->gps_s,
                'gps_e' => $this->gps_e,
                'elevation' => $this->elevation,
                'type_of_produce' => $this->type_of_produce,
                'epa' => $this->epa,
                'section' => $this->section,
                'ta' => $this->ta,
                'selling_price_desc' => $this->sellingPriceDesc,
                'selling_price_qty' => $this->sellingPriceQty,
                'selling_price_unit_price' => $this->sellingPriceUnit,
                'selling_price' => $this->sellingPrice,
                'income_price_desc' => $this->incomeDesc,
                'income_price_qty' => $this->incomeQty,
                'income_price_unit_price' => $this->incomeUnit,
                'income_price' => $this->income,
                'total_valuable_costs' => $this->totalValuableCost,
                'yield' => $this->yield,
                'break_even_yield' => $this->breakEvenYield,
                'break_even_price' => $this->breakEvenPrice,
                'gross_margin' => $this->grossMargin,
                'season_dates' => $this->seasonDate,
                'user_id' => auth()->user()->id,
                'organisation_id' => auth()->user()->organisation->id,
                'uuid' => $uuid
            ]);

            // Add items
            foreach ($this->items as $item) {
                $itemName = $item['item'];

                if ($itemName === 'Other') {


                    // Add new item option if it doesn't exist
                    GrossMarginItemOption::firstOrCreate(['name' => $item['custom_item']]);
                }

                $grossMarginDetail->items()->create([
                    'item_name' => $itemName === 'Other' ? $item['custom_item'] : $itemName,
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['qty'] * $item['unit_price'],
                ]);
            }

            // Refresh titles and reset inputs
            $this->existingTitles = GrossMargin::get()->toArray();
            $this->newTitle = null;

            $this->dispatch('set-new-title', title: $finalTitle, titles: $this->existingTitles);
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
