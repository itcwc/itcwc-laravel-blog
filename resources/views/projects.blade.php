@extends('layouts.app')

@section('title', 'Projects')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400&display=swap');

    /* 参考示例的图片效果 */
    .grayscale-img {
        filter: grayscale(100%);
        opacity: 0.8;
        transition: all 1s ease-in-out;
    }

    .group:hover .grayscale-img {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.05);
    }
</style>
@endsection

@section('content')
<!-- 参考示例的主内容区布局 - 缩小间距 -->
<main class="max-w-6xl mx-auto px-8 pt-32 pb-24">
    <!-- 搜索结果提示 -->
    <div id="project-search-result" class="mb-12 pt-8 text-[10px] text-zinc-300 uppercase tracking-widest hidden">
        <p>Search Results: <span id="project-search-keyword" class="text-zinc-900"></span> (<span id="project-result-count">0</span> items)</p>
        <hr class="mt-4 border-zinc-100">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-y-20 md:gap-y-32">

        <!-- 项目 1 - 参考示例的排版 -->
        <div class="md:col-span-10 md:col-start-1">
            <article class="group project-item" data-keywords="待办事项 Laravel Vue 数据导出 本地存储 极简工具">
                <div class="relative overflow-hidden bg-zinc-50 aspect-[16/9] shadow-sm">
                    <img src="https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?q=80&w=2067"
                        alt="极简待办事项工具"
                        class="w-full h-full object-cover grayscale-img">
                    <a href="#" class="absolute inset-0 z-10"></a>
                </div>

                <div class="mt-8 max-w-2xl">
                    <header class="mb-6">
                        <span class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest">Project 01</span>
                        <h2 class="text-3xl font-light tracking-tight mt-2">极简待办事项工具</h2>
                    </header>
                    <p class="text-zinc-400 leading-relaxed font-light text-lg">
                        基于 Laravel + Vue 开发的极简待办事项管理工具，无多余功能，专注核心需求，支持数据导出和本地存储。
                        设计上完全遵循极简原则，只有输入框、待办列表、完成状态三个核心元素，没有广告、没有推送、没有复杂的分类。
                    </p>
                    <footer class="mt-8 flex gap-8 text-[11px] tracking-[0.2em] uppercase">
                        <a href="#" class="border-b border-zinc-900 pb-1 hover:text-zinc-400 hover:border-zinc-400 transition">View Case</a>
                        <a href="#" class="text-zinc-400 hover:text-zinc-900 transition">Source Code</a>
                    </footer>
                </div>
            </article>
        </div>

        <!-- 项目 2 - 参考示例的右侧排版 -->
        <div class="md:col-span-10 md:col-start-3 text-right">
            <article class="group project-item" data-keywords="API 接口测试 RESTful 参数保存 响应对比 调试工具">
                <div class="relative overflow-hidden bg-zinc-50 aspect-[16/9] shadow-sm">
                    <img src="https://images.unsplash.com/photo-1451187530220-4c23ebb8d0ef?q=80&w=2070"
                        alt="API 接口测试工具"
                        class="w-full h-full object-cover grayscale-img">
                    <a href="#" class="absolute inset-0 z-10"></a>
                </div>

                <div class="mt-8 max-w-2xl ml-auto">
                    <header class="mb-6">
                        <span class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest">Project 02</span>
                        <h2 class="text-3xl font-light tracking-tight mt-2">API 接口测试工具</h2>
                    </header>
                    <p class="text-zinc-400 leading-relaxed font-light text-lg">
                        轻量级的 API 测试工具，支持 RESTful 接口测试、参数保存、响应结果对比，适合个人开发者快速调试接口。
                        界面只有输入区、发送按钮、响应区三个部分，去掉了 Postman 等工具的复杂功能，只保留开发者日常调试需要的核心能力。
                    </p>
                    <footer class="mt-8 flex gap-8 text-[11px] tracking-[0.2em] uppercase justify-end">
                        <a href="#" class="border-b border-zinc-900 pb-1 hover:text-zinc-400 hover:border-zinc-400 transition">View Case</a>
                        <a href="#" class="text-zinc-400 hover:text-zinc-900 transition">Source Code</a>
                    </footer>
                </div>
            </article>
        </div>

        <!-- 加载更多内容容器 -->
        <div id="project-content-container" class="md:col-span-12"></div>
    </div>

    <!-- 加载更多按钮 - 缩小间距 -->
    <div class="mt-16 text-center">
        <button id="project-load-more-btn" class="text-[11px] tracking-[0.2em] uppercase border-b border-zinc-900 pb-1 hover:text-zinc-400 hover:border-zinc-400 transition bg-transparent">
            Load More Projects
        </button>
        <div id="project-loading-indicator" class="mt-6 text-[10px] font-mono text-zinc-300 uppercase tracking-widest hidden">
            Loading...
        </div>
        <div id="project-no-more-content" class="mt-6 text-[10px] font-mono text-zinc-300 uppercase tracking-widest hidden">
            End of Projects
        </div>
    </div>
</main>
@endsection

@section('scripts')

