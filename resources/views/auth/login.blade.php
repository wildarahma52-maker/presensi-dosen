<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Presensi Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">
    <div class="bg-white p-8 rounded-lg shadow-sm border w-full max-w-md">
        <h1 class="text-2xl font-bold text-center">Login Presensi Dosen</h1>
        <p class="text-center text-slate-500 mt-1 mb-6">Masuk sebagai admin atau dosen</p>

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="/login" class="space-y-4">
            @csrf

            <input name="username" value="{{ old('username') }}" placeholder="Username" class="w-full border rounded-md px-4 py-2">

            <input name="password" type="password" placeholder="Password" class="w-full border rounded-md px-4 py-2">

            <button class="w-full bg-slate-950 text-white py-2 rounded-md hover:bg-slate-800">
                Login
            </button>
        </form>
    </div>
</body>
</html>
