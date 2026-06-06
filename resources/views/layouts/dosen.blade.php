@php
    $menus = [
        [
            'title' => 'Dashboard',
            'url' => route('dosen.dashboard'),
            'active' => request()->routeIs('dosen.dashboard'),
        ],
        [
            'title' => 'Jadwal Mengajar',
            'url' => route('dosen.jadwal.index'),
            'active' => request()->routeIs('dosen.jadwal.*'),
        ],
        [
            'title' => 'Riwayat Presensi',
            'url' => route('dosen.presensi.riwayat'),
            'active' => request()->routeIs('dosen.presensi.*'),
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dosen Presensi' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-900">
    <div class="min-h-screen md:flex">

        <aside class="flex flex-col bg-emerald-950 text-white md:w-64">
            <div class="border-b border-white/10 px-5 py-5">
                <div class="text-lg font-bold">Portal Dosen</div>
                <div class="mt-1 text-sm text-emerald-100">
                    {{ session('user.nama') }}
                </div>
            </div>

            <nav class="flex-1 space-y-1 p-3 text-sm">
                @foreach ($menus as $menu)
                    <a href="{{ $menu['url'] }}"
                        class="block rounded-md px-3 py-2 transition
                       {{ $menu['active']
                           ? 'bg-white text-emerald-950 font-semibold shadow'
                           : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}">
                        {{ $menu['title'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/10 p-3">
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-md bg-rose-600 px-3 py-2 text-left text-sm font-medium text-white hover:bg-rose-700">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1">
            <header class="border-b bg-white px-5 py-4">
                <h1 class="text-xl font-semibold">
                    {{ $title ?? 'Dosen' }}
                </h1>
            </header>

            <div class="space-y-4 p-5">
                @if (session('success'))
                    <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error') || ($error ?? null))
                    <div class="rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                        {{ session('error') ?? $error }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
                        {{ $errors->first() }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

    </div>
</body>

</html>
