<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use App\Models\HouseholdRtcConsumption;
use Illuminate\Bus\Batchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessHouseholdRtcConsumption implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $batch_no;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($batch_no = null)
    {
        $this->batch_no = $batch_no;
        Cache::put('hrc_batch', []);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $results = collect();

        $query = HouseholdRtcConsumption::with(['user.organisation']);

        if ($this->batch_no) {
            $query->where('uuid', $this->batch_no);
        }

        $query->chunk(1000, function ($items) use (&$results) {
            foreach ($items as $item) {
                $location = json_decode($item->location_data);
                $main_food = json_decode($item->main_food_data);

                $item->enterprise = $location->enterprise ?? null;
                $item->district = $location->district ?? null;
                $item->epa = $location->epa ?? null;
                $item->section = $location->section ?? null;
                $item->date_of_assessment = Carbon::parse($item->date_of_assessment)->format('d/m/Y');

                $food = collect($main_food);
                $item->cassava_count = $food->contains('name', 'CASSAVA') ? 'YES' : 'NO';
                $item->potato_count = $food->contains('name', 'POTATO') ? 'YES' : 'NO';
                $item->sweet_potato_count = $food->contains('name', 'SWEET POTATO') ? 'YES' : 'NO';

                $item->submission_date = Carbon::parse($item->created_at)->format('d/m/Y');
                $item->submitted_by = $item->user->organisation->name;

                $results->push($item);
            }
        });


        Cache::put('hrc_batch', $results, now()->addMinutes(1)); // Cache for 10 min
    }
}
