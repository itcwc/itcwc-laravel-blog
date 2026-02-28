@auth
<div id="settings-modal" class="fixed inset-0 z-[70] hidden">
    <div class="absolute inset-0 bg-white/60 backdrop-blur-md" onclick="toggleSettingsModal()"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-2xl bg-white shadow-2xl overflow-hidden border-l border-zinc-100 flex flex-col">
        <div class="p-8 border-b border-zinc-50 flex justify-between items-center">
            <h3 class="text-sm font-mono tracking-widest uppercase text-zinc-400">{{ __('settings.system_dashboard') }}</h3>
            <button onclick="toggleSettingsModal()" class="text-zinc-300 hover:text-black transition">✕</button>
        </div>

        <form id="settings-form" class="flex-1 overflow-y-auto p-10 space-y-12">
            @csrf

            <section>
                <h4 class="text-[10px] font-mono text-zinc-300 uppercase tracking-[0.3em] mb-8 border-b pb-2">{{ __('settings.basic_information') }}</h4>
                <div class="grid grid-cols-2 gap-8">
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] text-zinc-400 uppercase mb-2">{{ __('settings.site_name') }}</label>
                        <input type="text" name="site_name" value="{{ $site->site_name }}" class="w-full border-b border-zinc-200 py-1 focus:border-black outline-none font-light">
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] text-zinc-400 uppercase mb-2">{{ __('settings.site_url') }}</label>
                        <input type="url" name="site_url" value="{{ $site->site_url }}" class="w-full border-b border-zinc-200 py-1 focus:border-black outline-none font-light">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[9px] text-zinc-400 uppercase mb-2">{{ __('settings.site_slogan') }}</label>
                        <input type="text" name="site_slogan" value="{{ $site->site_slogan }}" class="w-full border-b border-zinc-200 py-1 focus:border-black outline-none font-light text-zinc-500">
                    </div>
                </div>
            </section>

            <section>
                <h4 class="text-[10px] font-mono text-zinc-300 uppercase tracking-[0.3em] mb-8 border-b pb-2">{{ __('settings.assets_branding') }}</h4>
                <div class="grid grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="block text-[9px] text-zinc-400 uppercase mb-2">{{ __('settings.site_icon') }}</label>
                        <div class="flex items-center gap-4">
                            <div id="icon-preview-container" class="w-12 h-12 border border-zinc-100 rounded-sm bg-zinc-50 flex items-center justify-center overflow-hidden">
                                @if($site->site_icon)
                                <img src="{{ asset('storage/'.$site->site_icon) }}" class="w-full h-full object-cover">
                                @else
                                <span class="text-[10px] text-zinc-300">ICO</span>
                                @endif
                            </div>
                            <input type="file" name="site_icon" onchange="previewImage(this, 'icon-preview-container')" class="text-[10px] flex-1 file:mr-4 file:py-1 file:px-3 file:border-0 file:text-[10px] file:bg-zinc-100 hover:file:bg-zinc-200 transition">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-[9px] text-zinc-400 uppercase mb-2">{{ __('settings.social_share_image') }}</label>
                        <div class="flex items-center gap-4">
                            <div id="share-preview-container" class="w-20 h-12 border border-zinc-100 rounded-sm bg-zinc-50 flex items-center justify-center overflow-hidden">
                                @if($site->share_image)
                                <img src="{{ asset('storage/'.$site->share_image) }}" class="w-full h-full object-cover">
                                @else
                                <span class="text-[10px] text-zinc-300">1200x630</span>
                                @endif
                            </div>
                            <input type="file" name="share_image" onchange="previewImage(this, 'share-preview-container')" class="text-[10px] flex-1 file:mr-4 file:py-1 file:px-3 file:border-0 file:text-[10px] file:bg-zinc-100 hover:file:bg-zinc-200 transition">
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <h4 class="text-[10px] font-mono text-zinc-300 uppercase tracking-[0.3em] mb-8 border-b pb-2">{{ __('settings.search_optimization') }}</h4>
                <div class="space-y-6">
                    <div>
                        <label class="block text-[9px] text-zinc-400 uppercase mb-2">{{ __('settings.seo_title') }}</label>
                        <input type="text" name="seo_title" value="{{ $site->seo_title }}" class="w-full border-b border-zinc-200 py-1 focus:border-black outline-none font-light">
                    </div>
                    <div>
                        <label class="block text-[9px] text-zinc-400 uppercase mb-2">{{ __('settings.seo_keywords') }}</label>
                        <input type="text" name="seo_keywords" value="{{ $site->seo_keywords }}" placeholder="{{ __('settings.comma_separated') }}" class="w-full border-b border-zinc-200 py-1 focus:border-black outline-none font-light text-sm">
                    </div>
                    <div>
                        <label class="block text-[9px] text-zinc-400 uppercase mb-2">{{ __('settings.seo_description') }}</label>
                        <textarea name="seo_description" class="w-full border border-zinc-100 p-3 focus:border-black outline-none font-light text-sm h-24 resize-none">{{ $site->seo_description }}</textarea>
                    </div>
                </div>
            </section>

            <section>
                <h4 class="text-[10px] font-mono text-zinc-300 uppercase tracking-[0.3em] mb-8 border-b pb-2">{{ __('settings.social_presence') }}</h4>
                <div class="grid grid-cols-2 gap-x-12 gap-y-6">
                    @foreach(['github', 'twitter', 'instagram', 'facebook', 'linkedin'] as $platform)
                    <div>
                        <label class="block text-[9px] text-zinc-400 uppercase mb-1">{{ $platform }}</label>
                        <input type="text" name="social_{{ $platform }}" value="{{ $site->{'social_'.$platform} }}" class="w-full border-b border-zinc-100 py-1 focus:border-black outline-none text-xs">
                    </div>
                    @endforeach
                </div>
            </section>

            <section>
                <h4 class="text-[10px] font-mono text-zinc-300 uppercase tracking-[0.3em] mb-8 border-b pb-2">{{ __('settings.footer_identity') }}</h4>
                <div class="space-y-6">
                    <div>
                        <label class="block text-[9px] text-zinc-400 uppercase mb-2">{{ __('settings.copyright_text') }}</label>
                        <input type="text" name="copyright" value="{{ $site->copyright }}" class="w-full border-b border-zinc-200 py-1 focus:border-black outline-none font-light">
                    </div>
                    <div>
                        <label class="block text-[9px] text-zinc-400 uppercase mb-2">{{ __('settings.footer_additional_text') }}</label>
                        <textarea name="footer_text" class="w-full border border-zinc-100 p-3 focus:border-black outline-none font-light text-xs h-20">{{ $site->footer_text }}</textarea>
                    </div>
                </div>
            </section>

            <section class="bg-zinc-50 p-6 rounded-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-[10px] font-mono text-zinc-900 uppercase tracking-widest">{{ __('settings.maintenance_mode') }}</h4>
                        <p class="text-[10px] text-zinc-400 mt-1">{{ __('settings.maintenance_mode_desc') }}</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="maintenance_mode" value="1" class="sr-only peer" {{ $site->maintenance_mode ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                    </label>
                </div>
            </section>
        </form>

        <div class="p-8 border-t border-zinc-50 bg-zinc-50/50 flex gap-4">
            <button id="save-settings-btn" type="button" onclick="updateSettings()" class="flex-1 bg-black text-white py-4 text-[10px] tracking-[0.3em] uppercase hover:bg-zinc-800 transition shadow-lg">
                {{ __('settings.save_settings') }}
            </button>
        </div>
    </div>
</div>
@endauth


<script>
    function toggleSettingsModal() {
        const modal = document.getElementById('settings-modal');
        modal.classList.toggle('hidden');
    }

    async function updateSettings() {
        const form = document.getElementById('settings-form');
        const formData = new FormData(form);
        const btn = document.getElementById('save-settings-btn');

        if (btn) {
            btn.innerText = "{{ __('settings.processing') }}";
            btn.disabled = true;
        }

        if (!formData.has('maintenance_mode')) {
            formData.append('maintenance_mode', '0');
        }

        try {
            const response = await fetch('/api/settings/update', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                btn.innerText = "{{ __('settings.settings_updated') }}";
                setTimeout(() => {
                    location.reload();
                }, 800);
            } else {
                throw new Error(result.error || 'Server error');
            }
        } catch (error) {
            console.error(error);
            alert('{{ __('settings.failed_to_save') }}' + error.message);
            if (btn) {
                btn.innerText = "{{ __('settings.save_settings') }}";
                btn.disabled = false;
            }
        }
    }

    function previewImage(input, containerId) {
        const container = document.getElementById(containerId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                container.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover animate-pulse-once">`;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    const style = document.createElement('style');
    style.textContent = `
    @keyframes pulse-once {
        0% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    .animate-pulse-once { animation: pulse-once 0.3s ease-out; }
`;
    document.head.appendChild(style);
</script>
