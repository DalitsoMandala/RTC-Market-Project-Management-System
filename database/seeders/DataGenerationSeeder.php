<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use App\Helpers\DistrictObject;
use Illuminate\Database\Seeder;
use App\Models\RpmFarmerFollowUp;
use Illuminate\Support\Collection;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Models\HouseholdRtcConsumption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerFollowUp;
use App\Models\AttendanceRegister;
use App\Models\SchoolRtcConsumption;

class DataGenerationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        //
        function hrc()
        {
            $faker = Faker::create();
            return [
                'epa' => $faker->city(),
                'section' => $faker->word(),
                'district' => $faker->randomElement(DistrictObject::districts()),
                'enterprise' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato']),
                'date_of_assessment' => $faker->date(),
                'actor_type' => $faker->randomElement(['Farmer', 'Trader', 'Processor', 'Individuals from nutrition interventions', 'Other']),
                'rtc_group_platform' => $faker->randomElement(['Household', 'Seed']),
                'producer_organisation' => $faker->company(),
                'actor_name' => $faker->name(),
                'age_group' => $faker->randomElement(['Youth', 'Not youth']),
                'sex' => $faker->randomElement(['Male', 'Female']),
                'phone_number' => $faker->optional()->phoneNumber(),
                'household_size' => $faker->numberBetween(1, 10),
                'under_5_in_household' => $faker->numberBetween(0, 5),
                'rtc_consumers' => $faker->numberBetween(0, 10),
                'rtc_consumers_potato' => $faker->numberBetween(0, 5),
                'rtc_consumers_sw_potato' => $faker->numberBetween(0, 5),
                'rtc_consumers_cassava' => $faker->numberBetween(0, 5),
                'rtc_consumption_frequency' => $faker->numberBetween(1, 30),
                'uuid' => $faker->uuid(),
                'user_id' => 3,
                'submission_period_id' => 1,
                'organisation_id' => 1,
                'financial_year_id' => $faker->numberBetween(1, 4),
                'period_month_id' => 1,
                'status' => $faker->randomElement(['approved']),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ];
        }


        $data = [];


        foreach (range(1, 10) as $index) {

            $data = HouseholdRtcConsumption::create(hrc());

            $faker = Faker::create();
            $data->mainFoods()->create([
                'name' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato'])
            ]);
        }


        function rpmf()
        {


            $faker = Faker::create();
            $dates = [
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ];

            $crops = ['Cassava', 'Sweet potato', 'Potato'];
            return [
                'main' => [
                    'epa' => $faker->word, // Random word for epa
                    'district' => $faker->randomElement(DistrictObject::districts()), // Random city for district
                    'section' => $faker->word, // Random word for section
                    'enterprise' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato']),
                    'date_of_recruitment' => $faker->date, // Random date
                    'name_of_actor' => $faker->name, // Random name
                    'name_of_representative' => $faker->name, // Random name
                    'phone_number' => $faker->phoneNumber, // Random phone number
                    'type' => $faker->randomElement(['Producer organization (PO)', 'Large scale farm']),

                    'approach' => $faker->optional()->randomElement([
                        'Collective production only',
                        'Collective marketing only',
                        'Knowledge sharing only',
                        'Collective production, marketing and knowledge sharing',
                        'N/A'
                    ]),

                    'sector' => $faker->randomElement(['Public', 'Private']), // Random sector
                    'group' => $faker->randomElement([
                        'Early generation seed producer',
                        'Seed multiplier',
                        'Rtc producer'
                    ]),

                    'establishment_status' => $faker->randomElement([
                        'New',
                        'Old'
                    ]),

                    'is_registered' => $faker->boolean, // Random boolean
                    'registration_body' => $faker->company, // Random company for registration body
                    'registration_number' => $faker->unique()->numerify('REG-####'), // Random unique registration number
                    'registration_date' => $faker->date, // Random registration date
                    'number_of_plantlets_produced_cassava' => $faker->numberBetween(1, 1000) * 10, // Random number for plantlets produced (cassava)
                    'number_of_plantlets_produced_potato' => $faker->numberBetween(1, 1000) * 10, // Random number for plantlets produced (potato)
                    'number_of_plantlets_produced_sweet_potato' => $faker->numberBetween(1, 1000) * 10, // Random number for plantlets produced (sweet potato)
                    'number_of_screen_house_vines_harvested' => $faker->numberBetween(1, 1000) * 10, // Random number for vines harvested
                    'number_of_screen_house_min_tubers_harvested' => $faker->numberBetween(1, 1000) * 10, // Random number for tubers harvested
                    'number_of_sah_plants_produced' => $faker->numberBetween(1, 1000) * 10, // Random number for SAH plants produced
                    'is_registered_seed_producer' => $faker->boolean, // Random boolean for registered seed producer
                    'registration_number_seed_producer' => $faker->unique()->numerify('SEED-####'), // Random seed registration number
                    'registration_date_seed_producer' => $faker->date, // Random date for seed producer registration
                    'uses_certified_seed' => $faker->boolean, // Random boolean
                    'market_segment_fresh' => $faker->boolean, // Random boolean for fresh market segment
                    'market_segment_processed' => $faker->boolean, // Random boolean for processed market segment
                    'has_rtc_market_contract' => $faker->boolean, // Random boolean for market contract
                    'total_vol_production_previous_season' => $faker->numberBetween(1, 1000) * 10, // Random volume for production (metric tonnes)
                    'prod_value_previous_season_total' => $faker->randomFloat(2, 1, 1000) * 10, // Random float for total production value
                    'prod_value_previous_season_date_of_max_sales' => $faker->date, // Random date for max sales
                    'prod_value_previous_season_usd_rate' => $faker->randomFloat(2, 0.8, 1.5), // Random USD rate
                    'prod_value_previous_season_usd_value' => $faker->randomFloat(2, 1, 1000) * 10, // Random USD value
                    'total_vol_irrigation_production_previous_season' => $faker->numberBetween(1, 1000) * 10, // Random volume for irrigation production
                    'irr_prod_value_previous_season_total' => $faker->randomFloat(2, 1, 1000) * 10, // Random total value for irrigation production
                    'irr_prod_value_previous_season_date_of_max_sales' => $faker->date, // Random date for max sales (irrigation)
                    'irr_prod_value_previous_season_usd_rate' => $faker->randomFloat(2, 0.8, 1.5), // Random USD rate for irrigation
                    'irr_prod_value_previous_season_usd_value' => $faker->randomFloat(2, 1, 1000) * 10, // Random USD value for irrigation
                    'sells_to_domestic_markets' => $faker->boolean, // Random boolean
                    'sells_to_international_markets' => $faker->boolean, // Random boolean
                    'uses_market_information_systems' => $faker->boolean, // Random boolean
                    'user_id' => 3, // Random user ID
                    'uuid' => $faker->uuid, // Random UUID
                    'submission_period_id' => 2, // Random submission period ID
                    'organisation_id' => 1, // Random organisation ID
                    'financial_year_id' => $faker->numberBetween(1, 4), // Random financial year ID
                    'period_month_id' => 1, // Random period month ID
                    'status' => 'approved', // Fixed value
                    'sells_to_aggregation_centers' => $faker->boolean, // Random boolean for selling to aggregation centers
                    'total_vol_aggregation_center_sales' => $faker->numberBetween(1, 1000) * 10, // Random volume for aggregation center sales
                    'emp_formal_female_18_35' => $faker->numberBetween(10, 1000) * 10, // Random number for formal female employees 18-35
                    'emp_formal_male_18_35' => $faker->numberBetween(10, 1000) * 10, // Random number for formal male employees 18-35
                    'emp_formal_male_35_plus' => $faker->numberBetween(10, 1000) * 10, // Random number for formal male employees 35+
                    'emp_formal_female_35_plus' => $faker->numberBetween(10, 1000) * 10, // Random number for formal female employees 35+
                    'emp_informal_female_18_35' => $faker->numberBetween(10, 1000) * 10, // Random number for informal female employees 18-35
                    'emp_informal_male_18_35' => $faker->numberBetween(10, 1000) * 10, // Random number for informal male employees 18-35
                    'emp_informal_male_35_plus' => $faker->numberBetween(10, 1000) * 10, // Random number for informal male employees 35+
                    'emp_informal_female_35_plus' => $faker->numberBetween(10, 1000) * 10, // Random number for informal female employees 35+
                    'mem_female_18_35' => $faker->numberBetween(10, 1000) * 10, // Random number for female members 18-35
                    'mem_male_18_35' => $faker->numberBetween(10, 1000) * 10, // Random number for male members 18-35
                    'mem_male_35_plus' => $faker->numberBetween(10, 1000) * 10, // Random number for male members 35+
                    'mem_female_35_plus' => $faker->numberBetween(10, 1000) * 10, // Random number for female members 35+
                ],

                'cultivation' => [
                    'variety' => $faker->word(),
                    'area' => $faker->numberBetween(10, 1000) * 10
                ],
                'area_under_basic_seed_multiplication' => [
                    'variety' => $faker->word(),
                    'area' => $faker->numberBetween(10, 1000) * 10
                ],

                'area_under_certified_seed_multiplication' => [
                    'variety' => $faker->word(),
                    'area' => $faker->numberBetween(10, 1000) * 10
                ],

                'aggregation_center_sales' => [
                    'name' => $faker->word(),

                ],

                'market_information_systems' => [
                    'name' => $faker->word(),
                ],

                'conc_aggrement' => [

                    'date_recorded' => $faker->dateTimeThisYear(), // Random date recorded this year
                    'partner_name' => $faker->company, // Random company name
                    'country' => $faker->country, // Random country
                    'date_of_maximum_sale' => $faker->date(), // Random date of maximum sale
                    'product_type' => $faker->randomElement([
                        'Seed',
                        'Ware',
                        'Value added products'
                    ]),

                    'volume_sold_previous_period' => $faker->numberBetween(1, 1000) * 10, // Random volume sold
                    'financial_value_of_sales' => $faker->randomFloat(2, 1, 1000) * 10, // Random financial value of sales
                    ...$dates
                ],

                'domestic' => [

                    'date_recorded' => $faker->dateTimeThisYear(), // Random date recorded this year
                    'crop_type' => $faker->randomElement($crops), // Random crop type
                    'market_name' => $faker->company, // Random market name
                    'district' => $faker->randomElement(DistrictObject::districts()), // Random city for district
                    'date_of_maximum_sale' => $faker->date(), // Random date of maximum sale
                    'product_type' => $faker->randomElement([
                        'Seed',
                        'Ware',
                        'Value added products'
                    ]),

                    'volume_sold_previous_period' => $faker->numberBetween(1, 1000) * 10, // Random volume sold
                    'financial_value_of_sales' => $faker->randomFloat(2, 1, 1000) * 10, // Random financial value of sales
                    ...$dates
                ],

                'inter' => [

                    'date_recorded' => $faker->dateTimeThisYear(), // Random date recorded this year
                    'crop_type' => $faker->randomElement($crops), // Random crop type
                    'market_name' => $faker->company, // Random market name
                    'country' => $faker->country, // Random country
                    'date_of_maximum_sale' => $faker->date(), // Random date of maximum sale
                    'product_type' => $faker->randomElement([
                        'Seed',
                        'Ware',
                        'Value added products'
                    ]),

                    'volume_sold_previous_period' => $faker->numberBetween(1, 1000) * 10, // Random volume sold
                    'financial_value_of_sales' => $faker->randomFloat(2, 1, 1000) * 10, // Random financial value of sales
                    ...$dates
                ],


            ];

        }


        $data = [];


        foreach (range(1, 10) as $index) {
            $data = rpmf();
            $farmer = RtcProductionFarmer::create($data['main']);

            $farmer->cultivatedArea()->create($data['cultivation']);
            $farmer->basicSeed()->create($data['area_under_basic_seed_multiplication']);
            $farmer->certifiedSeed()->create($data['area_under_certified_seed_multiplication']);
            $farmer->marketInformationSystems()->create($data['market_information_systems']);
            $farmer->aggregationCenters()->create($data['aggregation_center_sales']);
            $farmer->doms()->create($data['domestic']);
            $farmer->intermarkets()->create($data['inter']);
            $farmer->agreements()->create($data['conc_aggrement']);
            $faker = Faker::create();

        }


        function rpmfFU()
        {


            $faker = Faker::create();
            $dates = [
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ];

            $crops = ['Cassava', 'Sweet potato', 'Potato'];
            return [
                'main' => [
                    // 'epa' => $faker->word, // Random word for epa
                    // 'district' => $faker->randomElement(DistrictObject::districts()), // Random city for district
                    // 'section' => $faker->word, // Random word for section
                    // 'enterprise' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato']),
                    'date_of_follow_up' => $faker->date, // Random date

                    'number_of_plantlets_produced_cassava' => $faker->numberBetween(1, 1000) * 10, // Random number for plantlets produced (cassava)
                    'number_of_plantlets_produced_potato' => $faker->numberBetween(1, 1000) * 10, // Random number for plantlets produced (potato)
                    'number_of_plantlets_produced_sweet_potato' => $faker->numberBetween(1, 1000) * 10, // Random number for plantlets produced (sweet potato)
                    'number_of_screen_house_vines_harvested' => $faker->numberBetween(1, 1000) * 10, // Random number for vines harvested
                    'number_of_screen_house_min_tubers_harvested' => $faker->numberBetween(1, 1000) * 10, // Random number for tubers harvested
                    'number_of_sah_plants_produced' => $faker->numberBetween(1, 1000) * 10, // Random number for SAH plants produced
                    'is_registered_seed_producer' => $faker->boolean, // Random boolean for registered seed producer
                    'registration_number_seed_producer' => $faker->unique()->numerify('SEED-####'), // Random seed registration number
                    'registration_date_seed_producer' => $faker->date, // Random date for seed producer registration
                    'uses_certified_seed' => $faker->boolean, // Random boolean
                    'market_segment_fresh' => $faker->boolean, // Random boolean for fresh market segment
                    'market_segment_processed' => $faker->boolean, // Random boolean for processed market segment
                    'has_rtc_market_contract' => $faker->boolean, // Random boolean for market contract
                    'total_vol_production_previous_season' => $faker->numberBetween(1, 1000) * 10, // Random volume for production (metric tonnes)
                    'prod_value_previous_season_total' => $faker->randomFloat(2, 1, 1000) * 10, // Random float for total production value
                    'prod_value_previous_season_date_of_max_sales' => $faker->date, // Random date for max sales
                    'prod_value_previous_season_usd_rate' => $faker->randomFloat(2, 0.8, 1.5), // Random USD rate
                    'prod_value_previous_season_usd_value' => $faker->randomFloat(2, 1, 1000) * 10, // Random USD value
                    'total_vol_irrigation_production_previous_season' => $faker->numberBetween(1, 1000) * 10, // Random volume for irrigation production
                    'irr_prod_value_previous_season_total' => $faker->randomFloat(2, 1, 1000) * 10, // Random total value for irrigation production
                    'irr_prod_value_previous_season_date_of_max_sales' => $faker->date, // Random date for max sales (irrigation)
                    'irr_prod_value_previous_season_usd_rate' => $faker->randomFloat(2, 0.8, 1.5), // Random USD rate for irrigation
                    'irr_prod_value_previous_season_usd_value' => $faker->randomFloat(2, 1, 1000) * 10, // Random USD value for irrigation
                    'sells_to_domestic_markets' => $faker->boolean, // Random boolean
                    'sells_to_international_markets' => $faker->boolean, // Random boolean
                    'uses_market_information_systems' => $faker->boolean, // Random boolean
                    'user_id' => 3, // Random user ID
                    'status' => 'approved', // Fixed value
                    'sells_to_aggregation_centers' => $faker->boolean, // Random boolean for selling to aggregation centers
                    'total_vol_aggregation_center_sales' => $faker->numberBetween(1, 1000) * 10, // Random volume for aggregation center sales
                ],

                'cultivation' => [
                    'variety' => $faker->word(),
                    'area' => $faker->numberBetween(10, 1000) * 10
                ],
                'area_under_basic_seed_multiplication' => [
                    'variety' => $faker->word(),
                    'area' => $faker->numberBetween(10, 1000) * 10
                ],

                'area_under_certified_seed_multiplication' => [
                    'variety' => $faker->word(),
                    'area' => $faker->numberBetween(10, 1000) * 10
                ],

                'aggregation_center_sales' => [
                    'name' => $faker->word(),

                ],

                'market_information_systems' => [
                    'name' => $faker->word(),
                ],

                'conc_aggrement' => [

                    'date_recorded' => $faker->dateTimeThisYear(), // Random date recorded this year
                    'partner_name' => $faker->company, // Random company name
                    'country' => $faker->country, // Random country
                    'date_of_maximum_sale' => $faker->date(), // Random date of maximum sale
                    'product_type' => $faker->randomElement([
                        'Seed',
                        'Ware',
                        'Value added products'
                    ]),

                    'volume_sold_previous_period' => $faker->numberBetween(1, 1000) * 10, // Random volume sold
                    'financial_value_of_sales' => $faker->randomFloat(2, 1, 1000) * 10, // Random financial value of sales
                    ...$dates
                ],

                'domestic' => [

                    'date_recorded' => $faker->dateTimeThisYear(), // Random date recorded this year
                    'crop_type' => $faker->randomElement($crops), // Random crop type
                    'market_name' => $faker->company, // Random market name
                    'district' => $faker->randomElement(DistrictObject::districts()), // Random city for district
                    'date_of_maximum_sale' => $faker->date(), // Random date of maximum sale
                    'product_type' => $faker->randomElement([
                        'Seed',
                        'Ware',
                        'Value added products'
                    ]),

                    'volume_sold_previous_period' => $faker->numberBetween(1, 1000) * 10, // Random volume sold
                    'financial_value_of_sales' => $faker->randomFloat(2, 1, 1000) * 10, // Random financial value of sales
                    ...$dates
                ],

                'inter' => [

                    'date_recorded' => $faker->dateTimeThisYear(), // Random date recorded this year
                    'crop_type' => $faker->randomElement($crops), // Random crop type
                    'market_name' => $faker->company, // Random market name
                    'country' => $faker->country, // Random country
                    'date_of_maximum_sale' => $faker->date(), // Random date of maximum sale
                    'product_type' => $faker->randomElement([
                        'Seed',
                        'Ware',
                        'Value added products'
                    ]),

                    'volume_sold_previous_period' => $faker->numberBetween(1, 1000) * 10, // Random volume sold
                    'financial_value_of_sales' => $faker->randomFloat(2, 1, 1000) * 10, // Random financial value of sales
                    ...$dates
                ],


            ];

        }

        foreach (range(1, 10) as $index) {
            $data = rpmfFU();
            $farmer = RtcProductionFarmer::inRandomOrder()->first();
            $farmer->followups()->create($data['main']);
            $farmer->cultivatedArea()->create($data['cultivation']);
            $farmer->basicSeed()->create($data['area_under_basic_seed_multiplication']);
            $farmer->certifiedSeed()->create($data['area_under_certified_seed_multiplication']);
            $farmer->marketInformationSystems()->create($data['market_information_systems']);
            $farmer->aggregationCenters()->create($data['aggregation_center_sales']);
            $farmer->doms()->create($data['domestic']);
            $farmer->intermarkets()->create($data['inter']);
            $farmer->agreements()->create($data['conc_aggrement']);
            $faker = Faker::create();

        }



        function rpmp()
        {


            $faker = Faker::create();
            $dates = [
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ];

            $crops = ['Cassava', 'Sweet potato', 'Potato'];
            return [
                'main' => [
                    'epa' => $faker->word, // Random word for epa
                    'district' => $faker->randomElement(DistrictObject::districts()), // Random city for district
                    'section' => $faker->word, // Random word for section
                    'enterprise' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato']),
                    'date_of_recruitment' => $faker->date, // Random date
                    'name_of_actor' => $faker->name, // Random name
                    'name_of_representative' => $faker->name, // Random name
                    'phone_number' => $faker->phoneNumber, // Random phone number
                    'type' => $faker->randomElement(['Producer organization (PO)', 'Large scale farm']),

                    'approach' => $faker->optional()->randomElement([
                        'Collective production only',
                        'Collective marketing only',
                        'Knowledge sharing only',
                        'Collective production, marketing and knowledge sharing',
                        'N/A'
                    ]),

                    'sector' => $faker->randomElement(['Public', 'Private']), // Random sector
                    'group' => $faker->randomElement([

                        'Other'
                    ]),

                    'establishment_status' => $faker->randomElement([
                        'New',
                        'Old'
                    ]),

                    'is_registered' => $faker->boolean, // Random boolean
                    'registration_body' => $faker->company, // Random company for registration body
                    'registration_number' => $faker->unique()->numerify('REG-####'), // Random unique registration number
                    'registration_date' => $faker->date, // Random registration date
                    'market_segment_fresh' => $faker->boolean, // Random boolean for fresh market segment
                    'market_segment_processed' => $faker->boolean, // Random boolean for processed market segment
                    'has_rtc_market_contract' => $faker->boolean, // Random boolean for market contract
                    'total_vol_production_previous_season' => $faker->numberBetween(1, 1000) * 10, // Random volume for production (metric tonnes)
                    'prod_value_previous_season_total' => $faker->randomFloat(2, 1, 1000) * 10, // Random float for total production value
                    'prod_value_previous_season_date_of_max_sales' => $faker->date, // Random date for max sales
                    'prod_value_previous_season_usd_rate' => $faker->randomFloat(2, 0.8, 1.5), // Random USD rate
                    'prod_value_previous_season_usd_value' => $faker->randomFloat(2, 1, 1000) * 10, // Random USD value

                    'sells_to_domestic_markets' => $faker->boolean, // Random boolean
                    'sells_to_international_markets' => $faker->boolean, // Random boolean
                    'uses_market_information_systems' => $faker->boolean, // Random boolean
                    'user_id' => 3, // Random user ID
                    'uuid' => $faker->uuid, // Random UUID
                    'submission_period_id' => 2, // Random submission period ID
                    'organisation_id' => 1, // Random organisation ID
                    'financial_year_id' => $faker->numberBetween(1, 4), // Random financial year ID
                    'period_month_id' => 1, // Random period month ID
                    'status' => 'approved', // Fixed value
                    'sells_to_aggregation_centers' => $faker->boolean, // Random boolean for selling to aggregation centers
                    'total_vol_aggregation_center_sales' => $faker->numberBetween(1, 1000) * 10, // Random volume for aggregation center sales
                    'emp_formal_female_18_35' => $faker->numberBetween(10, 1000) * 10, // Random number for formal female employees 18-35
                    'emp_formal_male_18_35' => $faker->numberBetween(10, 1000) * 10, // Random number for formal male employees 18-35
                    'emp_formal_male_35_plus' => $faker->numberBetween(10, 1000) * 10, // Random number for formal male employees 35+
                    'emp_formal_female_35_plus' => $faker->numberBetween(10, 1000) * 10, // Random number for formal female employees 35+
                    'emp_informal_female_18_35' => $faker->numberBetween(10, 1000) * 10, // Random number for informal female employees 18-35
                    'emp_informal_male_18_35' => $faker->numberBetween(10, 1000) * 10, // Random number for informal male employees 18-35
                    'emp_informal_male_35_plus' => $faker->numberBetween(10, 1000) * 10, // Random number for informal male employees 35+
                    'emp_informal_female_35_plus' => $faker->numberBetween(10, 1000) * 10, // Random number for informal female employees 35+
                    'mem_female_18_35' => $faker->numberBetween(10, 1000) * 10, // Random number for female members 18-35
                    'mem_male_18_35' => $faker->numberBetween(10, 1000) * 10, // Random number for male members 18-35
                    'mem_male_35_plus' => $faker->numberBetween(10, 1000) * 10, // Random number for male members 35+
                    'mem_female_35_plus' => $faker->numberBetween(10, 1000) * 10, // Random number for female members 35+
                ],





                'aggregation_center_sales' => [
                    'name' => $faker->word(),

                ],

                'market_information_systems' => [
                    'name' => $faker->word(),
                ],

                'conc_aggrement' => [

                    'date_recorded' => $faker->dateTimeThisYear(), // Random date recorded this year
                    'partner_name' => $faker->company, // Random company name
                    'country' => $faker->country, // Random country
                    'date_of_maximum_sale' => $faker->date(), // Random date of maximum sale
                    'product_type' => $faker->randomElement([
                        'Seed',
                        'Ware',
                        'Value added products'
                    ]),

                    'volume_sold_previous_period' => $faker->numberBetween(1, 1000) * 10, // Random volume sold
                    'financial_value_of_sales' => $faker->randomFloat(2, 1, 1000) * 10, // Random financial value of sales
                    ...$dates
                ],

                'domestic' => [

                    'date_recorded' => $faker->dateTimeThisYear(), // Random date recorded this year
                    'crop_type' => $faker->randomElement($crops), // Random crop type
                    'market_name' => $faker->company, // Random market name
                    'district' => $faker->randomElement(DistrictObject::districts()), // Random city for district
                    'date_of_maximum_sale' => $faker->date(), // Random date of maximum sale
                    'product_type' => $faker->randomElement([
                        'Seed',
                        'Ware',
                        'Value added products'
                    ]),

                    'volume_sold_previous_period' => $faker->numberBetween(1, 1000) * 10, // Random volume sold
                    'financial_value_of_sales' => $faker->randomFloat(2, 1, 1000) * 10, // Random financial value of sales
                    ...$dates
                ],

                'inter' => [

                    'date_recorded' => $faker->dateTimeThisYear(), // Random date recorded this year
                    'crop_type' => $faker->randomElement($crops), // Random crop type
                    'market_name' => $faker->company, // Random market name
                    'country' => $faker->country, // Random country
                    'date_of_maximum_sale' => $faker->date(), // Random date of maximum sale
                    'product_type' => $faker->randomElement([
                        'Seed',
                        'Ware',
                        'Value added products'
                    ]),

                    'volume_sold_previous_period' => $faker->numberBetween(1, 1000) * 10, // Random volume sold
                    'financial_value_of_sales' => $faker->randomFloat(2, 1, 1000) * 10, // Random financial value of sales
                    ...$dates
                ],


            ];

        }
        foreach (range(1, 10) as $index) {
            $data = rpmp();
            $farmer = RtcProductionProcessor::create($data['main']);


            $farmer->marketInformationSystems()->create($data['market_information_systems']);
            $farmer->aggregationCenters()->create($data['aggregation_center_sales']);
            $farmer->doms()->create($data['domestic']);
            $farmer->intermarkets()->create($data['inter']);
            $farmer->agreements()->create($data['conc_aggrement']);


        }


        function rpmpFU()
        {


            $faker = Faker::create();
            $dates = [
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ];

            $crops = ['Cassava', 'Sweet potato', 'Potato'];
            return [
                'main' => [
                    // 'epa' => $faker->word, // Random word for epa
                    // 'district' => $faker->randomElement(DistrictObject::districts()), // Random city for district
                    // 'section' => $faker->word, // Random word for section
                    // 'enterprise' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato']),
                    'date_of_follow_up' => $faker->date, // Random date


                    'market_segment_fresh' => $faker->boolean, // Random boolean for fresh market segment
                    'market_segment_processed' => $faker->boolean, // Random boolean for processed market segment
                    'has_rtc_market_contract' => $faker->boolean, // Random boolean for market contract
                    'total_vol_production_previous_season' => $faker->numberBetween(1, 1000) * 10, // Random volume for production (metric tonnes)
                    'prod_value_previous_season_total' => $faker->randomFloat(2, 1, 1000) * 10, // Random float for total production value
                    'prod_value_previous_season_date_of_max_sales' => $faker->date, // Random date for max sales
                    'prod_value_previous_season_usd_rate' => $faker->randomFloat(2, 0.8, 1.5), // Random USD rate
                    'prod_value_previous_season_usd_value' => $faker->randomFloat(2, 1, 1000) * 10, // Random USD value
                    'sells_to_domestic_markets' => $faker->boolean, // Random boolean
                    'sells_to_international_markets' => $faker->boolean, // Random boolean
                    'uses_market_information_systems' => $faker->boolean, // Random boolean
                    'user_id' => 3, // Random user ID
                    'status' => 'approved', // Fixed value
                    'sells_to_aggregation_centers' => $faker->boolean, // Random boolean for selling to aggregation centers
                    'total_vol_aggregation_center_sales' => $faker->numberBetween(1, 1000) * 10, // Random volume for aggregation center sales
                ],

                'cultivation' => [
                    'variety' => $faker->word(),
                    'area' => $faker->numberBetween(10, 1000) * 10
                ],
                'area_under_basic_seed_multiplication' => [
                    'variety' => $faker->word(),
                    'area' => $faker->numberBetween(10, 1000) * 10
                ],

                'area_under_certified_seed_multiplication' => [
                    'variety' => $faker->word(),
                    'area' => $faker->numberBetween(10, 1000) * 10
                ],

                'aggregation_center_sales' => [
                    'name' => $faker->word(),

                ],

                'market_information_systems' => [
                    'name' => $faker->word(),
                ],

                'conc_aggrement' => [

                    'date_recorded' => $faker->dateTimeThisYear(), // Random date recorded this year
                    'partner_name' => $faker->company, // Random company name
                    'country' => $faker->country, // Random country
                    'date_of_maximum_sale' => $faker->date(), // Random date of maximum sale
                    'product_type' => $faker->randomElement([
                        'Seed',
                        'Ware',
                        'Value added products'
                    ]),

                    'volume_sold_previous_period' => $faker->numberBetween(1, 1000) * 10, // Random volume sold
                    'financial_value_of_sales' => $faker->randomFloat(2, 1, 1000) * 10, // Random financial value of sales
                    ...$dates
                ],

                'domestic' => [

                    'date_recorded' => $faker->dateTimeThisYear(), // Random date recorded this year
                    'crop_type' => $faker->randomElement($crops), // Random crop type
                    'market_name' => $faker->company, // Random market name
                    'district' => $faker->randomElement(DistrictObject::districts()), // Random city for district
                    'date_of_maximum_sale' => $faker->date(), // Random date of maximum sale
                    'product_type' => $faker->randomElement([
                        'Seed',
                        'Ware',
                        'Value added products'
                    ]),

                    'volume_sold_previous_period' => $faker->numberBetween(1, 1000) * 10, // Random volume sold
                    'financial_value_of_sales' => $faker->randomFloat(2, 1, 1000) * 10, // Random financial value of sales
                    ...$dates
                ],

                'inter' => [

                    'date_recorded' => $faker->dateTimeThisYear(), // Random date recorded this year
                    'crop_type' => $faker->randomElement($crops), // Random crop type
                    'market_name' => $faker->company, // Random market name
                    'country' => $faker->country, // Random country
                    'date_of_maximum_sale' => $faker->date(), // Random date of maximum sale
                    'product_type' => $faker->randomElement([
                        'Seed',
                        'Ware',
                        'Value added products'
                    ]),

                    'volume_sold_previous_period' => $faker->numberBetween(1, 1000) * 10, // Random volume sold
                    'financial_value_of_sales' => $faker->randomFloat(2, 1, 1000) * 10, // Random financial value of sales
                    ...$dates
                ],


            ];

        }

        foreach (range(1, 10) as $index) {
            $data = rpmpFU();
            $farmer = RtcProductionProcessor::inRandomOrder()->first();
            $farmer->followups()->create($data['main']);

            $farmer->marketInformationSystems()->create($data['market_information_systems']);
            $farmer->aggregationCenters()->create($data['aggregation_center_sales']);
            $farmer->doms()->create($data['domestic']);
            $farmer->intermarkets()->create($data['inter']);
            $farmer->agreements()->create($data['conc_aggrement']);
            $faker = Faker::create();

        }


        function src()
        {


            $faker = Faker::create();
            $dates = [
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ];

            $crops = ['Cassava', 'Sweet potato', 'Potato'];
            return [
                'main' => [

                    'epa' => $faker->word, // Random word for epa
                    'district' => $faker->randomElement(DistrictObject::districts()), // Random city for district
                    'section' => $faker->word, // Random word for section
                    "school_name" => $faker->word,
                    "date" => $faker->date,
                    "crop" => $faker->randomElement($crops),
                    "male_count" => $faker->numberBetween(1, 1000) * 10,
                    "female_count" => $faker->numberBetween(1, 1000) * 10,
                    "total" => $faker->numberBetween(1, 1000) * 10,
                    'user_id' => 3, // Random user ID
                    'uuid' => $faker->uuid, // Random UUID
                    'submission_period_id' => 2, // Random submission period ID
                    'organisation_id' => 1, // Random organisation ID
                    'financial_year_id' => $faker->numberBetween(1, 4), // Random financial year ID
                    'period_month_id' => 1, // Random period month ID
                    'status' => 'approved', // Fixed value
                    ...$dates
                ],



            ];

        }

        foreach (range(1, 10) as $index) {
            $data = src();
            SchoolRtcConsumption::create(src()['main']);
        }


        function att()
        {


            $faker = Faker::create();
            $dates = [
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ];

            $crops = ['Cassava', 'Sweet potato', 'Potato'];
            return [
                'main' => [

                    'meetingTitle' => $faker->sentence(3), // Random sentence with 3 words
                    'meetingCategory' => $faker->word, // Random word
                    'rtcCrop_cassava' => $faker->boolean, // True/False (assuming it's a boolean)
                    'rtcCrop_potato' => $faker->boolean, // True/False
                    'rtcCrop_sweet_potato' => $faker->boolean, // True/False
                    'venue' => $faker->city, // Random city name
                    'district' => $faker->randomElement(DistrictObject::districts()), // Random city for district
                    'startDate' => $faker->date, // Random date
                    'endDate' => $faker->date, // Random date
                    'totalDays' => $faker->numberBetween(1, 14), // Random number between 1 and 14
                    'name' => $faker->name, // Random full name
                    'sex' => $faker->randomElement(['Male', 'Female']), // Random gender
                    'organization' => $faker->company, // Random company/organization name
                    'designation' => $faker->jobTitle, // Random job title
                    'phone_number' => $faker->phoneNumber, // Random phone number
                    'email' => $faker->unique()->safeEmail, // Random unique email
                    'user_id' => 3, // Random user ID
                    'uuid' => $faker->uuid, // Random UUID
                    'submission_period_id' => 2, // Random submission period ID
                    'organisation_id' => 1, // Random organisation ID
                    'financial_year_id' => $faker->numberBetween(1, 4), // Random financial year ID
                    'period_month_id' => 1, // Random period month ID
                    'status' => 'approved', // Fixed value
                    ...$dates


                ],



            ];

        }

        foreach (range(1, 10) as $index) {
            $data = att();
            AttendanceRegister::create(attributes: att()['main']);
        }

    }
}
