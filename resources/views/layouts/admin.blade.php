@php
    $menus = [
        [
            'title' => 'Dashboard',
            'route' => 'admin.dashboard',
            'active' => request()->routeIs('admin.dashboard'),
        ],
        [
            'title' => 'Data Dosen',
            'route' => 'admin.dosen.*',
            'url' => route('admin.dosen.index'),
            'active' => request()->routeIs('admin.dosen.*'),
        ],
        [
            'title' => 'Mata Kuliah',
            'route' => 'admin.mata-kuliah.*',
            'url' => route('admin.mata-kuliah.index'),
            'active' => request()->routeIs('admin.mata-kuliah.*'),
        ],
        [
            'title' => 'Jadwal Mengajar',
            'route' => 'admin.jadwal.*',
            'url' => route('admin.jadwal.index'),
            'active' => request()->routeIs('admin.jadwal.*'),
        ],
        [
            'title' => 'Presensi Dosen',
            'route' => 'admin.presensi.*',
            'url' => route('admin.presensi.index'),
            'active' => request()->routeIs('admin.presensi.*'),
        ],
        [
            'title' => 'Rekap Presensi',
            'route' => 'admin.rekap.*',
            'url' => route('admin.rekap.index'),
            'active' => request()->routeIs('admin.rekap.*'),
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Presensi Dosen' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-900">
    <div class="min-h-screen md:flex">
        <aside class="w-64 bg-slate-950 text-white flex flex-col">

            <div class="border-b border-white/10 p-5">
                <h2 class="text-lg font-bold">
                    Presensi Dosen
                </h2>

                <p class="mt-1 text-sm text-slate-400">
                    {{ session('user.nama') }}
                </p>
            </div>

            <nav class="flex-1 p-3 space-y-1">

                @foreach ($menus as $menu)
                    <a href="{{ $menu['url'] ?? route($menu['route']) }}"
                        class="
                    flex items-center rounded-lg px-4 py-3 text-sm transition
                    {{ $menu['active']
                        ? 'bg-white text-slate-900 font-semibold shadow'
                        : 'text-slate-300 hover:bg-white/10 hover:text-white' }}
               ">
                        {{ $menu['title'] }}
                    </a>
                @endforeach

            </nav>

            <div class="border-t border-white/10 p-3">

                <form method="POST" action="{{ url('/logout') }}">
                    @csrf

                    <button type="submit"
                        class="w-full rounded-lg bg-rose-600 px-4 py-3 text-left text-sm font-medium text-white hover:bg-rose-700">
                        Logout
                    </button>

                </form>

            </div>

        </aside>

        <main class="flex-1">
            <header class="bg-white border-b px-5 py-4">
                <h1 class="text-xl font-semibold">{{ $title ?? 'Admin' }}</h1>
            </header>
            <div class="p-5 space-y-4">
                @if (session('success'))
                    <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                        {{ session('success') }}</div>
                @endif
                @if (session('error') || ($error ?? null))
                    <div class="rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                        {{ session('error') ?? $error }}</div>
                @endif
                @if ($errors->any())
                    <div class="rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                        {{ $errors->first() }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
