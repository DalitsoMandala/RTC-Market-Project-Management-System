<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionFarmers;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddFollowUp extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $variable;
    public $rowId;

    public function setData($id)
    {
        $this->resetErrorBag();

    }

    public function mount()
    {


        // if (isset($this->f_market_segment['fresh'])) {
        //     $this->f_market_segment['fresh'] = "YES";
        // } else {
        //     $this->f_market_segment['fresh'] = "NO";
        // }

        // if (isset($this->f_market_segment['processed'])) {
        //     $this->f_market_segment['processed'] = "YES";
        // } else {
        //     $this->f_market_segment['processed'] = "NO";
        // }
        // $secondTable = [
        //     'rpm_farmer_id' => $recruit->id,
        //     'location_data' => $this->location_data,
        //     'date_of_follow_up' => $this->f_date_of_follow_up,
        //     'area_under_cultivation' => $this->f_area_under_cultivation,
        //     'number_of_plantlets_produced' => $this->f_number_of_plantlets_produced,
        //     'number_of_screen_house_vines_harvested' => $this->f_number_of_screen_house_vines_harvested,
        //     'number_of_screen_house_min_tubers_harvested' => $this->f_number_of_screen_house_min_tubers_harvested,
        //     'number_of_sah_plants_produced' => $this->f_number_of_sah_plants_produced,
        //     'area_under_basic_seed_multiplication' => $this->f_area_under_basic_seed_multiplication,
        //     'area_under_certified_seed_multiplication' => $this->f_area_under_certified_seed_multiplication,
        //     'is_registered_seed_producer' => $this->f_is_registered_seed_producer,
        //     'seed_service_unit_registration_details' => $this->f_seed_service_unit_registration_details,
        //     'uses_certified_seed' => $this->f_uses_certified_seed,
        //     'market_segment' => $this->f_market_segment,
        //     'has_rtc_market_contract' => $this->f_has_rtc_market_contract,
        //     'total_vol_production_previous_season' => $this->f_total_vol_production_previous_season,
        //     'total_production_value_previous_season' => $this->f_total_production_value_previous_season,
        //     'total_vol_irrigation_production_previous_season' => $this->f_total_vol_irrigation_production_previous_season,
        //     'total_irrigation_production_value_previous_season' => $this->f_total_irrigation_production_value_previous_season,
        //     'sells_to_domestic_markets' => $this->f_sells_to_domestic_markets,
        //     'sells_to_international_markets' => $this->f_sells_to_international_markets,
        //     'uses_market_information_systems' => $this->f_uses_market_information_systems,
        //     'market_information_systems' => $this->f_market_information_systems,
        //     'aggregation_centers' => $this->f_aggregation_centers,
        //     'aggregation_center_sales' => $this->f_aggregation_center_sales,

        // ];

        // foreach ($secondTable as $key => $value) {
        //     if (is_array($value)) {
        //         $secondTable[$key] = json_encode($value);
        //     }
        // }

        // RpmFarmerFollowUp::create($secondTable);
    }


    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-production-farmers.add-follow-up');
    }
}
