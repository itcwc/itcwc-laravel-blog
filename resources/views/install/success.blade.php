<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>{{ __('success.title') }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
</head>

<body class="bg-white text-zinc-900 selection:bg-zinc-100 antialiased font-sans">
    <div class="max-w-2xl mx-auto py-20 px-6 font-light">
        <div class="absolute -left-10 -top-10 select-none pointer-events-none opacity-[0.02] transform -rotate-12">
            <h1 class="text-[25rem] font-black leading-none">100</h1>
        </div>

        <header class="mb-20 relative z-10">
            <span class="text-[10px] font-mono text-emerald-500 uppercase tracking-[0.5em] mb-4 block">
                {{ __('success.phase_complete') }}
            </span>
            <h1 class="text-5xl md:text-6xl font-display italic leading-tight">
                {{ __('success.header_1') }} <br><span class="text-zinc-300">{{ __('success.header_2') }}</span>
            </h1>
        </header>

        <div class="space-y-16 relative z-10">
            <section class="border-l border-zinc-100 pl-8 space-y-6">
                <h3 class="text-[10px] font-mono text-zinc-400 uppercase tracking-widest">{{ __('success.admin_access') }}</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex justify-between items-baseline border-b border-zinc-50 pb-2">
                        <span class="text-xs text-zinc-400 uppercase">{{ __('success.login_url') }}</span>
                        <span class="text-sm font-mono text-black">{{ url(config('auth.admin_path')) }}</span>
                    </div>
                    <div class="flex justify-between items-baseline border-b border-zinc-50 pb-2">
                        <span class="text-xs text-zinc-400 uppercase">{{ __('success.admin_user') }}</span>
                        <span class="text-sm font-mono text-black">{{ $admin_email }}</span>
                    </div>
                </div>
                <p class="text-[11px] text-zinc-400 italic">
                    {{ __('success.save_login_hint') }}
                </p>
            </section>

            <section class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="group">
                    <h4 class="text-[10px] font-mono uppercase tracking-[0.2em] mb-4">{{ __('success.step_01') }}</h4>
                    <p class="text-sm text-zinc-500 leading-relaxed mb-6">
                        {{ __('success.step_01_desc') }}
                    </p>
                    <a href="{{ url(config('auth.admin_path')) }}" class="text-[10px] uppercase tracking-widest border-b border-black pb-1 hover:text-zinc-400 hover:border-zinc-200 transition">{{ __('success.go_to_dashboard') }}</a>
                </div>
                <div class="group">
                    <h4 class="text-[10px] font-mono uppercase tracking-[0.2em] mb-4">{{ __('success.step_02') }}</h4>
                    <p class="text-sm text-zinc-500 leading-relaxed mb-6">
                        {{ __('success.step_02_desc') }}
                    </p>
                    <a href="/" class="text-[10px] uppercase tracking-widest border-b border-black pb-1 hover:text-zinc-400 hover:border-zinc-200 transition">{{ __('success.visit_homepage') }}</a>
                </div>
            </section>

            <div class="bg-zinc-50 p-6 rounded-sm">
                <div class="flex gap-4 items-start text-zinc-400">
                    <span class="text-xs">{{ __('success.note') }}</span>
                    <p class="text-[11px] leading-relaxed">
                        {{ __('success.lock_hint') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
