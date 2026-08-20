<?php
namespace App\Livewire\Forms\RtcMarket\ProductionMarketingLog;

use App\Models\Form;
use App\Models\ProductionMarketingLog;
use App\Traits\ManualDataTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Throwable;

class Edit extends Component
{

    use LivewireAlert;
    use ManualDataTrait;
    public $recordId;
    public $uuid;
    public $form_name;
    // Location / identification
    public $prod_market_id;
    public $district;
    public $epa;
    public $section;
    public $enterprise;
    public $group_name;

    // Farming information
    public $type_of_farming;
    public $season;

    // Group information
    public $group_chair_name;
    public $group_chair_contact;

    // Farmer information
    public $farmer_name;
    public $farmer_id_phone;
    public $sex;
    public $age;

    // Production information
    public $area_grown_acre;
    public $variety;
    public $harvesting_units;
    public $unit_weight_kg;
    public $qty;
    public $selling_price;
    public $main_buyer;

    public $routePrefix;

    public $uniqueId;
    protected function rules()
    {
        return [

            'district'            => 'required|string|max:255',
            'epa'                 => 'required|string|max:255',
            'section'             => 'required|string|max:255',
            'enterprise'          => 'required|string|in:Cassava,Potato,Sweet potato',
            'group_name'          => 'required|string|max:255',

            'type_of_farming'     => 'required|string|max:255',
            'season'              => 'required|string|in:Rainfed,Winter',

            'group_chair_name'    => 'nullable|string|max:255',
            'group_chair_contact' => 'nullable|string|max:255',

            'farmer_name'         => 'nullable|string|max:255',
            'farmer_id_phone'     => 'nullable|string|max:255',

            'sex'                 => 'required|string|in:Male,Female',
            'age'                 => 'nullable|integer|min:0',

            'area_grown_acre'     => 'nullable|numeric|min:0',
            'variety'             => 'required|string|max:255',

            'harvesting_units'    => 'nullable|string|max:255',
            'unit_weight_kg'      => 'nullable|numeric|min:0',
            'qty'                 => 'nullable|numeric|min:0',

            'selling_price'       => 'nullable|numeric|min:0',

            'main_buyer'          => 'nullable|string|max:255',
        ];
    }

    protected function validationAttributes()
    {
        return [

            'group_chair_name'    => 'group chair name',
            'group_chair_contact' => 'group chair contact',
            'farmer_id_phone'     => 'farmer ID / phone',
            'area_grown_acre'     => 'area grown (acres)',
            'unit_weight_kg'      => 'unit weight (kg)',
            'main_buyer'          => 'main buyer',
        ];
    }

    public function mount($id, $uuid)
    {
        $this->routePrefix = Route::current()->getPrefix();

        $record = ProductionMarketingLog::where('id', $id)
            ->where('uuid', $uuid)
            ->firstOrFail();

        $this->openSubmission = $record ? true : false;
        $this->recordId       = $record->id;
        $this->uuid           = $record->uuid;

        $this->editData($record);
    }

    public function editData(ProductionMarketingLog $data)
    {
        $this->fill([
            'uniqueId'            => $data->prod_market_id,

            'district'            => $data->district,
            'epa'                 => $data->epa,
            'section'             => $data->section,
            'enterprise'          => $data->enterprise,
            'group_name'          => $data->group_name,

            'type_of_farming'     => $data->type_of_farming,
            'season'              => $data->season,

            'group_chair_name'    => $data->group_chair_name,
            'group_chair_contact' => $data->group_chair_contact,

            'farmer_name'         => $data->farmer_name,
            'farmer_id_phone'     => $data->farmer_id_phone,
            'sex'                 => $data->sex,
            'age'                 => $data->age,

            'area_grown_acre'     => $data->area_grown_acre,
            'variety'             => strtolower(trim($data->variety)),

            'harvesting_units'    => $data->harvesting_units,
            'unit_weight_kg'      => $data->unit_weight_kg,
            'qty'                 => $data->qty,

            'selling_price'       => $data->selling_price,
            'main_buyer'          => $data->main_buyer,
        ]);
    }

    public function save()
    {
        try {
            $this->validate();
        } catch (Throwable $e) {
            $this->dispatch('show-alert', data: [
                'type'    => 'error',
                'message' => 'There are errors in the form.',
            ]);

            throw $e;
        }

        try {
            DB::beginTransaction();

            $record = ProductionMarketingLog::findOrFail($this->recordId);

            $record->update([

                'district'            => $this->district,
                'epa'                 => $this->epa,
                'section'             => $this->section,
                'enterprise'          => $this->enterprise,
                'group_name'          => $this->group_name,

                'type_of_farming'     => $this->type_of_farming,
                'season'              => $this->season,

                'group_chair_name'    => $this->group_chair_name,
                'group_chair_contact' => $this->group_chair_contact,

                'farmer_name'         => $this->farmer_name,
                'farmer_id_phone'     => $this->farmer_id_phone,
                'sex'                 => $this->sex,
                'age'                 => $this->age,

                'area_grown_acre'     => $this->area_grown_acre,
                'variety'             => strtolower(trim($this->variety)), //$this->variety,

                'harvesting_units'    => $this->harvesting_units,
                'unit_weight_kg'      => $this->unit_weight_kg,
                'qty'                 => $this->qty,

                'selling_price'       => $this->selling_price,
                'main_buyer'          => $this->main_buyer,
            ]);

            DB::commit();

            $this->dispatch('show-alert', data: [
                'type'    => 'success',
                'message' => 'Production marketing record successfully updated.',
            ]);

        } catch (Throwable $th) {

            DB::rollBack();

            Log::error('Production marketing edit failed', [
                'record_id' => $this->recordId,
                'error'     => $th->getMessage(),
            ]);

            $this->dispatch('show-alert', data: [
                'type'    => 'error',
                'message' => 'Something went wrong while updating the record.',
            ]);
        }
    }

    public function render()
    {
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }
        return view('livewire.forms.rtc-market.production-marketing-log.edit');
    }
}
