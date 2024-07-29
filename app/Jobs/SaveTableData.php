<?php

namespace App\Jobs;

use App\Models\HouseholdRtcConsumption;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SaveTableData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data, $table;

    protected $submissionData;
    protected $uuid;

    public function __construct(array $submissionData, $uuid)
    {
        $this->submissionData = $submissionData;
        $this->uuid = $uuid;
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //


        $modifiedRows = Cache::get($this->uuid);
        $uuid = $this->uuid;
        $submissionData = $this->submissionData;


        $items = collect($modifiedRows);

        $chunks = $items->chunk(200);
        foreach ($chunks as $chunk) {

            DB::table('household_rtc_consumption')->insert($chunk->toArray());

        }




    }
}
