@php
/** @var \App\Models\Setting $site */
@endphp
@if($site)
<section id="site-introduction" class="py-10">
    <!-- 核心标题与介绍 -->
    <div class="mx-auto">
        <div class="mb-6 flex items-center gap-3">
            <span class="text-xs text-zinc-500 uppercase tracking-wider">Intro</span>
            <div class="h-[1px] flex-1 bg-zinc-200"></div>
        </div>

        <h1 class="text-4xl font-light text-zinc-900 mb-8">
            {{ $site->site_name }}
            <span class="block text-xl text-zinc-400 font-normal mt-2">
                {{ $site->seo_title ?? 'The Digital Garden' }}
            </span>
        </h1>

        <!-- 站点标语 -->
        <p class="text-lg text-zinc-600 mb-8 max-w-xl">
            {{ $site->site_slogan ?? '专注记录技术思考与生活瞬间。' }}
        </p>

        <!-- 社交链接 -->
        @php
        $socialLinks = [
        'social_github' => 'Github',
        'social_twitter' => 'Twitter',
        'social_instagram' => 'Instagram'
        ];
        @endphp
        @if(count(array_filter(array_map(fn($key) => $site->$key, array_keys($socialLinks)))))
        <div class="flex flex-wrap gap-6 mb-10">
            @foreach($socialLinks as $key => $label)
            @if($site->$key)
            <a href="{{ $site->$key }}" target="_blank" class="text-sm text-zinc-500 hover:text-black transition-colors">
                {{ $label }}
            </a>
            @endif
            @endforeach
        </div>
        @endif

        <!-- 站点状态 -->
        <div class="flex items-center gap-2 text-sm">
            <span class="w-2 h-2 rounded-full {{ $site->maintenance_mode ? 'bg-orange-400' : 'bg-emerald-500' }}"></span>
            <span class="text-zinc-800 font-medium">
                {{ $site->maintenance_mode ? 'Under Maintenance' : 'Online' }}
            </span>
        </div>
    </div>

    <!-- 登录后编辑按钮 -->
    @auth
    <div class="mt-8 text-right">
        <button onclick="toggleSettingsModal()" class="px-4 py-2 bg-black text-white text-xs uppercase tracking-wider hover:bg-zinc-800 transition">
            Edit Identity
        </button>
    </div>
    @endauth
</section>
@endif
