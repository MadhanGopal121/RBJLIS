<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function showChangePassword()
    {
        return view('password.changepassword');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => ['required'],
            'new_password' => ['required', 'min:6'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with('error', 'Invalid old password. Please try again.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }
}
