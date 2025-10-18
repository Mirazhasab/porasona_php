<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle traditional email/password login
     */
    public function handleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            // Keep rule lenient because legacy accounts may use shorter passwords.
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Successfully logged in!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration
     */
    public function handleRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Account created successfully!');
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Update existing user with Google info
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => $user->role ?? 'user',
                ]);
                
                // Ensure user has a role assigned
                if (!$user->hasAnyRole(['admin', 'moderator', 'user'])) {
                    $user->assignRole('user');
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => null, // Google OAuth users don't need password
                    'role' => 'user',
                ]);
                
                // Auto-assign 'user' role after Google registration
                $user->assignRole('user');
            }
            
            // Login the user
            Auth::login($user);
            
            return redirect()->route('dashboard')->with('success', 'Successfully logged in with Google!');
            
        } catch (Exception $e) {
            return redirect()->route('auth.error')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * Show success page
     */
    public function success()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        return view('auth.success');
    }

    /**
     * Show error page
     */
    public function error()
    {
        return view('auth.error');
    }

    /**
     * Logout user
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Successfully logged out!');
    }
}
