<nav class="fixed {{ auth()->check() ? 'top-10' : 'top-0' }} w-full px-6 py-6 md:px-12 md:py-10 flex flex-wrap justify-center gap-4 md:justify-between items-center z-50 bg-white/80 backdrop-blur-md max-[440px]:flex-col">

    <div class="flex gap-4 md:gap-8 items-center">

        <a href="{{ route('index') }}" class="text-[11px] md:text-xs tracking-widest uppercase hover:opacity-50 transition @if(request()->routeIs('index')) bg-black text-white px-3 py-1 @endif">Index</a>

        <a href="{{ route('note') }}" class="text-[11px] md:text-xs tracking-widest uppercase hover:opacity-50 transition @if(request()->routeIs('note')) bg-black text-white px-3 py-1 @endif">Note</a>

        <a href="{{ route('blog') }}" class="text-[11px] md:text-xs tracking-widest uppercase hover:opacity-50 transition @if(request()->routeIs('blog')) bg-black text-white px-3 py-1 @endif">Blog</a>

        <a href="{{ route('projects') }}" class="text-[11px] md:text-xs tracking-widest uppercase hover:opacity-50 transition @if(request()->routeIs('projects')) bg-black text-white px-3 py-1 @endif">Projects</a>


        <button id="search-open" class="text-[11px] md:text-xs tracking-widest uppercase text-zinc-400 hover:text-zinc-900 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <span class="hidden sm:inline">Search</span>
        </button>
    </div>
    <span class="text-[9px] md:text-[10px] tracking-[0.3em] uppercase text-zinc-300 font-light truncate">Words 24-26</span>
</nav>
