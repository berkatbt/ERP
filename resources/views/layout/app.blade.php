<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ERP System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-slate-950 text-slate-100">

<!-- BACKGROUND -->
<div class="fixed inset-0 -z-10">
    <img src="/img/wallpaper_login.png" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-slate-950/80"></div>
</div>

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-72 bg-slate-900/95 backdrop-blur-xl border-r border-slate-800 flex flex-col">

        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800">
            <img src="/img/B_isnis.png" alt="Logo" class="w-40 h-12 mr-3">
        </div>

        @auth
        @php
            $role = strtolower(Auth::user()->role ?? '');
            $roleLabelMap = [
                'owner' => 'Owner',
                'manager' => 'Manager',
                'finance' => 'Finance Admin',
                'warehouse' => 'Warehouse Admin',
                'cashier' => 'Cashier',
            ];
            $roleLabel = $roleLabelMap[$role] ?? ucfirst($role);
            $userInitial = strtoupper(substr(Auth::user()->name ?? '', 0, 1));
        @endphp

        <!-- MENU -->
        <nav class="flex-1 p-4 space-y-2 text-sm">

            <a href="/{{ $role }}"
               class="flex items-center gap-3 px-4 py-3 rounded-3xl bg-slate-800 text-white font-semibold shadow-sm shadow-slate-900/40 transition hover:bg-slate-700">
                Dashboard
            </a>

            <a href="#" class="flex items-center px-4 py-3 rounded-3xl  hover:bg-slate-800 hover:text-white transition">
                <span class="material-symbols-outlined"> view_in_ar </span><span class="ml-3 text-ms">Produk</span>
            </a>

            @unless($role === 'cashier')
                <a href="#" class="flex items-center px-4 py-3 rounded-3xl text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <span class="material-symbols-outlined">remove_shopping_cart</span><span class="ml-3 text-ms">Pembelian</span>
                </a>
            @endunless

            <a href="#" class="flex items-center px-4 py-3 rounded-3xl text-slate-300 hover:bg-slate-800 hover:text-white transition">
                <span class="material-symbols-outlined">shopping_bag</span><span class="ml-3 text-ms">Penjualan</span>
            </a>

            @if(in_array($role, ['owner', 'manager', 'warehouse']))
                <a href="#" class="flex items-center px-4 py-3 rounded-3xl text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <span class="material-symbols-outlined">view_comfy_alt</span><span class="ml-3 text-ms">Gudang</span>
                </a>
            @endif

            @if(in_array($role, ['owner', 'manager', 'finance']))
                <a href="#" class="flex items-center px-4 py-3 rounded-3xl text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <span class="material-symbols-outlined">payments</span><span class="ml-3 text-ms">Keuangan</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 rounded-3xl text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <span class="material-symbols-outlined">description</span><span class="ml-3 text-ms">Laporan</span>
                </a>
            @endif

            @if(in_array($role, ['owner', 'manager']))
                <h3 class="mt-4 px-4 text-xs tracking-[0.2em] text-slate-500 uppercase">Admin</h3>
                <a href="{{ route('audit.log') }}" class="flex items-center px-4 py-3 rounded-3xl text-slate-300 hover:bg-slate-800 hover:text-white transition">Audit Log</a>
                <a href="#" class="flex items-center px-4 py-3 rounded-3xl text-slate-300 hover:bg-slate-800 hover:text-white transition">Approval</a>
                <a href="{{ route('branches.index') }}" class="flex items-center px-4 py-3 rounded-3xl text-slate-300 hover:bg-slate-800 hover:text-white transition">data perusahaan</a>
            @endif
        </nav>

        <!-- PROFILE -->
        <div class="m-4 rounded-3xl bg-slate-950/70 p-4 border border-slate-800 shadow-inner shadow-slate-950/40">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center text-lg font-semibold">
                    {{ $userInitial ?: 'U' }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-400">{{ $roleLabel }}</p>
                </div>
            </div>
        </div>

        @endauth

    </aside>


    <!-- MAIN -->
    <div class="flex-1 flex flex-col">

        <!-- NAVBAR -->
        <header class="h-16 
                       bg-white/10 backdrop-blur-xl 
                       border-b border-white/20 
                       flex items-center justify-between px-6">

            <!-- LEFT -->
            <div>
                <h2 class="text-lg font-semibold text-white">Dashboard</h2>
                <p class="text-xs text-gray-300">Overview sistem ERP</p>
            </div>

            <!-- RIGHT -->
            <div class="flex items-center gap-4">

                <!-- NOTIF -->
                <div class="relative cursor-pointer">
                   <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-1 rounded-full">
                        2
                    </span>
                </div>

                <!-- LOGOUT -->
                <form method="POST" action="/logout">
                    @csrf
                    <button class="text-sm text-red-300 hover:text-red-500 transition">
                        Logout
                    </button>
                </form>

            </div>

        </header>

        <!-- CONTENT -->
        <main class="flex-1 p-6 overflow-y-auto">

            <div class="bg-white/10 backdrop-blur-xl 
                        border border-white/20 
                        rounded-2xl shadow-xl p-6">

                @yield('content')

            </div>

        </main>

    </div>

</div>

</body>
</html>