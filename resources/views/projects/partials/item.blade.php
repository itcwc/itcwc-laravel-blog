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
                <button onclick='handleProjectEdit(@json($project))'
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
                <button onclick='handleProjectEdit(@json($project))' class="text-[10px] text-zinc-300 hover:text-black transition">
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

@auth
<div id="project-modal" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-white/90 backdrop-blur-sm" onclick="toggleProjectModal()"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-xl bg-white shadow-2xl p-10 overflow-y-auto border-l border-zinc-100">
        <h3 id="project-modal-title" class="text-sm font-mono tracking-widest uppercase mb-12">{{ __('project.new_project') }}</h3>

        <form id="project-form" class="space-y-8">
            @csrf
            <input type="hidden" name="id" id="project-id">

            <div>
                <label class="block text-[10px] text-zinc-400 uppercase tracking-widest mb-2">{{ __('project.project_title') }}</label>
                <input type="text" name="title" id="p-title" class="w-full border-b border-zinc-200 py-2 focus:border-black outline-none font-light text-xl">
            </div>

            <div>
                <label class="block text-[10px] text-zinc-400 uppercase tracking-widest mb-2">{{ __('project.description') }}</label>
                <textarea name="description" id="p-description" class="w-full border border-zinc-100 p-3 focus:border-black outline-none font-light text-sm h-32 resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] text-zinc-400 uppercase tracking-widest mb-2">{{ __('project.keywords') }}</label>
                    <input type="text" name="keywords" id="p-keywords" placeholder="Logic, UI, Laravel" class="w-full border-b border-zinc-200 py-2 focus:border-black outline-none font-light text-sm">
                </div>
                <div>
                    <label class="block text-[10px] text-zinc-400 uppercase tracking-widest mb-2">{{ __('project.order') }}</label>
                    <input type="number" name="order" id="p-order" value="0" class="w-full border-b border-zinc-200 py-2 focus:border-black outline-none font-light text-sm">
                </div>
            </div>

            <div class="space-y-4">
                <label class="block text-[10px] text-zinc-400 uppercase tracking-widest mb-2">{{ __('project.project_image') }}</label>

                <div id="project-image-preview" class="w-full aspect-[16/9] bg-zinc-50 border border-zinc-100 rounded-sm overflow-hidden flex items-center justify-center group/preview relative">
                    <img id="p-preview-img" src="" class="w-full h-full object-cover hidden">
                    <div id="p-preview-placeholder" class="text-[10px] text-zinc-300 font-mono uppercase tracking-widest">
                        {{ __('project.no_image_selected') }}
                    </div>
                </div>

                <input type="hidden" name="existing_image" id="p-existing-image">

                <div class="relative">
                    <input type="file" name="image" id="p-image-file"
                        accept="image/*"
                        onchange="handleImageSelect(this)"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="w-full border-b border-zinc-200 py-2 flex justify-between items-center">
                        <span id="file-name-display" class="text-[10px] text-zinc-400 font-light uppercase tracking-wider">{{ __('project.choose_local_file') }}</span>
                        <span class="text-[9px] bg-zinc-100 px-2 py-1 uppercase tracking-tighter">{{ __('project.browse') }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] text-zinc-400 uppercase tracking-widest mb-2">{{ __('project.case_url') }}</label>
                    <input type="text" name="case_url" id="p-case-url" class="w-full border-b border-zinc-200 py-2 focus:border-black outline-none font-light text-xs">
                </div>
                <div>
                    <label class="block text-[10px] text-zinc-400 uppercase tracking-widest mb-2">{{ __('project.source_code') }}</label>
                    <input type="text" name="source_code_url" id="p-source-code-url" class="w-full border-b border-zinc-200 py-2 focus:border-black outline-none font-light text-xs">
                </div>
            </div>

            <div class="pt-10 flex gap-4">
                <button type="button" onclick="saveProject()" class="flex-1 bg-black text-white py-4 text-[10px] tracking-[0.3em] uppercase hover:bg-zinc-800 transition">
                    {{ __('project.save_project') }}
                </button>
                <button type="button" id="delete-project-btn" onclick="deleteProject()" class="hidden px-6 border border-red-100 text-red-400 text-[10px] uppercase tracking-widest hover:bg-red-50 transition">
                    {{ __('project.delete') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endauth


<script>
const i18n = {
    newProject: '{{ __('project.new_project') }}',
    editProject: '{{ __('project.edit_project') }}',
    saving: '{{ __('project.saving') }}',
    saveProject: '{{ __('project.save_project') }}',
    confirmDelete: '{{ __('project.confirm_delete') }}',
    deleteFailed: '{{ __('project.delete_failed') }}',
    uploadFailed: '{{ __('project.upload_failed') }}',
    networkError: '{{ __('project.network_error') }}',
    currentImage: '{{ __('project.current_image') }}',
    chooseLocalFile: '{{ __('project.choose_local_file') }}'
};

function toggleProjectModal() {
    document.getElementById('project-modal').classList.toggle('hidden');
}

function handleProjectEdit(project = null) {
    const form = document.getElementById('project-form');
    form.reset();

    const img = document.getElementById('p-preview-img');
    const placeholder = document.getElementById('p-preview-placeholder');
    const fileDisplay = document.getElementById('file-name-display');

    document.getElementById('project-id').value = '';
    document.getElementById('project-modal-title').innerText = i18n.newProject;
    document.getElementById('delete-project-btn').classList.add('hidden');

    if (project) {
        document.getElementById('project-modal-title').innerText = i18n.editProject;
        document.getElementById('project-id').value = project.id;
        document.getElementById('p-title').value = project.title;
        document.getElementById('p-description').value = project.description;
        document.getElementById('p-keywords').value = project.keywords;
        document.getElementById('p-order').value = project.order;
        document.getElementById('p-case-url').value = project.case_url;
        document.getElementById('p-source-code-url').value = project.source_code_url;
        document.getElementById('delete-project-btn').classList.remove('hidden');

        if (project.image_url) {
            const src = project.image_url.startsWith('http') ? project.image_url : `/storage/${project.image_url}`;
            img.src = src;
            img.classList.remove('hidden');
            placeholder.classList.add('hidden');
            document.getElementById('p-existing-image').value = project.image_url;
            fileDisplay.innerText = i18n.currentImage + ": " + project.image_url.split('/').pop();
        }
    } else {
        img.classList.add('hidden');
        placeholder.classList.remove('hidden');
        fileDisplay.innerText = i18n.chooseLocalFile;
    }

    toggleProjectModal();
}

async function saveProject() {
    const form = document.getElementById('project-form');
    const formData = new FormData(form);
    const id = document.getElementById('project-id').value;
    const url = id ? `/api/projects/update/${id}` : '/api/projects/store';

    const btn = form.querySelector('button[onclick="saveProject()"]');
    btn.disabled = true;
    btn.innerText = i18n.saving;

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (response.ok && result.success) {
            location.reload();
        } else {
            const errorMsg = result.message || 'Error occurred';
            alert(i18n.uploadFailed + ': ' + errorMsg);
            btn.disabled = false;
            btn.innerText = i18n.saveProject;
        }
    } catch (e) {
        console.error(e);
        btn.disabled = false;
        btn.innerText = i18n.saveProject;
    }
}

async function deleteProject() {
    if (!confirm(i18n.confirmDelete)) return;
    const id = document.getElementById('project-id').value;
    try {
        await fetch(`/api/projects/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        location.reload();
    } catch (e) {
        alert(i18n.deleteFailed);
    }
}

function handleImageSelect(input) {
    const fileDisplay = document.getElementById('file-name-display');
    const img = document.getElementById('p-preview-img');
    const placeholder = document.getElementById('p-preview-placeholder');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        fileDisplay.innerText = file.name;

        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            img.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
}

async function confirmDeleteProject(id) {
    if (!confirm(i18n.confirmDelete)) {
        return;
    }

    try {
        const response = await fetch(`/api/projects/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (response.ok && result.success) {
            const item = document.querySelector(`button[data-id="${id}"]`).closest('.project-item');
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            item.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            alert(i18n.deleteFailed);
        }
    } catch (error) {
        console.error('Delete error:', error);
        alert(i18n.networkError);
    }
}
</script>
