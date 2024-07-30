<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class UserNotificationComponent extends Component
{
    use LivewireAlert;

    public $variable;
    public $rowId;

    public $notifications;

    public function readNotifications()
    {
        $user = Auth::user();
        foreach ($user->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
    }
    #[On('notify')]
    public function putNotifications()
    {
        $user = Auth::user();
        $this->notifications = $user->unreadNotifications;
    }

    public function mount()
    {
        $user = Auth::user();
        $this->notifications = $user->unreadNotifications;
    }

    public function render()
    {
        return view('livewire.user-notification-component');
    }
}