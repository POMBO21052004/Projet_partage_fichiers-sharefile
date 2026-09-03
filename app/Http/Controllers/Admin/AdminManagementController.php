<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminManagementController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')->paginate(10);
        $stats = [
            'total' => User::where('role', 'admin')->count(),
            'active' => User::where('role', 'admin')->where('status', 'active')->count(),
            'inactive' => User::where('role', 'admin')->where('status', 'inactive')->count(),
        ];
        return view('admin.admins.index', compact('admins', 'stats'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'status' => 'active',
            'is_verified' => true,
        ]);

        \App\Services\AuditService::log(
            'admin_create',
            'User',
            $admin->id,
            "A créé l'administrateur : {$admin->name} ({$admin->email})"
        );

        return redirect()->route('admin.admins.index')->with('success', 'Administrateur créé avec succès.');
    }

    public function show(User $admin)
    {
        if ($admin->role !== 'admin') abort(403);
        $admin->load('files');
        return view('admin.admins.show', compact('admin'));
    }

    public function edit(User $admin)
    {
        if ($admin->role !== 'admin') abort(403);
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        if ($admin->role !== 'admin') abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();

        \App\Services\AuditService::log(
            'admin_update',
            'User',
            $admin->id,
            "A mis à jour l'administrateur : {$admin->name}"
        );

        return redirect()->route('admin.admins.index')->with('success', 'Administrateur mis à jour avec succès.');
    }

    public function destroy(User $admin)
    {
        if ($admin->role !== 'admin') abort(403);
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        
        \App\Services\AuditService::log(
            'admin_delete',
            'User',
            $admin->id,
            "A supprimé définitivement l'administrateur : {$admin->name}"
        );

        $admin->delete();
        return redirect()->route('admin.admins.index')->with('success', 'Administrateur supprimé.');
    }

    public function toggleStatus(User $admin)
    {
        if ($admin->role !== 'admin') abort(403);
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }
        $admin->status = $admin->status === 'active' ? 'inactive' : 'active';
        $admin->save();

        \App\Services\AuditService::log(
            'admin_toggle_status',
            'User',
            $admin->id,
            "A basculé le statut de l'administrateur '{$admin->name}' à : " . ($admin->status === 'active' ? 'Actif' : 'Inactif')
        );

        return back()->with('success', 'Statut de l\'administrateur mis à jour.');
    }
}
