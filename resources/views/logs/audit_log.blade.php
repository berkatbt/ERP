@extends('layout.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">Audit Log Login & Logout</h1>
            <p class="text-sm text-slate-300">Riwayat aktivitas login dan logout untuk role Owner dan Manager.</p>
        </div>
        <div class="rounded-3xl bg-slate-900/80 border border-slate-800 px-4 py-3 text-sm text-slate-300">
            Total log: {{ $logs->total() }}</div>
    </div>

    <div class="overflow-x-auto rounded-3xl border border-slate-800 bg-slate-950/80 shadow-xl shadow-slate-950/20">
        <table class="min-w-full table-auto text-left">
            <thead class="bg-slate-900/90 text-xs uppercase tracking-wider text-slate-400">
                <tr>
                    <th class="px-4 py-3">Waktu</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Aksi</th>
                    <th class="px-4 py-3">IP</th>
                    <th class="px-4 py-3">URL</th>
                    <th class="px-4 py-3">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-sm text-slate-200">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-900/80 transition">
                        <td class="px-4 py-3">{{ $log->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ optional($log->user)->name ?? 'System' }}</td>
                        <td class="px-4 py-3">{{ optional($log->user)->role->name ? ucfirst(optional($log->user)->role->name) : '-' }}</td>
                        <td class="px-4 py-3 capitalize">{{ $log->action }}</td>
                        <td class="px-4 py-3">{{ $log->ip_address ?? '-' }}</td>
                        <td class="px-4 py-3 break-words">{{ $log->url ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <details class="cursor-pointer">
                                <summary class="text-slate-300 hover:text-white">Lihat</summary>
                                <div class="mt-2 space-y-2 text-xs text-slate-400">
                                    @if($log->description)
                                        <div><strong>Deskripsi:</strong> {{ $log->description }}</div>
                                    @endif
                                    <div><strong>User Agent:</strong> {{ $log->user_agent ?? '-' }}</div>
                                </div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-slate-400">Belum ada data audit log.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
