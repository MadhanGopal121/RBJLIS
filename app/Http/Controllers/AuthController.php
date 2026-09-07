<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function showLogin()
    {
        return $this->showLoginForm();
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            $user->last_login = now();
            $user->save();

            // Store active sidebar in session based on role
            $this->setSessionVariables($user);

            return $this->redirectBasedOnRole($user);
        }

        return back()->with('error', 'Invalid username or password, try again')->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function setSessionVariables(User $user): void
    {
        if ($user->role_id == 1) {
            session(['sidebar' => 'superadmin']);
        } elseif ($user->role_id == 2) {
            session(['sidebar' => 'labadmin']);
        } elseif ($user->role_id == 8) {
            session(['sidebar' => 'll']);
        } else {
            session(['sidebar' => 'frontoffice']);
        }
    }

    private function redirectBasedOnRole(User $user)
    {
        if ($user->role_id == 1) {
            return redirect()->route('superadmin.index');
        } elseif ($user->role_id == 2) {
            return redirect()->route('labadmin.index');
        } elseif ($user->role_id == 3) {
            return redirect()->route('frontoffice.index');
        } elseif (in_array($user->role_id, [4, 5, 6, 7])) {
            return redirect()->route('labinvestigation.index');
        } elseif ($user->role_id == 8) {
            return redirect()->route('ll.index');
        }

        return redirect()->route('frontoffice.index');
    }
}
