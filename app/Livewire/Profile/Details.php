<?php

namespace App\Livewire\Profile;

use Exception;
use Throwable;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Details extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    // Profile information
    public $email;
    public $username;
    public $profile_image;
    public $organization;

    // Security information
    public $old_password;
    public $new_password;
    public $confirm_password;

    public $form_top = true;

    public function mount()
    {
        // Load the user's current information
        $user = auth()->user();
        $this->email = $user->email;
        $this->username = $user->name;
        $this->organization = $user->organisation->name;
    }

    public function saveProfile()
    {

        $this->form_top = true;

        try {

            $this->validate([
                'username' => 'required|string|max:255',
                'profile_image' => 'nullable|image', // 1MB Max
                'organization' => 'required|string|max:255',
            ]);

        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }



        try {
            $user = User::find(auth()->user()->id);
            $user->name = $this->username;
            //  $user->organisation->id = $this->organization;
            if ($this->profile_image) {
                $name = Str::random(32) . '.' . $this->profile_image->extension();
                $this->profile_image->storeAs('public/profiles', $name);
                $user->image = $name;

            }

            $user->save();


            session()->flash('success', 'Profile updated successfully.');
            return redirect()->to(url()->previous());
        } catch (Throwable $th) {
            session()->flash('error', 'Something went wrong.');
        }





    }

    public function saveSecurity()
    {
        $this->form_top = false;

        try {

            $this->validate([
                'old_password' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (!Hash::check($value, Auth::user()->password)) {
                            return $fail('The provided password does not match your current password.');
                        }
                    },
                ],
                'new_password' => 'required|min:8',
                'confirm_password' => 'required|same:new_password',
            ]);

        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }




        try {
            $user = User::find(auth()->user()->id);
            // if (!Hash::check($this->old_password, $user->password)) {
            //     session()->flash('error', 'The provided password does not match your current password.');
            //     throw new Exception('Old password does not match');
            // }

            $user->password = Hash::make($this->new_password);
            $user->save();

            session()->flash('success', 'Password updated successfully.');
            return redirect()->to(url()->previous());
        } catch (Throwable $th) {
            session()->flash('error', 'Something went wrong.');
        }



    }

    public function render()
    {
        return view('livewire.profile.details');
    }
}
