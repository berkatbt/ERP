@extends('layout.app')

@section('content')
<div>

    <div class="mb-4">
        <h1 class="text-2xl font-bold">User Management</h1>
        <p class="text-sm text-slate-400">Kelola data user dan role.</p>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div id="Notification" class="rounded-3xl bg-emerald-500/10 border border-emerald-400 text-emerald-200 p-4 transition-opacity duration-500">
            {{ session('success') }}
        </div>
    @endif
    {{-- ERROR MESSAGE --}}
    @if(session('error'))
        <div id="Notification" class="rounded-3xl bg-emerald-500/10 border border-emerald-400 text-emerald-200 p-4 transition-opacity duration-500">
            {{ session('error') }}
        </div>
    @endif

    <div class="text-white shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Daftar User</h2>

            <button onclick="openCreateModal()" 
                class="rounded-3xl bg-blue-500 px-6 py-3 text-sm font-semibold hover:bg-blue-400 transition">
                Tambah User
            </button>
        </div>

        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6">
            <table class="min-w-full text-sm text-slate-200">
                <thead class="border-b border-slate-800 text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Cabang</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-900">
                            <td class="px-4 py-3">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ $user->role->name }}</td>
                            <td class="px-4 py-3">{{ $user->branch->name }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">

                                    {{-- EDIT --}}
                                    <button onclick='openEditModal(@json($user))'
                                        class="bg-amber-500/20 px-3 py-1 rounded-lg">
                                        Edit
                                    </button>

                                    {{-- DELETE --}}
                                    <button onclick="openDeleteModal({{ $user->id }}, '{{ $user->name }}')"
                                        class="bg-rose-500/20 px-3 py-1 rounded-lg">
                                        Hapus
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>


{{-- Modal Create --}}
<div id="createModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-slate-950 p-6 rounded-3xl w-full max-w-md">

        <h2 class="text-lg mb-4">Tambah User</h2>

        <form method="POST" action="{{ route('admin.user.store') }}">
            @csrf

            <input type="text" name="name" placeholder="Nama"
                class="w-full mb-3 px-4 py-2 bg-slate-900 border border-slate-700 rounded-3xl">

            <input type="email" name="email" placeholder="Email"
                class="w-full mb-3 px-4 py-2 bg-slate-900 border border-slate-700 rounded-3xl">

            <input type="password" name="password" placeholder="Password"
                class="w-full mb-3 px-4 py-2 bg-slate-900 border border-slate-700 rounded-3xl">

            <select name="role_id" class="w-full mb-3 px-4 py-2 bg-slate-900 border border-slate-700 rounded-3xl">
                @foreach($roles as $role)
                    @if ($role->name !== 'Owner')
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endif
                @endforeach
            </select>
            <select name="branch_id" class="w-full mb-3 px-4 py-2 bg-slate-900 border border-slate-700 rounded-3xl">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>

            <div class="flex gap-2 mt-3">
                <button class="flex-1 bg-blue-500 py-2 rounded-3xl">Simpan</button>
                <button type="button" onclick="closeCreateModal()" class="flex-1 border py-2 rounded-3xl">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-slate-950 p-6 rounded-3xl w-full max-w-md">

        <h2 class="text-lg mb-4">Edit User</h2>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <input type="text" name="name" id="editName"
                class="w-full mb-3 px-4 py-2 rounded-3xl bg-slate-900 border border-slate-700">

            <input type="email" name="email" id="editEmail"
                class="w-full mb-3 px-4 py-2 rounded-3xl bg-slate-900 border border-slate-700">

            <select name="role_id" id="editRole"
                class="w-full mb-3 px-4 py-2 rounded-3xl bg-slate-900 border border-slate-700">
                @foreach($roles as $role)
                    @if ($role->name !== 'Owner')
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endif
                @endforeach
            </select>
            <select name="branch_id" id="editBranch" class="w-full mb-3 px-4 py-2 rounded-3xl bg-slate-900 border border-slate-700">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>

            <div class="flex gap-2 mt-3">
                <button class="flex-1 bg-blue-500 py-2 rounded-3xl">Update</button>
                <button type="button" onclick="closeEditModal()" class="flex-1 border py-2 rounded-3xl">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Delete --}}
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center">
    <div class="bg-slate-950 p-6 rounded-3xl w-full max-w-md">

        <h2 class="mb-4">Konfirmasi</h2>
        <p id="deleteText"></p>

        <div class="flex gap-2 mt-4">
            <button onclick="confirmDelete()" class="flex-1 bg-rose-500 py-2 rounded">Hapus</button>
            <button onclick="closeDeleteModal()" class="flex-1 border py-2 rounded">Batal</button>
        </div>

        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

function openEditModal(user) {
    document.getElementById('editModal').classList.remove('hidden');

    document.getElementById('editName').value = user.name;
    document.getElementById('editEmail').value = user.email;
    document.getElementById('editRole').value = user.role_id;
    document.getElementById('editBranch').value = user.branch_id;

    document.getElementById('editForm').action = `/admin/user/update/${user.id}`;
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

let deleteId = null;

function openDeleteModal(id, name) {
    deleteId = id;

    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteText').innerHTML = 
        `Yakin hapus user <b>${name}</b>?`;
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function confirmDelete() {
    let form = document.getElementById('deleteForm');
    form.action = `/admin/user/delete/${deleteId}`;
    form.submit();
}

// auto hide notif
const notif = document.getElementById('Notification');
if (notif) {
    setTimeout(() => notif.style.display = 'none', 2000);
}
</script>