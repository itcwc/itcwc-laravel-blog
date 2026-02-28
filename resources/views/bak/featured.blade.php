@if($site)
<section id="site-introduction" class="group relative overflow-hidden">
    <div class="absolute -left-10 -top-10 select-none pointer-events-none opacity-[0.03] transition-transform duration-1000 group-hover:-translate-y-4">
        <h1 class="text-[20rem] font-black leading-none">00</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-12 relative z-10">

        <div class="md:col-span-9">
            <div class="flex items-center gap-4 mb-10">
                <span class="text-[10px] font-mono text-zinc-400 uppercase tracking-[0.5em]">Intro</span>
                <div class="h-[1px] w-12 bg-zinc-200"></div>
            </div>

            <h1 class="text-5xl md:text-6xl font-light tracking-tighter leading-[1.05] text-zinc-900">
                {{ $site->site_name }}<br>
                <span class="text-zinc-300 font-serif ml-0 md:ml-12">
                    {{ $site->seo_title ?? 'The Digital Garden' }}
                </span>
            </h1>

            <div class="mt-12 md:ml-12 max-w-xl">
                <p class="text-xl text-zinc-500 font-light leading-relaxed">
                    {{ $site->site_slogan ?? '专注记录技术思考与生活瞬间。' }}
                </p>

                <div class="mt-12 flex flex-wrap gap-x-10 gap-y-4">
                    @foreach(['social_github' => 'Github', 'social_twitter' => 'Twitter', 'social_instagram' => 'Instagram'] as $key => $label)
                    @if($site->$key)
                    <a href="{{ $site->$key }}" target="_blank" class="text-[10px] font-mono text-zinc-400 uppercase tracking-[0.3em] hover:text-black transition-colors relative group/link">
                        {{ $label }}
                        <span class="absolute -bottom-1 left-0 w-0 h-[1px] bg-black transition-all group-hover/link:w-full"></span>
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="md:col-span-3 flex flex-col justify-between border-l border-zinc-50 pl-8 hidden md:flex">
            <div class="space-y-12">
                <div class="rotate-180 [writing-mode:vertical-lr] text-[9px] font-mono text-zinc-200 uppercase tracking-[1em] py-4">
                    Minimalist Interface
                </div>

                <div class="space-y-4">
                    <label class="block text-[10px] font-mono text-zinc-300 uppercase tracking-widest">Index</label>
                    <ul class="text-[11px] text-zinc-400 space-y-2 font-light italic">
                        <li>— {{ date('Y') }} Edition</li>
                        <li>— v2.0.4 stable</li>
                        <li>— Open Source</li>
                    </ul>
                </div>
            </div>

            <div class="pb-2 mt-4">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full {{ $site->maintenance_mode ? 'bg-orange-300' : 'bg-emerald-400' }}"></span>
                    <span class="text-[10px] text-zinc-900 uppercase tracking-widest font-medium">
                        {{ $site->maintenance_mode ? 'Restoring' : 'Online' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @auth
    <div class="absolute top-0 right-0 opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-x-4 group-hover:translate-x-0">
        <button onclick="toggleSettingsModal()" class="px-6 py-2 bg-black text-[9px] text-white tracking-[0.4em] uppercase hover:bg-zinc-800 transition shadow-2xl">
            Identity
        </button>
    </div>
    @endauth
</section>
@endif
