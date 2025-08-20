<?php

namespace App\Livewire\Admin\System;

use Ramsey\Uuid\Uuid;
use Livewire\Component;
use Livewire\Attributes\On;

use App\Models\SystemDetail;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use PHPUnit\Event\Telemetry\System;
use Jantinnerezo\LivewireAlert\LivewireAlert;


class Setup extends Component
{
    use LivewireAlert;
    public $name;
    public $address;
    public $website;
    public $phone;
    public $email;
    public $maintenance_mode = false;
    public $maintenance_message;
    public $confirmingMaintenanceMode = false;
    public $secretKey;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'maintenance_mode' => 'nullable|boolean',
            'maintenance_message' => 'nullable|string',
        ];
    }


    public function confirmMaintenanceMode()
    {
        $this->confirmingMaintenanceMode = true;
    }
    public function saveMaintananceMode()
    {
        $systemDetail = SystemDetail::find(1);

        if ($systemDetail) {
            $systemDetail->update([
                'maintenance_mode' => $this->maintenance_mode,
                'maintenance_message' => $this->maintenance_message,
            ]);
            $this->dispatch('hideModal');


            if ($this->maintenance_mode) {
                Artisan::call('down' . ' --secret=' . $this->secretKey);
                $user = User::find(auth()->user()->id);
                $user->notify(new \App\Notifications\MaintenanceNotification($this->secretKey));
                session()->flash('success', 'Maintenance mode enabled successfully.');
            } else {
                Artisan::call('up');
                session()->flash('success', 'Maintenance mode disabled successfully.');
            }
        }
    }
    public function save()
    {

        $this->validate();

        try {
            SystemDetail::updateOrCreate(
                ['id' => 1], // Assuming you have only one system detail record to manage
                [
                    'name' => $this->name,
                    'address' => $this->address,
                    'website' => $this->website,
                    'phone' => $this->phone,
                    'email' => $this->email,

                ]
            );


            session()->flash('success', 'System details updated successfully.');
        } catch (\Throwable $th) {

            Log::error($th);
            session()->flash('error', 'An error occurred while updating the system details.');
        }
    }

    public function mount()
    {
        $systemDetail = SystemDetail::find(1); // Load the existing data

        if ($systemDetail) {
            $this->name = $systemDetail->name;
            $this->address = $systemDetail->address;
            $this->website = $systemDetail->website;
            $this->phone = $systemDetail->phone;
            $this->email = $systemDetail->email;
            $this->maintenance_mode = $systemDetail->maintenance_mode;
            $this->maintenance_message = $systemDetail->maintenance_message;
            $this->secretKey = Uuid::uuid4()->toString();
        }
    }


    public function render()
    {
        return view('livewire.admin.system.setup');
    }
}
