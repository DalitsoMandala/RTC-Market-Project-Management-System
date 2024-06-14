<?php

namespace App\Livewire\Forms\RtcMarket\SchoolConsumption;

use App\Models\SchoolRtcConsumption;
use App\Notifications\ManualDataAddedNotification;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Ramsey\Uuid\Uuid;

class Add extends Component
{
    use LivewireAlert;

    public $variable;
    public $rowId;

    public $location_data = [];

    public $date;
    public $crop;
    public $male_count;
    public $female_count;
    public $total;

    public function setData($id)
    {
        $this->resetErrorBag();

    }

    public function save()
    {

        try {
            # code...
            $uuid = Uuid::uuid4()->toString();
            $table = [
                'date' => $this->date,
                'location_data' => json_encode($this->location_data),
                'male_count' => $this->male_count,
                'female_count' => $this->female_count,
                'total' => $this->total,
                'crop' => $this->crop,
                'uuid' => $uuid,
                'user_id' => auth()->user()->id,
            ];

            SchoolRtcConsumption::create($table);

            $currentUser = Auth::user();
            $link = 'forms/rtc-market/school-rtc-consumption-form/' . $uuid . '/view';
            $currentUser->notify(new ManualDataAddedNotification($uuid, $link));
            $this->dispatch('notify');
            $this->alert('success', 'Successfully submitted!', [
                'toast' => false,
                'position' => 'center',
            ]);
        } catch (\Throwable $e) {
            # code...

            dd($e);
        }

    }

    public function render()
    {
        return view('livewire.forms.rtc-market.school-consumption.add');
    }
}
