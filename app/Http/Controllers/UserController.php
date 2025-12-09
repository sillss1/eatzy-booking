<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function viewProfile()
    {
        $user = Auth::user();

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

        $request->validate([
            'name' => 'nullable|string|max:100',
            'surname' => 'nullable|string|max:100',
            'profile_description' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|max:2048'
        ]);

        if ($request->filled('name')) $user->name = $request->name;
        if ($request->filled('surname')) $user->surname = $request->surname;
        $user->profile_description = $request->profile_description;

        /* File upload example
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $user->profile_picture = $path;
        }
        */

        $user->save();

        return redirect()->route('account')->with('success', 'Profile updated successfully.');
    }

    // Web: Delete account (HARD DELETE)
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            try {
                DB::transaction(function () use ($user) {
                    // 1. Anonymize Data (Set user_id to NULL)
                    DB::table('review')->where('user_id', $user->id)->update(['user_id' => null]);
                    DB::table('reply')->where('user_id', $user->id)->update(['user_id' => null]);
                    DB::table('reservation')->where('user_id', $user->id)->update(['user_id' => null]);
                    DB::table('waitlist')->where('user_id', $user->id)->update(['user_id' => null]);
                    DB::table('notification')->where('user_id', $user->id)->update(['user_id' => null]);

                    // 2. Hard Delete Private Data
                    DB::table('favourite')->where('user_id', $user->id)->delete();

                    // 3. Handle Roles 
                    DB::table('customer')->where('id', $user->id)->delete();
                    DB::table('owner')->where('id', $user->id)->delete();
                    DB::table('administrator')->where('id', $user->id)->delete();

                    // 4. Delete the User
                    DB::table('user')->where('id', $user->id)->delete();
                });

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->with('success', 'Account deleted successfully.');

            } catch (\Exception $e) {
                \Log::error('Delete Account Failed: ' . $e->getMessage());
                // Return the error to the view
                return back()->withErrors(['msg' => 'Error deleting account: ' . $e->getMessage()]);
            }
        }
        return redirect('/login');
        }

        // API: Block user
        public function blockUser(Request $request, $id)
        {
            $admin = Auth::user();
            if (!$admin || !$admin->isAdmin()) {
                return response()->json(['error' => 'Forbidden'], 403);
            }
            $user = User::findOrFail($id);
            $user->is_blocked = (bool)$request->input('is_blocked');
            $user->save();
            return response()->json(['id' => $user->id, 'is_blocked' => $user->is_blocked], 200);
        }
}
