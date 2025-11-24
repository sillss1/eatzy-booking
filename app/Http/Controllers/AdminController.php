<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // US47: Admin Dashboard
    public function index()
    {
        // Contagens simples para o dashboard
        $stats = [
            'users' => User::count(),
            'owners' => DB::table('owner')->count(),
            'customers' => DB::table('customer')->count(),
            'restaurants' => DB::table('restaurant')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // US48 & US49: Manage Users (List & Search)
    public function listUsers(Request $request)
    {
        $query = User::query();

        // Search by Name or Email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('username', 'ilike', "%{$search}%");
            });
        }

        // Filter by Role
        if ($request->filled('role')) {
            if ($request->role === 'owner') {
                $query->whereIn('id', function($q) { $q->select('id')->from('owner'); });
            } elseif ($request->role === 'customer') {
                $query->whereIn('id', function($q) { $q->select('id')->from('customer'); });
            } elseif ($request->role === 'admin') {
                $query->whereIn('id', function($q) { $q->select('id')->from('administrator'); });
            }
        }

        // Não mostrar o próprio admin na lista (para não se apagar a si mesmo)
        $query->where('id', '!=', Auth::id());

        $users = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.users', compact('users'));
    }

    // US50: Delete User Account (Admin Action)
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Impedir apagar outros admins (opcional, mas seguro)
        if ($user->isAdmin()) {
            return back()->withErrors(['msg' => 'Cannot delete another administrator via this panel.']);
        }

        try {
            // Reutilizar a lógica robusta de anonimização (Hard Delete + Nullify)
            DB::transaction(function () use ($user) {
                DB::table('review')->where('user_id', $user->id)->update(['user_id' => null]);
                DB::table('reply')->where('user_id', $user->id)->update(['user_id' => null]);
                DB::table('reservation')->where('user_id', $user->id)->update(['user_id' => null]);
                DB::table('waitlist')->where('user_id', $user->id)->update(['user_id' => null]);
                DB::table('notification')->where('user_id', $user->id)->update(['user_id' => null]);
                
                DB::table('favourite')->where('user_id', $user->id)->delete();
                DB::table('customer')->where('id', $user->id)->delete();
                DB::table('owner')->where('id', $user->id)->delete();
                
                // Finalmente apaga o user
                DB::table('user')->where('id', $user->id)->delete();
            });

            return back()->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Error deleting user: ' . $e->getMessage()]);
        }
    }
}
