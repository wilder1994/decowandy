<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManageUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::select('id', 'name', 'email', 'role', 'can_operate', 'can_inventory', 'created_at')
            ->orderBy('name')
            ->get();

        $editUser = null;
        if ($request->query('edit')) {
            $editUser = $users->firstWhere('id', (int) $request->query('edit'));
        }

        return view('settings.users', compact('users', 'editUser'));
    }

    public function store(ManageUserRequest $request): RedirectResponse
    {
        $user = new User();
        $user->active = true;
        $user->email_verified_at = now();
        $user->applyAccountSettings($request->accountData());
        $user->save();

        return redirect()->route('settings.users')->with('status', 'Usuario creado: '.$user->name);
    }

    public function update(ManageUserRequest $request, User $user): RedirectResponse
    {
        $user->applyAccountSettings($request->accountData());
        $user->save();

        return redirect()->route('settings.users')->with('status', 'Usuario actualizado: '.$user->name);
    }

    public function destroy(User $user): RedirectResponse
    {
        $name = $user->name;
        $user->delete();

        return redirect()->route('settings.users')->with('status', 'Usuario eliminado: '.$name);
    }
}
