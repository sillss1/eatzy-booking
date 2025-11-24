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
                // BR01: Anonimizar dados antes de apagar fisicamente
                DB::transaction(function () use ($user) {
                    // 1. Anonimizar conteúdos públicos (meter user_id a NULL)
                    // Nota: As colunas user_id nestas tabelas têm de aceitar NULL na BD
                    DB::table('review')->where('user_id', $user->id)->update(['user_id' => null]);
                    DB::table('reply')->where('user_id', $user->id)->update(['user_id' => null]);
                    DB::table('reservation')->where('user_id', $user->id)->update(['user_id' => null]);
                    DB::table('waitlist')->where('user_id', $user->id)->update(['user_id' => null]);
                    DB::table('notification')->where('user_id', $user->id)->update(['user_id' => null]);
                    // Se for Owner, pode querer anonimizar restaurantes
                    // DB::table('restaurant')->where('owner_id', $user->id)->update(['owner_id' => null]);

                    // 2. Apagar dados privados
                    DB::table('favourite')->where('user_id', $user->id)->delete();

                    // 3. TRUE DELETE
                    $user->delete();
                });

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->with('success', 'Account permanently deleted.');
            } catch (\Exception $e) {
                // Caso dê erro (ex: violação de constraint NOT NULL)
                return back()->withErrors(['error' => 'Could not delete account: ' . $e->getMessage()]);
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
