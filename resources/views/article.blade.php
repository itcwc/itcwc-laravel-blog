@extends('layouts.app')

{{-- 填充 SEO 变量 --}}
@section('title', $article->title)
@section('description', Str::limit(strip_tags($article->content), 160))
{{-- 如果文章有关键词字段则用文章的，否则留空使用全局配置 --}}
@section('keywords', $article->keywords ?? $site->seo_keywords)

@section('content')
<article class="max-w-3xl mx-auto">
    {{-- 文章头部 --}}
    <header class="mb-12 pt-8">
        <div class="flex items-center gap-3 mb-4">
            <span class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest">Article</span>

            {{-- 仅管理员可见的编辑按钮 --}}
            @auth
            <button
                data-item="{{ json_encode($article) }}"
                onclick="handleEdit(this)"
                class="px-3 py-1 bg-black text-[9px] text-white tracking-[0.2em] uppercase shadow-xl">
                Edit Article
            </button>
            @endauth
        </div>

        <h1 class="text-4xl font-light tracking-tight mt-4 text-zinc-900 leading-tight">
            {{ $article->title }}
        </h1>

        <div class="mt-8 flex items-center justify-between text-[10px] font-mono text-zinc-300 uppercase tracking-[0.2em]">
            <div class="flex items-center gap-4">
                <time datetime="{{ $article->published_date }}">{{ $article->published_date->format('F d, Y') }}</time>
                <span>·</span>
                <span>{{ ceil(mb_strlen(strip_tags($article->content)) / 400) }} min read</span>
            </div>
        </div>

        <div class="mt-8 h-[1px] bg-gradient-to-r from-zinc-100 to-transparent"></div>
    </header>

    {{-- 文章主体：使用 Tailwind Typography (prose) --}}
    {{-- 这里的 prose-zinc 配合 font-light 能保持你网站的一致性 --}}
    <div class="prose prose-zinc max-w-none
                    prose-headings:font-light prose-headings:tracking-tight prose-headings:text-zinc-900
                    prose-p:text-zinc-500 prose-p:leading-relaxed prose-p:font-light prose-p:text-lg
                    prose-a:text-zinc-900 prose-a:no-underline prose-a:border-b prose-a:border-zinc-200 hover:prose-a:border-zinc-900 prose-a:transition
                    prose-blockquote:font-light prose-blockquote:italic prose-blockquote:text-zinc-400
                    prose-code:text-zinc-800 prose-code:bg-zinc-50 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:before:content-none prose-code:after:content-none
                    prose-img:rounded-sm prose-img:shadow-sm">

        {{-- 使用 Spatie 提供的 Blade 指令 --}}
        @markdown($article->content)

    </div>

    {{-- 文章底部 --}}
    <div class="mt-20 pt-10 border-t border-zinc-50">
        <footer class="flex justify-between items-center">
            <a href="/" class="text-[11px] tracking-[0.2em] uppercase text-zinc-400 hover:text-zinc-900 transition flex items-center gap-2 group">
                <span class="group-hover:-translate-x-1 transition-transform">←</span> Back to Index
            </a>

            <div class="flex gap-6">
                {{-- 这里可以放分享按钮或者 End 标记 --}}
                <span class="text-[10px] font-mono text-zinc-200 uppercase tracking-widest">End of Article</span>
            </div>
        </footer>
    </div>
</article>
@endsection
