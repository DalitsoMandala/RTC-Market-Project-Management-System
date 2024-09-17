<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use App\Models\HouseholdRtcConsumption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataGenerationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        function hrc($faker)
        {
            return [
                'epa' => $faker->city(),
                'section' => $faker->word(),
                'district' => $faker->state(),
                'enterprise' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato']),
                'date_of_assessment' => $faker->date(),
                'actor_type' => $faker->randomElement(['Farmer', 'Trader', 'Processor']),
                'rtc_group_platform' => $faker->randomElement(['Household', 'Seed']),
                'producer_organisation' => $faker->company(),
                'actor_name' => $faker->name(),
                'age_group' => $faker->randomElement(['Youth', 'Not Youth']),
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
                'financial_year_id' => 1,
                'period_month_id' => 1,
                'status' => $faker->randomElement(['approved']),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ];
        }


        $data = [];
        $faker = Faker::create();

        foreach (range(1, 10000) as $index) {

            $data = HouseholdRtcConsumption::create(hrc($faker));


            $data->mainFoods()->create([
                'name' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato'])
            ]);
        }
        foreach (range(1, 10000) as $index) {

            $data = HouseholdRtcConsumption::create(hrc($faker));


            $data->mainFoods()->create([
                'name' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato'])
            ]);
        }
        foreach (range(1, 10000) as $index) {

            $data = HouseholdRtcConsumption::create(hrc($faker));


            $data->mainFoods()->create([
                'name' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato'])
            ]);
        }
        foreach (range(1, 10000) as $index) {

            $data = HouseholdRtcConsumption::create(hrc($faker));


            $data->mainFoods()->create([
                'name' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato'])
            ]);
        }
        foreach (range(1, 10000) as $index) {

            $data = HouseholdRtcConsumption::create(hrc($faker));


            $data->mainFoods()->create([
                'name' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato'])
            ]);
        }
        foreach (range(1, 10000) as $index) {

            $data = HouseholdRtcConsumption::create(hrc($faker));


            $data->mainFoods()->create([
                'name' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato'])
            ]);
        }
        foreach (range(1, 10000) as $index) {

            $data = HouseholdRtcConsumption::create(hrc($faker));


            $data->mainFoods()->create([
                'name' => $faker->randomElement(['Cassava', 'Sweet potato', 'Potato'])
            ]);
        }

    }
}
