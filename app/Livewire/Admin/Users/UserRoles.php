<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class UserRoles extends Component
{
    use LivewireAlert;
    public $role;

    protected $rules = [
        'role' => 'required|unique:roles,name',
    ];
    public function save()
    {

        try {
            $this->validate();
        } catch (\Throwable $e) {
            //       session()->flash('validation_error', 'There are errors in the form.');
            $this->dispatch('show-alert', data: [
                'type' => 'error',  // success, error, info, warning
                'message' => 'There are errors in the form.'
            ]);
            throw $e;
        }
        DB::beginTransaction();
        try {
            # code...

            DB::commit();
            Role::create([
                'name' => strtolower($this->role)
            ]);
            $this->dispatch('show-alert', data: [
                'type' => 'success',  // success, error, info, warning
                'message' => 'Role created successfully.'
            ]);
            $this->dispatch('hideModal');
        } catch (\Throwable $e) {
            # code...
            DB::rollBack();
            $this->dispatch('show-alert', data: [
                'type' => 'error',  // success, error, info, warning
                'message' => 'Something went wrong.'
            ]);
            throw $e;
        }
    }
    public function mount() {}


    public function render()
    {
        return view('livewire.admin.users.user-roles');
    }
}
