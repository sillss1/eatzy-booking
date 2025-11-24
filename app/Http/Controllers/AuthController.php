<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/restaurants');
        }
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:250',
            'surname' => 'required|string|max:250',
            'username' => 'required|string|max:250|unique:user',
            'email' => 'required|email|max:250|unique:user',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:customer,owner', // Validate role
        ]);

        // 1. Create the Base User
        $user = User::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'joined_at' => now(),
        ]);

        // 2. Assign Role (Insert into specific table)
        if ($validated['role'] === 'owner') {
            \DB::table('owner')->insert(['id' => $user->id]);
        } else {
            \DB::table('customer')->insert(['id' => $user->id]);
        }

        Auth::login($user);

        // Redirect based on role
        if (method_exists($user, 'isOwner') && $user->isOwner()) {
            return redirect('/restaurants')->with('success', 'Owner account created!');
        }
        return redirect('/restaurants')->with('success', 'Customer account created!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
