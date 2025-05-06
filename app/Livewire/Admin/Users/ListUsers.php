<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Models\Organisation;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\NewUserNotification;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ListUsers extends Component
{
    use LivewireAlert;

    public $name;
    public $email;
    public $phone;
    public $organisation;

    public $organisations = [];
    public $role;

    public $rowId;
    public $roles = [];
    public $password;
    public $password_confirmation;
    public $changePassword = false;
    public $disableValue;
    public $disableAll = false;
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)->ignore($this->rowId)],
            'phone' => 'required',
            'organisation' => 'required',
            'role' => 'required',

        ];

        if ($this->changePassword) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        return $rules;
    }


    public function mount()
    {
        $this->roles = Role::all()->pluck('name'); // Fetch all roles from the database
        $this->organisations = Organisation::get();
        $this->organisation = 1;
    }

    #[On('edit')]
    public function editForm($rowId)
    {
        $this->rowId = $rowId;
        $user = User::find($rowId);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone_number;
        $this->organisation = $user->organisation_id;
        $this->role = $user->getRoleNames()->first();
        $this->changePassword = false;
    }

    #[On('showModal-delete')]
    public function delete($rowId)
    {
        $this->rowId = $rowId;
    }

    public function deleteUser()
    {
        User::find($this->rowId)->delete();
        $this->alert('success', 'User successfully deleted!');
        $this->dispatch('refresh');
    }

    public function restoreUser()
    {
        User::withTrashed()->find($this->rowId)->restore();
        $this->alert('success', 'User successfully restored!');
        $this->dispatch('refresh');
    }
    #[On('showModal-restore')]
    public function restore($rowId)
    {
        $this->rowId = $rowId;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->name = null;
        $this->email = null;
        $this->phone = null;
        $this->rowId = null;
        $this->roles = Role::all()->pluck('name'); // Fetch all roles from the database
        $this->organisations = Organisation::get();

        $this->role = null;
        $this->password = null;
        $this->password_confirmation = null;
        $this->changePassword = true;
    }


    #[On('send-users')]
    public function usersData($users)
    {

        $rules = [
            '*.email' => 'required|email|unique:users,email', // Validate email format and uniqueness
            '*.name' => 'required|string|max:255', // Validate name
            '*.organisation' => 'required|string|max:255', // Validate organisation
            '*.role' => 'required|string', // Validate role (only allow 'staff' or 'admin')
        ];
        DB::beginTransaction();
        // Validate the array
        $validator = Validator::make($users, $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // Flash validation errors to the session
            session()->flash('error', $validator->errors());

            // Redirect back or to a specific route
            DB::rollBack();
        } else {
            // Flash a success message to the session

            try {
                foreach ($users as $user) {
                    $organisation = Organisation::where('name', $user['organisation'])->first();
                    $password = Str::random(10);
                    $addedUser =  User::create([
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'phone_number' => '+9999999999',
                        'organisation_id' => $organisation->id,
                        'password' => Hash::make($password),

                    ])->assignRole($user['role']);
                    $addedUser->notify(new NewUserNotification($addedUser->email, $password, $user['role']));
                }
                $this->dispatch('refresh');
                session()->flash('success', 'All users have been validated successfully!');
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                session()->flash('error', 'An error occurred while processing your request.');
            }
        }
    }
    public function save()
    {
        $this->validate();
        DB::beginTransaction();
        // Handle form submission logic, e.g., saving to database or sending an email

        try {

            if ($this->rowId) {

                if ($this->changePassword) {
                    User::find($this->rowId)->update([
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone_number' => $this->phone,
                        'organisation_id' => $this->organisation,
                        'password' => Hash::make($this->password),
                    ]);
                }
                User::find($this->rowId)->update([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone_number' => $this->phone,
                    'organisation_id' => $this->organisation,

                ]);




                User::find($this->rowId)->syncRoles($this->role);


                $this->dispatch('refresh');
                session()->flash('success', 'User successfully updated!');
                $this->resetForm();
            } else {

                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone_number' => $this->phone,
                    'organisation_id' => $this->organisation,
                    'password' => Hash::make($this->password),

                ])->assignRole($this->role);


                $this->dispatch('refresh');
                session()->flash('success', 'User successfully added!');
                $user->notify(new NewUserNotification($this->email, $this->password, $this->role));
                $this->resetForm();
            }

            DB::commit();
        } catch (\Throwable $th) {

            session()->flash('error', 'An error occurred while adding the user.');
            DB::rollBack();
            throw $th;
        }
    }

    public function updatedRole($value)
    {

        if ($value == 'external') {
            $this->organisations = Organisation::where('name', '!=', 'CIP')->get();
        } else {
            $this->organisations = Organisation::where('name', 'CIP')->get();
        }
    }
    public function render()
    {
        return view('livewire.admin.users.list-users');
    }
}