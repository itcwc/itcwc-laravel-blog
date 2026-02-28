<div id="search-overlay" class="fixed inset-0 z-[100] bg-white opacity-0 pointer-events-none transition-all duration-500 flex flex-col justify-center items-center px-6">
    <button id="search-close" class="absolute top-6 right-6 md:top-10 md:right-10 text-zinc-400 hover:text-zinc-900 transition p-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
    </button>
    <div class="w-full max-w-3xl">
        <input type="text" placeholder="{{ __('ui.search_placeholder') }}"
               class="w-full text-3xl md:text-6xl font-light tracking-tighter text-center border-none focus:ring-0 placeholder-zinc-100 outline-none bg-transparent">
    </div>
</div>

<div id="search-result" class="mb-12 pt-8 text-[10px] text-zinc-300 uppercase tracking-widest hidden">
    <p>{{ __('ui.search_results') }}: <span id="search-keyword" class="text-zinc-900"></span> (<span id="result-count">0</span>
        items)</p>
    <hr class="mt-4 border-zinc-100">
</div>
