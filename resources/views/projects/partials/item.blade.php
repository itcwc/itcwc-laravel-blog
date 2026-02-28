@php
$isEven = ($index + 1) % 2 === 0;
@endphp

<div class="{{ $isEven ? 'md:col-span-10 md:col-start-3 text-right' : 'md:col-span-10 md:col-start-1' }} project-item relative">
    <article class="group" data-keywords="{{ $project->keywords }}">

        <div class="relative overflow-hidden bg-zinc-50 aspect-[16/9] shadow-sm">
            <img src="{{ str_starts_with($project->image_url, 'http') ? $project->image_url : asset('storage/' . $project->image_url) }}"
                alt="{{ $project->title }}"
                class="w-full h-full object-cover grayscale-img">

            @auth
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-4 z-20">
                <button data-project="{{ json_encode($project) }}"
                    onclick="handleProjectEdit(JSON.parse(this.dataset.project))"
                    class="bg-white text-black px-3 py-1 text-[10px] tracking-widest uppercase hover:bg-zinc-200 transition">
                    {{ __('project.edit') }}
                </button>
                <button onclick="confirmDeleteProject(`{{ $project->id }}`)"
                    data-id="{{ $project->id }}"
                    class="bg-red-500 text-white px-3 py-1 text-[10px] tracking-widest uppercase hover:bg-red-600 transition">
                    {{ __('project.delete') }}
                </button>
            </div>
            @endauth

            @if($project->case_url && !auth()->check())
            <a href="{{ $project->case_url }}" class="absolute inset-0 z-10"></a>
            @endif
        </div>

        <div class="mt-8 max-w-2xl {{ $isEven ? 'ml-auto' : '' }}">
            <header class="mb-6 flex items-center {{ $isEven ? 'justify-end' : '' }} gap-4">
                <span class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest">
                    {{ __('project.project') }} {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                </span>
                @auth
                <button data-project="{{ json_encode($project) }}"
                    onclick="handleProjectEdit(JSON.parse(this.dataset.project))"
                    class="text-[10px] text-zinc-300 hover:text-black transition">
                    [{{ __('project.edit') }}]
                </button>
                @endauth
            </header>

            <h2 class="text-3xl font-light tracking-tight mt-2">{{ $project->title }}</h2>
            <p class="text-zinc-400 leading-relaxed font-light text-lg mt-4">
                {{ $project->description }}
            </p>

            <footer class="mt-8 flex gap-8 text-[11px] tracking-[0.2em] uppercase {{ $isEven ? 'justify-end' : '' }}">
                @if($project->case_url)
                <a href="{{ $project->case_url }}" target="_blank" class="border-b border-zinc-900 pb-1 hover:text-zinc-400 transition">{{ __('project.view_case') }}</a>
                @endif
                @if($project->source_code_url)
                <a href="{{ $project->source_code_url }}" target="_blank" class="text-zinc-400 hover:text-zinc-900 transition">{{ __('project.source_code_url') }}</a>
                @endif
            </footer>
        </div>
    </article>
</div>
