<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Login - MCQ PRO')]
class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    public $error = '';

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            // Do not enforce minimum length here since legacy users may have shorter passwords.
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();
            $this->redirect('/dashboard', navigate: true);
        } else {
            $this->error = 'Invalid email or password.';
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
