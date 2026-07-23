<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            return redirect()->intended(route('vouchers.index'));
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    public function quickLogin(string $email): void
    {
        $this->email = $email;
        $this->password = 'password';
        $this->login();
    }

    public function render(): View
    {
        return view('livewire.auth.login');
    }
}
