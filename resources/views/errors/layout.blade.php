<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - SMP Kristen 1 Wonosobo</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-surface-light text-slate-800">
    <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-12 sm:px-6">
        <!-- Background Ornaments -->
        <div class="pointer-events-none absolute -top-40 -left-40 h-96 w-96 rounded-full bg-primary-200/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 -right-40 h-96 w-96 rounded-full bg-secondary-200/40 blur-3xl"></div>

        <!-- School Brand Header -->
        <div class="mb-6 flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SMP Kristen 1 Wonosobo" class="h-10 w-10 shrink-0 object-contain drop-shadow-sm" />
            <div>
                <h2 class="text-sm font-bold tracking-wide text-primary-950 uppercase">SMP Kristen 1 Wonosobo</h2>
                <p class="text-2xs font-medium text-slate-500">Sistem Informasi Manajemen Siswa</p>
            </div>
        </div>

        <!-- Error Card -->
        <div class="relative z-10 w-full max-w-lg rounded-2xl border border-border-subtle bg-white/95 p-8 text-center shadow-xl backdrop-blur-xs sm:p-10">
            <div class="mb-4 flex justify-center">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-primary-200 bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-primary-500"></span>
                    @yield('badge')
                </span>
            </div>

            <div class="relative my-2 select-none">
                <span class="text-7xl font-extrabold tracking-tighter text-transparent bg-clip-text bg-linear-to-br from-primary-900 via-primary-700 to-secondary-500 sm:text-8xl">
                    @yield('code')
                </span>
            </div>

            <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">@yield('title')</h1>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">@yield('message')</p>

            @auth
            <div class="mt-5 rounded-lg border border-primary-100 bg-primary-50/60 p-3 text-xs text-primary-900">
                Masuk sebagai: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->username }})
            </div>
            @endauth

            <div class="mt-7 flex flex-col-reverse items-center justify-center gap-3 sm:flex-row">
                <button type="button" onclick="window.history.back()" class="cursor-pointer inline-flex items-center justify-center gap-2 rounded-lg border border-border-subtle bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-surface-subtle">
                    Kembali
                </button>

                @auth
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-xs transition-colors hover:bg-primary-700">
                    Ke Dashboard
                </a>
                @else
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-xs transition-colors hover:bg-primary-700">
                    Masuk ke Sistem
                </a>
                @endauth
            </div>
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            Butuh bantuan? Hubungi staf Tata Usaha SMP Kristen 1 Wonosobo.
        </p>
    </div>
</body>
</html>
