<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request, ImageService $imageService)
    {
        $request->validate([
            'designation' => ['nullable', 'string', 'max:100'],
            'signimg' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->designation = $request->designation;

        if ($request->hasFile('signimg')) {
            $signPath = $imageService->upload($request->file('signimg'), 'uploads/signs');
            if ($signPath) {
                $user->signature = $signPath;
            }
        }

        $user->save();

        return back()->with('success', 'Profile and signature updated successfully.');
    }
}
