<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login — Agent Bus</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=geist:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Geist', system-ui, sans-serif; }</style>
</head>
<body class="min-h-screen bg-zinc-50 flex items-center justify-center p-4">

    <div class="w-full max-w-sm">
        <div class="mb-6 text-center">
            <div class="mx-auto mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-900">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="1.5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 10h20M7 19v2M17 19v2"/>
                </svg>
            </div>
            <h1 class="text-lg font-semibold text-zinc-900">Agent Muliajaya</h1>
            <p class="mt-1 text-sm text-zinc-500">Masuk ke akun Anda</p>
        </div>

        <div class="card p-6">
            @if(session('status'))
                <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-3 py-2 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label class="label" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="input {{ $errors->has('email') ? 'border-red-400 focus-visible:ring-red-400' : '' }}">
                    @error('email')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="label" for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="input {{ $errors->has('password') ? 'border-red-400 focus-visible:ring-red-400' : '' }}">
                    @error('password')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input id="remember" type="checkbox" name="remember"
                           class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                    <label for="remember" class="text-sm text-zinc-600">Ingat saya</label>
                </div>

                <button type="submit" class="btn-default w-full">Masuk</button>
            </form>
        </div>

        <p class="mt-4 text-center text-xs text-zinc-400">Internal use only · Agent Bus &copy; {{ date('Y') }}</p>
    </div>

</body>
</html>
