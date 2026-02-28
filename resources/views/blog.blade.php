@extends('layouts.app')

@section('title', __('ui.blog'))

@section('content')

<div id="content-container" class="space-y-16">
    @if($contents->isEmpty())
    <div class="py-20 text-center">
        <p class="text-zinc-300 font-light italic text-lg">{{ __('ui.no_matches') }}</p>
    </div>
    @else
    @include('partials.content-item', ['contents' => $contents])
    @endif
</div>

<div class="mt-16 text-center">
    @if($contents->hasMorePages())
    <a href="{{ $contents->nextPageUrl() }}" class="text-[11px] tracking-[0.2em] uppercase border-b border-zinc-900 pb-1 hover:text-zinc-400 transition">{{ __('ui.next_page') }}</a>
    @else
    <div class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest">{{ __('ui.end_of_content') }}</div>
    @endif
</div>
@endsection
