<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // Web: Delete account (HARD DELETE)
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            try {
                DB::transaction(function () use ($user) {
                    // 1. Anonimizar dados antes de apagar fisicamente
                    DB::table('review')->where('user_id', $user->id)->update(['user_id' => null]);
                    DB::table('reply')->where('user_id', $user->id)->update(['user_id' => null]);
                    DB::table('reservation')->where('user_id', $user->id)->update(['user_id' => null]);
                    DB::table('waitlist')->where('user_id', $user->id)->update(['user_id' => null]);
                    DB::table('notification')->where('user_id', $user->id)->update(['user_id' => null]);
                    
                    // 2. Apagar dados privados
                    DB::table('favourite')->where('user_id', $user->id)->delete();
                    DB::table('customer')->where('id', $user->id)->delete();
                    DB::table('owner')->where('id', $user->id)->delete();
                    DB::table('administrator')->where('id', $user->id)->delete();

                    // 3. TRUE DELETE
                    DB::table('user')->where('id', $user->id)->delete();
                });

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->with('success', 'Account permanently deleted.');

            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Could not delete account: ' . $e->getMessage()]);
            }
        }

        return redirect('/login');
    }

    // API: Block user (Admin Action)
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
