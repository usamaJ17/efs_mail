<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function getAdminDashboard(Request $request)
    {
        $query = User::WithoutSuperAdmin();

        if ($request->has('q')) {
            $query->where('name', 'like', "%$request->q%")
                ->orWhere('email', 'like', "%$request->q%");
        }

        $totalNoOfUsers = User::count();
        $totalNoOfEmailServed = Email::latest()->first()?->id;
        $users = $query->paginate();

        return Inertia::render('Dashboard', compact([
            'totalNoOfUsers',
            'totalNoOfEmailServed',
            'users'
        ]));
    }

    public function toggleAdminStatus(User $user)
    {
        if ($user->id == 1) abort('400', 'SuperAdmin cant be updated');

        if ($user->is_admin) {
            $user->update([
                'is_admin' => false
            ]);
        } else {
            $user->update([
                'is_admin' => true
            ]);
        }

        return back()->with('message', 'Admin status changed successfully!');
    }
}
