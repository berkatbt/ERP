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

    public function show(Branch $branch)
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role), ['owner', 'manager'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        return view('branches.show', [
            'branch' => $branch,
        ]);
    }

    public function detail(Branch $branch)
    {
        $user = Auth::user();

        if (! $user || ! in_array(strtolower($user->role), ['owner', 'manager'])) {
            abort(403, 'Akses tidak diizinkan.');
        }

        return response()->json([
            'id' => $branch->id,
            'name' => $branch->name,
            'email' => $branch->email ?? '-',
            'phone' => $branch->phone ?? '-',
            'address' => $branch->address ?? '-',
            'city' => $branch->city ?? '-',
            'office_type' => ucfirst($branch->office_type) ?? '-',
            'status' => ucfirst($branch->status),
            'status_class' => $branch->status === 'aktif' ? 'emerald' : 'rose',
            'photo' => $branch->photo ? asset('storage/' . $branch->photo) : null,
            'latitude' => $branch->latitude,
            'longitude' => $branch->longitude,
            'radius' => $branch->radius,
            'created_at' => $branch->created_at->format('d M Y H:i'),
            'updated_at' => $branch->updated_at->format('d M Y'),
        ]);
    }

    public function edit(Branch $branch)
    {
        $user = Auth::user();

        if (! $user || strtolower($user->role) !== 'owner') {
            abort(403, 'Akses tidak diizinkan.');
        }

        return response()->json([
            'id' => $branch->id,
            'name' => $branch->name,
            'email' => $branch->email,
            'phone' => $branch->phone,
            'address' => $branch->address,
            'city' => $branch->city,
            'latitude' => $branch->latitude,
            'longitude' => $branch->longitude,
            'radius' => $branch->radius,
            'office_type' => $branch->office_type,
            'status' => $branch->status,
            'photo' => $branch->photo,
        ]);
    }

    public function update(Request $request, Branch $branch)
    {
        $user = Auth::user();

        if (! $user || strtolower($user->role) !== 'owner') {
            abort(403, 'Akses tidak diizinkan.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:branches,email,' . $branch->id,
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
            // Delete old photo if exists
            if ($branch->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($branch->photo);
            }
            $photoPath = $request->file('photo')->store('branches', 'public');
            $data['photo'] = $photoPath;
        }

        $branch->update($data);

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch)
    {
        $user = Auth::user();

        if (! $user || strtolower($user->role) !== 'owner') {
            abort(403, 'Akses tidak diizinkan.');
        }

        $branchName = $branch->name;
        $branch->delete();

        return redirect()->route('branches.index')->with('success', "Cabang '$branchName' berhasil dihapus.");
    }
}