<script>
    // 模拟更多项目数据
    const moreProjects = [
        `
<div class="md:col-span-10 md:col-start-1">
    <article class="group project-item" data-keywords="Markdown 编辑器 实时预览 Laravel 极简写作">
        <div class="relative overflow-hidden bg-zinc-50 aspect-[16/9] shadow-sm">
            <img src="https://images.unsplash.com/photo-1507537297325-5f24762c6d88?q=80&w=2069"
                alt="极简 Markdown 编辑器"
                class="w-full h-full object-cover grayscale-img">
            <a href="#" class="absolute inset-0 z-10"></a>
        </div>

        <div class="mt-8 max-w-2xl">
            <header class="mb-6">
                <span class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest">Project 03</span>
                <h2 class="text-3xl font-light tracking-tight mt-2">极简 Markdown 编辑器</h2>
            </header>
            <p class="text-zinc-400 leading-relaxed font-light text-lg">
                轻量级 Markdown 编辑器，支持实时预览、常用快捷键、一键导出 HTML/PDF，基于 Laravel 后端存储文章内容。
                界面极简，无多余按钮，专注写作体验，适合博客作者和文档编辑使用。
            </p>
            <footer class="mt-8 flex gap-8 text-[11px] tracking-[0.2em] uppercase">
                <a href="#" class="border-b border-zinc-900 pb-1 hover:text-zinc-400 hover:border-zinc-400 transition">View Case</a>
                <a href="#" class="text-zinc-400 hover:text-zinc-900 transition">Source Code</a>
            </footer>
        </div>
    </article>
</div>
`,
        `
<div class="md:col-span-10 md:col-start-3 text-right">
    <article class="group project-item" data-keywords="个人仪表盘 Laravel 数据统计 极简 UI">
        <div class="relative overflow-hidden bg-zinc-50 aspect-[16/9] shadow-sm">
            <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=2070"
                alt="个人数据仪表盘"
                class="w-full h-full object-cover grayscale-img">
            <a href="#" class="absolute inset-0 z-10"></a>
        </div>

        <div class="mt-8 max-w-2xl ml-auto">
            <header class="mb-6">
                <span class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest">Project 04</span>
                <h2 class="text-3xl font-light tracking-tight mt-2">个人数据仪表盘</h2>
            </header>
            <p class="text-zinc-400 leading-relaxed font-light text-lg">
                极简风格的个人数据仪表盘，集成天气、待办、阅读统计、习惯打卡等功能，基于 Laravel 开发，支持自定义数据来源。
                所有数据可视化均采用极简设计，无多余图表样式，只展示核心数据。
            </p>
            <footer class="mt-8 flex gap-8 text-[11px] tracking-[0.2em] uppercase justify-end">
                <a href="#" class="border-b border-zinc-900 pb-1 hover:text-zinc-400 hover:border-zinc-400 transition">View Case</a>
                <a href="#" class="text-zinc-400 hover:text-zinc-900 transition">Source Code</a>
            </footer>
        </div>
    </article>
</div>
`
    ];

    let projectLoadedCount = 0;
    const totalMoreProjects = moreProjects.length;

    // 项目搜索功能
    const searchOpen = document.getElementById('search-open');
    const searchClose = document.getElementById('search-close');
    const searchOverlay = document.getElementById('search-overlay');
    const searchInput = searchOverlay.querySelector('input');
    const projectSearchResult = document.getElementById('project-search-result');
    const projectSearchKeyword = document.getElementById('project-search-keyword');
    const projectResultCount = document.getElementById('project-result-count');
    let projectItems = document.querySelectorAll('.project-item');

    function performProjectSearch(keyword) {
        keyword = keyword.trim().toLowerCase();

        if (keyword === '') {
            projectSearchResult.classList.add('hidden');
            projectItems.forEach(item => {
                item.classList.remove('hidden');
            });
            return;
        }

        projectSearchResult.classList.remove('hidden');
        projectSearchKeyword.textContent = keyword;

        let count = 0;
        projectItems.forEach(item => {
            const itemKeywords = item.dataset.keywords.toLowerCase();
            if (itemKeywords.includes(keyword)) {
                item.classList.remove('hidden');
                count++;
            } else {
                item.classList.add('hidden');
            }
        });

        projectResultCount.textContent = count;
    }

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
            searchOverlay.style.opacity = '0';
            searchOverlay.style.pointerEvents = 'none';
            performProjectSearch(keyword);
        }
    });

    // 项目加载更多
    const projectLoadMoreBtn = document.getElementById('project-load-more-btn');
    const projectLoadingIndicator = document.getElementById('project-loading-indicator');
    const projectNoMoreContent = document.getElementById('project-no-more-content');
    const projectContentContainer = document.getElementById('project-content-container');

    projectLoadMoreBtn.addEventListener('click', () => {
        projectLoadMoreBtn.disabled = true;
        projectLoadMoreBtn.classList.add('opacity-70');
        projectLoadingIndicator.classList.remove('hidden');

        setTimeout(() => {
            if (projectLoadedCount < totalMoreProjects) {
                projectContentContainer.innerHTML += moreProjects[projectLoadedCount];
                projectLoadedCount++;

                // 更新项目列表
                projectItems = document.querySelectorAll('.project-item');

                projectLoadingIndicator.classList.add('hidden');
                projectLoadMoreBtn.disabled = false;
                projectLoadMoreBtn.classList.remove('opacity-70');

                if (projectLoadedCount >= totalMoreProjects) {
                    projectLoadMoreBtn.classList.add('hidden');
                    projectNoMoreContent.classList.remove('hidden');
                }
            }
        }, 800);
    });
</script>
@endsection
