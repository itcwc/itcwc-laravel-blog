@extends('layouts.app')

@section('title', __('ui.projects'))

@section('content')
<main class="max-w-6xl mx-auto px-8 pb-24">
    @if($keyword)
    <div id="project-search-result" class="mb-12 pt-8 text-[10px] text-zinc-300 uppercase tracking-widest">
        <p>{{ __('ui.search_results') }}: <span class="text-zinc-900">{{ $keyword }}</span> ({{ $projects->total() }} items)</p>
        <hr class="mt-4 border-zinc-100">
    </div>
    @endif

    <div id="project-list" class="grid grid-cols-1 md:grid-cols-12 gap-y-20 md:gap-y-32">
        @foreach($projects as $index => $project)
        @include('projects.partials.item', ['project' => $project, 'index' => $index])
        @endforeach
    </div>

    @if($projects->hasMorePages())
    <div class="mt-16 text-center">
        <button id="load-more-projects" data-next-page="{{ $projects->nextPageUrl() }}"
            class="text-[11px] tracking-[0.2em] uppercase border-b border-zinc-900 pb-1 hover:text-zinc-400 hover:border-zinc-400 transition">
            {{ __('ui.load_more_projects') }}
        </button>
    </div>
    @endif
</main>
@endsection

<script>
    document.getElementById('load-more-projects')?.addEventListener('click', async function() {
        const btn = this;
        const url = btn.getAttribute('data-next-page');

        if (!url) return;

        btn.innerText = '{{ __('ui.loading') }}';

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const html = await response.text();

            document.getElementById('project-list').insertAdjacentHTML('beforeend', html);
        } catch (e) {
            console.error(e);
        }
    });
</script>

<style>
    .grayscale-img {
        filter: grayscale(100%);
        opacity: 0.8;
        background-color: #f4f4f5;
        transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .group:hover .grayscale-img {
        filter: grayscale(0%);
        opacity: 1;
        transform: scale(1.05);
    }

    .login-out-form {
        margin-top: 0.35rem;
    }
</style>
