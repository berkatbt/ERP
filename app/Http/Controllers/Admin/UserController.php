<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Expectation;

class UserController extends Controller
{
    public function index() {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role->name), ['owner', 'manager'])) {
            abort(403, 'Akses tidak diizinkan.');
        }
        
        $users = User::all();
        $roles = Role::all();
        $branches = Branch::all();
    
        return view('user.index', compact('users', 'roles', 'branches'));
    }
    public function store(Request $request) {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role->name), ['owner', 'manager'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        try {
            $validation = $request->validate([
                'name' => 'required|string',
                'password' => 'required|string',
                'email' => 'required|email',
                'role_id' => 'required',
                'branch_id' => 'required'
            ]);

            if ($request->role_id === 'Owner') {
                return back()->with('error', 'User dengan role Owner sudah ada');
            }
    
            $user = User::create($validation);
            $userName = $user->name;

            return back()->with('success', 'User '.$userName.' berhasil dibuat');
        } catch (Expectation $e) {
            return back()->withInput()->with('error', $e);
        }
    }

    public function update(Request $request, $id) {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role->name), ['owner', 'manager'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        try {
            $validation = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'role_id' => 'required',
                'branch_id' => 'required'
            ]);
    
            $user = User::findOrFail($id);
            $userName = $user->name;

            if ($request->role_id === 'Owner') {
                return back()->with('error', 'User dengan role Owner sudah ada');
            }

            $user->update([
                'name' => $request->name ?? $user->name,
                'email' => $request->email ?? $user->email,
                'role_id' => $request->role_id ?? $user->role_id,
                'branch_id' => $request->branch_id ?? $user->branch_id,
            ]);

            return back()->with('success', 'User '.$userName.' berhasil diupdate');
        } catch (Expectation $e) {
            return back()->withInput()->with('error', $e);
        }
    }
    public function destroy($id) {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role->name), ['owner', 'manager'])) {
            abort(403, 'Akses tidak diizinkan.');
        }
        
        try {
            $user = User::findOrFail($id);
            $namaUser = $user->name;
            $user->delete();

            return back()->with('success', 'User '. $namaUser .' berhasil di hapus');
        } catch (Expectation $e) {
            return back()->withInput()->with('error', $e);
        }
    }
}