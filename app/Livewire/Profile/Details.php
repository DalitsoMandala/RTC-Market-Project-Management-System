<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

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

    public function mount()
    {
        // Load the user's current information
        $user = auth()->user();
        $this->email = $user->email;
        $this->username = $user->username;
        $this->organization = $user->organization;
    }

    public function saveProfile()
    {
        $this->validate([
            'username' => 'required|string|max:255',
            'profile_image' => 'nullable|image|max:1024', // 1MB Max
            'organization' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $user->username = $this->username;
        $user->organization = $this->organization;

        if ($this->profile_image) {
            $profileImagePath = $this->profile_image->store('profile_images', 'public');
            $user->profile_image = $profileImagePath;
        }

        $user->save();

        session()->flash('profile_message', 'Profile updated successfully.');
    }

    public function saveSecurity()
    {
        $this->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->old_password, $user->password)) {
            session()->flash('security_error', 'The provided password does not match your current password.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        session()->flash('security_message', 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.profile.details');
    }
}
