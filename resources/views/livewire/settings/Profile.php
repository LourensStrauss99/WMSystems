<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Profile extends Component
{
    public $name;
    public $email;
    public $telephone;
    public $address;
    public $photo;

    public function updateProfileInformation()
    {
        $user = Auth::user();

        // Update user data
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'address' => $this->address,
            // Handle photo upload if applicable
        ]);

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function render()
    {
        return view('livewire.settings.profile');
    }
}