<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Mockery\Expectation;

class RoleController extends Controller
{
    public function index() {
        $roles = Role::all();
    
        return view('role.index', compact('roles'));
    }
    public function store(Request $request) {
        try {
            $validation = $request->validate([
                'name' => 'required|string'
            ]);
    
            Role::create($validation);
            $newRole = $request->name;

            return back()->with('success', 'Role '.$newRole.' berhasil dibuat');
        } catch (Expectation $e) {
            return back()->withInput()->with('error', $e);
        }
    }
    public function update(Request $request, $id) {
        try {
            $validation = $request->validate([
                'name' => 'required|string'
            ]);
    
            $role = Role::findOrFail($id);
            $oldName = $role->name;
            $newName = $request->name;

            $role->update([
                'name' => $request->name ?? $role->name
            ]);

            return back()->with('success', 'Role '.$oldName.' berhasil diupdate menjadi '.$newName);
        } catch (Expectation $e) {
            return back()->withInput()->with('error', $e);
        }
    }
    public function destroy($id) {
        try {
            $role = Role::findOrFail($id);
            $namaRole = $role->name;
            $role->delete();

            return back()->with('success', 'Role '. $namaRole .' berhasil di hapus');
        } catch (Expectation $e) {
            return back()->withInput()->with('error', $e);
        }
    }
}
