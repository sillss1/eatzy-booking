<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function viewProfile($id = null)
    {
        $user = $id ? User::findOrFail($id) : Auth::user();
        return view('auth.account', compact('user'));
    }
    
    public function editProfile()
    {
        $user = Auth::user();
        
        return view('auth.account_edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login')->withErrors(['msg' => 'Unauthorized action.']);
        }

        $validated = $request->validate([
        'username' => [
            'required',
            'string',
            'max:50',
            Rule::unique('user', 'username')->ignore($user->id),
        ],
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('user', 'email')->ignore($user->id),
        ],
        'name' => 'nullable|string|max:100',
        'surname' => 'nullable|string|max:100',
        'profile_description' => 'nullable|string|max:500',
        'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'remove_picture' => 'nullable|boolean',
    ]);

    $user->updateProfile(
        $validated,
        $request->file('profile_picture'),
        $request->boolean('remove_picture')
    );


        return back()->with('success', 'Profile updated successfully.');
    }

    public function removePicture()
    {
        $user = Auth::user();
        $user->updateProfile([], null, true);

        return back()->with('success', 'Profile picture removed successfully.');
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();
        $user->deleteAccount();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Account deleted successfully.');
    }

    public function blockUser(Request $request, $id)
    {
        $admin = Auth::user();
        $user = User::findOrFail($id);

        if (!$admin->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $user->block((bool)$request->input('is_blocked'));
        return response()->json(['id' => $user->id, 'is_blocked' => $user->is_blocked]);
    }
}