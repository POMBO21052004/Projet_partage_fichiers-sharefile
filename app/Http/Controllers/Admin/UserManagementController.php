<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')->paginate(10);
        $stats = [
            'total' => User::where('role', 'user')->count(),
            'active' => User::where('role', 'user')->where('status', 'active')->count(),
            'pending' => User::where('role', 'user')->where('is_verified', false)->count(),
        ];
        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active',
            'is_verified' => true,
        ]);

        \App\Services\AuditService::log(
            'user_create',
            'User',
            $user->id,
            "A créé l'utilisateur standard : {$user->name} ({$user->email})"
        );

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        if ($user->role !== 'user') abort(403);
        $user->load(['files', 'sharedFiles']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if ($user->role !== 'user') abort(403);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role !== 'user') abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        \App\Services\AuditService::log(
            'user_update',
            'User',
            $user->id,
            "A mis à jour l'utilisateur standard : {$user->name}"
        );

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        if ($user->role !== 'user') abort(403);
        
        \App\Services\AuditService::log(
            'user_delete',
            'User',
            $user->id,
            "A supprimé définitivement l'utilisateur standard : {$user->name}"
        );

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->role !== 'user') abort(403);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        \App\Services\AuditService::log(
            'user_toggle_status',
            'User',
            $user->id,
            "A basculé le statut de l'utilisateur standard '{$user->name}' à : " . ($user->status === 'active' ? 'Actif' : 'Inactif')
        );

        return back()->with('success', 'Statut de l\'utilisateur mis à jour.');
    }
}
