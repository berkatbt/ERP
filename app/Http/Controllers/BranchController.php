<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role), ['owner', 'manager'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $branches = Branch::orderBy('name')->get();

        return view('branches.index', [
            'branches' => $branches,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user || strtolower($user->role) !== 'owner') {
            abort(403, 'Akses tidak diizinkan.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:branches,email',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:0',
            'office_type' => 'nullable|in:kantor pusat,cabang,gudang,toko,lainnya',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('branches', 'public');
            $data['photo'] = $photoPath;
        }

        Branch::create($data);

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil dibuat.');
    }
}
