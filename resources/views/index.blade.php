@extends('layouts.app')

@section('title', 'Index')

@section('content')

@if($keyword)
<div class="mb-16 pt-8 border-b border-zinc-100 pb-12">
    <h1 class="text-[10px] font-mono text-zinc-300 uppercase tracking-[0.3em] mb-4">Search Results</h1>
    <div class="flex items-end justify-between">
        <p class="text-3xl font-light tracking-tight">“{{ $keyword }}”</p>
        <a href="/" class="text-[10px] font-mono text-zinc-400 border-b border-zinc-200 pb-1 hover:text-black hover:border-black transition">Clear Search</a>
    </div>
    <p class="mt-4 text-[10px] font-mono text-zinc-300 uppercase tracking-widest">Found {{ $contents->total() }} items</p>
</div>
@else
@if($featured)
@include('partials.featured')
@endif
@endif



<div id="content-container" class="space-y-16">
    @if($contents->isEmpty())
    <div class="py-20 text-center">
        <p class="text-zinc-300 font-light italic text-lg">No matches found for your search.</p>
    </div>
    @else
    @include('partials.content-item', ['contents' => $contents])
    @endif
</div>

<div class="mt-16 text-center">
    @if($contents->hasMorePages())
    <a href="{{ $contents->nextPageUrl() }}" class="text-[11px] tracking-[0.2em] uppercase border-b border-zinc-900 pb-1 hover:text-zinc-400 transition">Next Page</a>
    @else
    <div class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest">End of Content</div>
    @endif
</div>
@endsection
