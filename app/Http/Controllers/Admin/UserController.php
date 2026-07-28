<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CoursePurchase;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['purchases' => function ($q) {
                $q->where('status', 'paid');
            }])
            ->where('role', 'user')
            ->latest()
            ->paginate(15);

        $stats = [
            'total'      => User::where('role', 'user')->count(),
            'active'     => User::where('role', 'user')->whereNull('banned_at')->count(),
            'banned'     => User::where('role', 'user')->whereNotNull('banned_at')->count(),
            'with_courses' => User::where('role', 'user')->whereHas('purchases', function ($q) {
                $q->where('status', 'paid');
            })->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function ban(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('success', 'Cannot ban an admin user.');
        }

        if ($user->banned_at) {
            return redirect()->route('admin.users.index')
                ->with('success', 'This user is already banned.');
        }

        $user->update(['banned_at' => now()]);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' has been banned.");
    }

    public function unban(User $user)
    {
        if (!$user->banned_at) {
            return redirect()->route('admin.users.index')
                ->with('success', 'This user is not banned.');
        }

        $user->update(['banned_at' => null]);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' has been unbanned.");
    }
}