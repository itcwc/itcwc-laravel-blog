@foreach($contents as $index => $item)
@if($item->type === 'article')
<article class="content-item pt-8 group relative" data-keywords="{{ $item->keywords }}">
    <header class="mb-6">
        <span class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest">Article {{ str_pad($contents->total() - ($contents->firstItem() + $index) + 1, 2, '0', STR_PAD_LEFT) }}</span>

        <a href="/article/{{ $item->slug ?? $item->id }}" class="block group/title">
            <h2 class="text-3xl font-light tracking-tight mt-2 group-hover/title:text-zinc-500 transition-colors duration-300">
                @if(!empty($keyword))
                <x-highlight :text="$item->title" :term="$keyword" />
                @else
                {{ $item->title }}
                @endif
            </h2>
        </a>
    </header>

    <p class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest mb-6">
        {{ $item->published_date->format('Y.m.d') }} · {{ $item->read_time ?? '5 min' }} read
    </p>

    <div class="text-zinc-400 leading-relaxed font-light text-lg mb-8 line-clamp-3">
        @if(!empty($keyword))
        <x-highlight :text="Str::limit(strip_tags($item->content), 200)" :term="$keyword" />
        @else
        {{ Str::limit(strip_tags($item->content), 200) }}
        @endif
    </div>

    <footer class="flex gap-8 text-[11px] tracking-[0.2em] uppercase">
        <a href="/article/{{ $item->slug ?? $item->id }}" class="border-b border-zinc-900 pb-1 hover:text-zinc-400 hover:border-zinc-400 transition-all duration-300">Read Full</a>
    </footer>

    @auth
    <div class="absolute top-8 right-0 opacity-0 group-hover:opacity-100 transition-all">
        <button data-item="{{ json_encode($item) }}" onclick="handleEdit(this)" class="px-3 py-1 bg-black text-[9px] text-white tracking-[0.2em] uppercase shadow-xl">Edit</button>
        <button onclick="confirmGeneralDelete(`{{ $item->id }}`, this.closest('.content-item'))" class="bg-red-500 text-white px-3 py-1 text-[10px] tracking-widest uppercase hover:bg-red-600 transition">Delete</button>
    </div>
    @endauth
    <hr class="mt-8 border-zinc-100">
</article>

@else
{{-- Note 部分保持原样，通常 Note 不需要详情页 --}}
<div class="note-item pt-8 group relative " data-keywords="{{ $item->keywords }}">
    <header class="mb-6">
        <span class="text-[10px] font-mono text-zinc-300 uppercase tracking-widest">Note {{ str_pad($contents->total() - ($contents->firstItem() + $index) + 1, 2, '0', STR_PAD_LEFT) }}</span>
    </header>
    <p class="text-2xl font-light tracking-tight text-balance">{{ $item->content }}</p>

    @if($item->images && count($item->images) > 0)
    <div class="mt-6 grid {{ count($item->images) === 1 ? 'grid-cols-1' : (count($item->images) === 2 ? 'grid-cols-2' : 'grid-cols-3') }} gap-2 w-full max-w-sm">
        @foreach($item->images as $img)
        <div class="aspect-square bg-zinc-50 overflow-hidden cursor-zoom-in">
            <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover hover:scale-105 transition duration-500">
        </div>
        @endforeach
    </div>
    @endif

    <p class="mt-4 text-[10px] font-mono text-zinc-300 uppercase tracking-widest">{{ $item->published_date->format('Y.m.d') }}</p>

    @auth
    <div class="absolute top-8 right-0 opacity-0 group-hover:opacity-100 transition-all">
        <button data-item="{{ json_encode($item) }}" onclick="handleEdit(this)" class="px-3 py-1 bg-black text-[9px] text-white tracking-[0.2em] uppercase shadow-xl">Edit</button>
        <button onclick="confirmGeneralDelete(`{{ $item->id }}`, this.closest('.note-item'))" class="bg-red-500 text-white px-3 py-1 text-[10px] tracking-widest uppercase hover:bg-red-600 transition">Delete</button>
    </div>
    @endauth
    <hr class="mt-8 border-zinc-100">
</div>
@endif
@endforeach
