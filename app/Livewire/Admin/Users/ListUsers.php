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
use Livewire\WithFileUploads;

class ListUsers extends Component
{
    use LivewireAlert;
use WithFileUploads;
    public $name;
    public $email;
    public $phone;
    public $organisation;
    public $file;
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


    public function noFile()
    {
        session()->flash('error', 'File is required');
        $this->validate([
            'file' => 'required',
        ]);
    }

    public function mount()
    {
        $this->roles = Role::all()->pluck('name'); // Fetch all roles from the database
        $this->organisations = Organisation::all()->toArray();
    }

    #[On('edit')]
    public function editForm($rowId)
    {
        $this->resetForm();
        $this->rowId = $rowId;
        $user = User::find($rowId);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone_number;
        $this->changePassword = false;
        $this->organisation = null;
        $this->role = null;
        // Set role — this triggers updatedRole() automatically
        $role = $user->roles[0]->name;
        $this->dispatch('update-org', role: $role, organisation: $user->organisation->id);
    }


    // public function changeOrg($role){


    //     if ($role === 'external') {
    //         $this->organisations = Organisation::whereNotIn('name',  ['CIP'])->get();
    //     } else {
    //         $this->organisations = Organisation::whereIn('name', ['CIP'])->get();
    //     }



    //}
    // public function updated($property, $value)
    // {

    //     if ($property === 'role') {
    //         $role = $value;

    //         if ($role === 'external') {
    //             $this->organisations = [];
    //             $this->organisations = Organisation::whereNotIn('name',  ['CIP'])->get()->toArray();
    //         } else {
    //             $this->organisations = [];
    //             $this->organisations = Organisation::whereIn('name', ['CIP'])->get()->toArray();
    //         }
    //     }
    // }

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
        $this->organisation = null;
        $this->role = null;
        $this->password = null;
        $this->password_confirmation = null;
        $this->changePassword = true;
    }


    #[On('send-users')]
    public function usersData($users)
    {
        $rules = [
            '*.email' => 'required|email|unique:users,email',
            '*.name' => 'required|string|max:255',
            '*.organisation' => 'required|string|max:255',
            '*.role' => 'required|string',

        ];

        DB::beginTransaction();

        $validator = Validator::make($users, $rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            $messages = [];

            foreach ($errors as $field => $fieldErrors) {
                // Extract row number from field like "0.email"
                [$row] = explode('.', $field);
                $rowNumber = $row + 1;

                foreach ($fieldErrors as $error) {
                    $cleanError = preg_replace('/\b\d+\./', '', $error);
                    $messages[$rowNumber][] = $cleanError;
                }
            }

            $finalMessages = [];
            foreach ($messages as $rowNumber => $rowErrors) {
                $rowNumber++;
                $finalMessages[] = "Row {$rowNumber}: " . implode(' ', $rowErrors);
            }

            session()->flash('error', implode("<br>", $finalMessages));
            DB::rollBack();
            $this->dispatch('able-button');
            return;
        }

        try {
            if (count($users) == 0) {
                session()->flash('error', 'No data found');
                $this->dispatch('able-button');
                return;
            }

            foreach ($users as $user) {
                $organisation = Organisation::where('name', $user['organisation'])->first();
                $password = Str::random(10);

                $addedUser = User::create([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'phone_number' => '+9999999999',
                    'organisation_id' =>  $organisation->id,
                    'password' => Hash::make($password),
                ])->assignRole($user['role']);

                $addedUser->notify(new NewUserNotification($addedUser->email, $password, $user['role']));
            }

            $this->dispatch('refresh');
            session()->flash('success', 'All users have been validated successfully!');
            $this->dispatch('able-button');
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred while processing your request.');
        }
    }


    public function save()
    {
        $this->validate();
        DB::beginTransaction();
        // Handle form submission logic, e.g., saving to database or sending an email
        $organisationId = $this->organisation;

        try {

            if ($this->rowId) {
                if ($this->changePassword) {
                    User::find($this->rowId)->update([
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone_number' => $this->phone,
                        'organisation_id' => $organisationId,
                        'password' => Hash::make($this->password),
                    ]);
                }
                User::find($this->rowId)->update([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone_number' => $this->phone,
                    'organisation_id' => $organisationId,

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
                    'organisation_id' => $organisationId,
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


    public function render()
    {
        return view('livewire.admin.users.list-users');
    }
}
