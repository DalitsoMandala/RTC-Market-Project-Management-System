<?php

namespace App\Livewire\Admin\System;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;

use Livewire\Attributes\On;
use App\Models\SystemDetail;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Event\Telemetry\System;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;
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

    public function clearCache()
    {
        $pendingJobs = DB::table('jobs')->count();

        if ($pendingJobs === 0) {
            Artisan::call('cache:clear');
            session()->flash('success', 'Cache cleared successfully.');
        }else{
            session()->flash('error', 'There are still pending jobs in the queue.');
        }
    }

    public function exportMantenance()
    {
        $date = date('d/m/Y h:i A');
        $content = "System is now in maintenance mode. Please use this secret key: " . $this->secretKey;
        $content .= "\n\nTo bypass maintenance mode, Please do not share this key with anyone.";
        $content .= "\n\nIf you have any questions or need further assistance, please contact our support team.";
        $content .= "\n\nThank you for your understanding.";
        $content .= "\n\nDate: {$date}";

        return $content;
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
                'maintenance_message' => $this->secretKey,
            ]);



            if ($this->maintenance_mode) {
                Log::info('Maintenance mode enabled successfully:' . $this->secretKey);
                Artisan::call('down' . ' --secret=' . $this->secretKey);
                $user = User::find(auth()->user()->id);
                $user->notify(new \App\Notifications\MaintenanceNotification($this->secretKey));
                session()->flash('success', 'Maintenance mode enabled successfully.');

                $this->dispatch('hideModal', data: $this->exportMantenance());
            } else {
                Artisan::call('up');
                $systemDetail->update([
                    'maintenance_mode' => false,
                    'maintenance_message' => $this->secretKey,
                ]);
                session()->flash('success', 'Maintenance mode disabled successfully.');
                $this->dispatch('hideModal', data: null);
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
