<?php

namespace App\Livewire\Admin\System;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SystemDetail;
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

            session()->flash('success', 'Maintenance mode updated successfully.');
        }

        $this->confirmingMaintenanceMode = false;
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
                    'maintenance_mode' => $this->maintenance_mode,
                    'maintenance_message' => $this->maintenance_message,
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
        }
    }


    public function render()
    {
        return view('livewire.admin.system.setup');
    }
}
