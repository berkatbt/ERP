@extends('layout.app')

@section('content')
<div>

    <div class="mb-4">
        <h1 class="text-2xl font-bold">Role Management</h1>
        <p class="text-sm text-slate-400">Kelola role dan hak akses user.</p>
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
            <h2 class="text-lg font-semibold">Daftar Role</h2>

            <button onclick="openCreateModal()" 
                class="rounded-3xl bg-blue-500 px-6 py-3 text-sm font-semibold hover:bg-blue-400 transition">
                Tambah Role
            </button>
        </div>

        <div class="rounded-3xl bg-slate-950 border border-slate-800 p-6">
            <table class="min-w-full text-sm text-slate-200">
                <thead class="border-b border-slate-800 text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Nama Role</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($roles as $role)
                        <tr class="hover:bg-slate-900">
                            <td class="px-4 py-3">{{ $role->name }}</td>

                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center gap-2">

                                    {{-- EDIT --}}
                                    <button onclick='openEditModal(@json($role))'
                                        class="bg-amber-500/20 px-3 py-1 rounded-lg">
                                        Edit
                                    </button>

                                    {{-- DELETE --}}
                                    <button onclick="openDeleteModal({{ $role->id }}, '{{ $role->name }}')"
                                        class="bg-rose-500/20 px-3 py-1 rounded-lg">
                                        Hapus
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-6 text-slate-500">
                                Belum ada role.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

{{-- Modal Create --}}
<div id="createModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-950 p-6 rounded-3xl w-full max-w-md">

        <h2 class="text-lg mb-4">Tambah Role</h2>

        <form method="POST" action="{{ route('admin.role.store') }}">
            @csrf

            <input type="text" name="name" placeholder="Nama Role"
                class="w-full mb-4 px-4 py-2 rounded-3xl bg-slate-900 border border-slate-700"
                required>

            <div class="flex gap-2 mt-3">
                <button class="flex-1 bg-blue-500 py-2 rounded-3xl">Simpan</button>
                <button type="button" onclick="closeCreateModal()" class="flex-1 border py-2 rounded-3xl">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-slate-950 p-6 rounded-3xl w-full max-w-md">

        <h2 class="text-lg mb-4">Edit Role</h2>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <input type="text" name="name" id="editName"
                class="w-full mb-4 px-4 py-2 rounded-3xl bg-slate-900 border border-slate-700"
                required>

            <div class="flex gap-2 mt-3">
                <button class="flex-1 bg-blue-500 py-2 rounded-3xl">Update</button>
                <button type="button" onclick="closeEditModal()" class="flex-1 border py-2 rounded-3xl">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Delete --}}
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
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
// CREATE
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}
function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

// EDIT
function openEditModal(role) {
    document.getElementById('editModal').classList.remove('hidden');

    document.getElementById('editName').value = role.name;
    document.getElementById('editForm').action = `/admin/role/update/${role.id}`;
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// DELETE
let deleteId = null;

function openDeleteModal(id, name) {
    deleteId = id;

    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteText').innerHTML =
        `Yakin hapus role <b>${name}</b>?`;
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function confirmDelete() {
    let form = document.getElementById('deleteForm');
    form.action = `/admin/role/delete/${deleteId}`;
    form.submit();
}

// NOTIF AUTO HIDE
const notif = document.getElementById('Notification');
if (notif) {
    setTimeout(() => notif.style.display = 'none', 2000);
}
</script>