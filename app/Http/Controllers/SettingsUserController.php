<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\ManageUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
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
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?? null,
            'password' => bcrypt($data['password']),
        ]);

        return redirect()->route('settings.users')->with('status', 'Usuario creado: '.$user->name);
    }

    public function update(ManageUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'] ?? null;
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
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
