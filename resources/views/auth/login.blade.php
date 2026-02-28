<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Entry | {{ $site->site_name }}
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white min-h-screen flex items-center justify-center">

    <div class="w-full max-w-xs px-6">
        <header class="mb-12 text-center">
            <h1 class="text-[10px] tracking-[0.5em] uppercase text-zinc-300">Identity Verify</h1>
        </header>

        <form method="POST" action="{{ route('login') }}" class="space-y-8">
            @csrf
            <div>
                <input type="email" name="email" placeholder="Email" required
                    class="w-full border-0 border-b border-zinc-100 py-2 focus:ring-0 focus:border-zinc-900 transition-colors placeholder-zinc-200 outline-none text-sm font-light">
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required
                    class="w-full border-0 border-b border-zinc-100 py-2 focus:ring-0 focus:border-zinc-900 transition-colors placeholder-zinc-200 outline-none text-sm font-light">
            </div>

            @if ($errors->any())
            <p class="text-[10px] text-red-400 italic font-light">{{ $errors->first() }}</p>
            @endif

            <div class="pt-4">
                <button type="submit" class="w-full py-3 bg-zinc-900 text-white text-[10px] tracking-[0.3em] uppercase hover:bg-zinc-800 transition">
                    Enter System
                </button>
            </div>
        </form>

        <footer class="mt-16 text-center">
            <a href="/" class="text-[9px] text-zinc-300 hover:text-zinc-900 transition uppercase tracking-widest">← Back to Index</a>
        </footer>
    </div>

</body>

</html>
