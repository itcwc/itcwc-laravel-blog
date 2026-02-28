<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', $site->seo_title ?: $site->site_name)
        @if(View::hasSection('title')) | {{ $site->site_name }} @endif
    </title>

    <meta name="description" content="@yield('description', $site->seo_description)">
    <meta name="keywords" content="@yield('keywords', $site->seo_keywords)">
    <link rel="icon" type="image/x-icon" href="{{ $site->site_icon ? asset('storage/'.$site->site_icon) : '/favicon.ico' }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', $site->share_title ?: $site->site_name)">
    <meta property="og:description" content="@yield('description', $site->share_description ?: $site->seo_description)">
    <meta property="og:image" content="@yield('share_image', $site->share_image ? asset('storage/'.$site->share_image) : '')">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', $site->share_title ?: $site->site_name)">
    <meta property="twitter:description" content="@yield('description', $site->share_description ?: $site->seo_description)">

    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        zinc: {
                            50: '#fafafa',
                            300: '#d4d4d4',
                            400: '#a3a3a3',
                            900: '#262626'
                        },
                        border: '#e5e5e5',
                    },
                    fontFamily: {
                        sans: ['Inter', 'PingFang SC', 'Helvetica Neue', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .blog-container { max-width: 720px; }
            .text-balance { text-wrap: balance; }
        }
        body { scroll-behavior: smooth; }
        mark { background-color: #f4f4f5; color: #18181b; font-weight: 500; font-style: normal; }
        .login-out-form {
            margin-bottom: 0.35rem;
        }
    </style>
    @yield('styles')
</head>

<body class="bg-white text-zinc-900 selection:bg-zinc-100 antialiased font-sans">


    @auth
    <div class="sticky top-0 z-[60] bg-black/90 text-white backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-6 h-10 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-[9px] tracking-[0.3em] uppercase opacity-50">Admin Mode</span>
                <div class="h-3 w-[1px] bg-zinc-800"></div>
                <a href="/" class="text-[9px] tracking-[0.2em] uppercase hover:text-zinc-400 transition">Dashboard</a>
            </div>

            <div class="flex items-center gap-6">

                @if(request()->is('projects*'))
                <button onclick="handleProjectEdit()" class="text-[9px] border border-zinc-500 px-2 py-0.5 tracking-[0.2em] uppercase hover:bg-white hover:text-black transition">
                    + New Project
                </button>
                @else
                <button onclick="toggleQuickPost()" class="text-[9px] border border-zinc-500 px-2 py-0.5 tracking-[0.2em] uppercase hover:bg-white hover:text-black transition">
                    + New
                </button>
                @endif

                <button onclick="toggleSettingsModal()" class="text-[9px] tracking-[0.2em] uppercase flex items-center gap-2 hover:text-zinc-400 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Settings
                </button>


                <form action="{{ route('logout') }}" method="POST" class="inline login-out-form">
                    @csrf
                    <button type="submit" class="text-[9px] tracking-[0.2em] uppercase opacity-50 hover:opacity-100">Logout</button>
                </form>
            </div>
        </div>
    </div>
    @endauth

    @include('components.header')
    @include('components.search')

    <!-- 核心内容区 - 缩小间距 -->
    <main class="max-w-6xl mx-auto px-8 pt-32 pb-24">
        @yield('content')
    </main>
    {{-- 只有登录后才注入管理插件 --}}
    @auth
    <div class="fixed bottom-10 right-10 z-[70] flex flex-col gap-4">

        @if(request()->is('projects*'))
        <button onclick="handleProjectEdit()" class="w-12 h-12 bg-black text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </button>
        @else
        <button onclick="toggleQuickPost()" class="w-12 h-12 bg-black text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </button>
        @endif

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-12 h-12 bg-black text-white rounded-full flex items-center justify-center hover:text-red-500 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </button>
        </form>
    </div>

    {{-- 这里放置你的“快捷发布”弹窗组件 --}}
    @include('admin.quick-post-modal')
    @endauth



    @include('components.footer')

    <script>
        // 搜索功能
        const searchOpen = document.getElementById('search-open');
        const searchClose = document.getElementById('search-close');
        const searchOverlay = document.getElementById('search-overlay');
        const searchInput = searchOverlay.querySelector('input');
        const searchResult = document.getElementById('search-result');
        const searchKeyword = document.getElementById('search-keyword');
        const resultCount = document.getElementById('result-count');
        const featuredSentence = document.getElementById('featured-sentence');
        let contentItems = document.querySelectorAll('.content-item');

        // 搜索事件绑定
        searchOpen.addEventListener('click', () => {
            searchOverlay.style.opacity = '1';
            searchOverlay.style.pointerEvents = 'auto';
            setTimeout(() => {
                searchInput.focus();
            }, 100);
        });

        searchClose.addEventListener('click', () => {
            searchOverlay.style.opacity = '0';
            searchOverlay.style.pointerEvents = 'none';
            searchInput.value = '';
        });

        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const keyword = searchInput.value.trim();
                if (keyword) {
                    // 直接跳转到首页，带上搜索参数
                    window.location.href = `/?q=${encodeURIComponent(keyword)}`;
                }
            }
        });

        // 回到顶部功能
        document.querySelector('footer a').addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
    @yield('scripts')

    @include('admin.settings-modal')

</body>

<footer class="mt-24 pb-12 text-center">
    <div class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest leading-loose">
        {!! $site->footer_text !!}
        <p class="mt-2">{{ $site->copyright }} {{ date('Y') }}</p>
    </div>

    {{-- 社交链接图标 --}}
    <div class="flex justify-center gap-4 mt-6 text-zinc-300">
        @if($site->social_github)
        <a href="{{ $site->social_github }}" class="hover:text-black transition">Github</a>
        @endif
        @if($site->social_twitter)
        <a href="{{ $site->social_twitter }}" class="hover:text-black transition">Twitter</a>
        @endif
    </div>
</footer>

</html>
