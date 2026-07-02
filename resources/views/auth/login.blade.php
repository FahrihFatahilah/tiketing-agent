<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#6366f1">
    <title>Login — Agent Bus</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .gradient-bg { background: linear-gradient(160deg, #6366f1 0%, #8b5cf6 50%, #a78bfa 100%); }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-end sm:items-center justify-center">

    <div class="w-full max-w-md">
        {{-- Logo area --}}
        <div class="text-center px-8 py-10 text-white">
            <div class="w-16 h-16 bg-white/20 rounded-3xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="3" stroke-width="1.5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 10h20M7 19v2M17 19v2"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold">Agent Muliajaya</h1>
            <p class="text-white/70 text-sm mt-1">Sistem Manajemen Penumpang</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-t-3xl sm:rounded-3xl px-6 pt-6 pb-10 shadow-2xl">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Masuk</h2>
            <p class="text-sm text-slate-400 mb-6">Gunakan akun yang telah diberikan</p>

            @if(session('status'))
                <div class="mb-4 bg-emerald-50 text-emerald-700 text-sm px-4 py-3 rounded-2xl border border-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="mt-1 w-full h-12 px-4 text-sm bg-slate-50 border rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition
                                  {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Password</label>
                    <input type="password" name="password" required autocomplete="current-password"
                           class="mt-1 w-full h-12 px-4 text-sm bg-slate-50 border rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition
                                  {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="remember"
                           class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-slate-600">Ingat saya</span>
                </label>

                <button type="submit"
                        class="w-full h-12 bg-indigo-600 text-white text-sm font-bold rounded-2xl hover:bg-indigo-700 active:scale-[.98] transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 mt-2">
                    Masuk
                </button>
            </form>

            <p class="text-center text-xs text-slate-400 mt-6">Internal use only · Agent Bus &copy; {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
