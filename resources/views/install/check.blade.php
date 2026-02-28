<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('install.title') }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .font-display {
            font-family: serif;
        }
    </style>
</head>

<body class="bg-white text-zinc-900 selection:bg-zinc-100 antialiased font-sans">
    <div class="max-w-2xl mx-auto py-20 px-6 font-light">
        <header class="mb-16 flex justify-between items-start">
            <div>
                <span class="text-[10px] font-mono text-zinc-300 uppercase tracking-[0.5em]">{{ __('install.phase') }}</span>
                <h1 class="text-4xl mt-4 font-display italic">{{ __('install.header') }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] text-zinc-400 uppercase tracking-widest">{{ __('install.select_language') }}</span>
                <select id="language-select" class="text-xs border border-zinc-200 rounded px-2 py-1 outline-none focus:border-black">
                    <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                    <option value="zh_CN" {{ app()->getLocale() === 'zh_CN' ? 'selected' : '' }}>简体中文</option>
                </select>
            </div>
        </header>

        <div class="space-y-12">
            <div class="flex justify-between items-center border-b border-zinc-50 pb-4">
                <span class="text-sm text-zinc-600 uppercase tracking-widest">{{ __('install.php_version', ['version' => $requirements['php']['current']]) }}</span>
                @if($requirements['php']['status'])
                <span class="text-[9px] bg-emerald-500 text-white px-2 py-1 uppercase">{{ __('install.passed') }}</span>
                @else
                <span class="text-[9px] bg-red-500 text-white px-2 py-1 uppercase">{{ __('install.failed') }}</span>
                @endif
            </div>

            <section>
                <h3 class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest mb-6">{{ __('install.required_extensions') }}</h3>
                <div class="grid grid-cols-2 gap-y-4 gap-x-12">
                    @foreach($requirements['extensions'] as $ext => $status)
                    <div class="flex justify-between items-center text-[11px] uppercase tracking-tighter">
                        <span class="{{ $status ? 'text-zinc-900' : 'text-zinc-300' }}">{{ $ext }}</span>
                        <span class="{{ $status ? 'text-emerald-500' : 'text-red-500' }}">
                            {{ $status ? '●' : '○' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </section>

            @if(config('queue.default') === 'redis')
            <section>
                <h3 class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest mb-6">Redis Check</h3>
                <div class="flex justify-between items-center text-[11px] uppercase tracking-tighter">
                    <span class="{{ $requirements['redis']['status'] ? 'text-zinc-900' : 'text-zinc-300' }}">Redis Connection</span>
                    <span class="{{ $requirements['redis']['status'] ? 'text-emerald-500' : 'text-red-500' }}">
                        {{ $requirements['redis']['status'] ? '●' : '○' }}
                    </span>
                </div>
            </section>
            @endif

            <section>
                <h3 class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest mb-6">{{ __('install.write_permissions') }}</h3>
                @foreach($requirements['permissions'] as $path => $status)
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[11px] text-zinc-500 font-mono">{{ $path }}</span>
                    <span class="text-[9px] {{ $status ? 'text-emerald-500' : 'text-red-400 underline' }}">
                        {{ $status ? __('install.writable') : __('install.read_only') }}
                    </span>
                </div>
                @endforeach
            </section>

            <div class="pt-10 space-y-4">
                @if($allPassed)
                <a href="{{ route('install.setup', ['lang' => app()->getLocale()]) }}" class="block w-full bg-black text-white py-4 text-center text-[10px] tracking-[0.4em] uppercase hover:bg-zinc-800 transition">
                    {{ __('install.next_step') }}
                </a>
                @else
                <button onclick="location.reload()" class="block w-full border border-zinc-200 text-zinc-400 py-4 text-center text-[10px] tracking-[0.4em] uppercase hover:border-zinc-400 hover:text-zinc-600 transition">
                    {{ __('install.check_again') }}
                </button>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.getElementById('language-select').addEventListener('change', function() {
            const lang = this.value;
            const url = new URL(window.location.href);
            url.searchParams.set('lang', lang);
            window.location.href = url.toString();
        });
    </script>
</body>

</html>
