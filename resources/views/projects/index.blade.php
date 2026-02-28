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
@endsection

@section('styles')
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
</style>
@endsection

@section('scripts')
<script>
    const newProjectI18n = {
        newProject: `{{ __('project.new_project') }}`,
        editProject: `{{ __('project.edit_project') }}`,
        saving: `{{ __('project.saving') }}`,
        saveProject: `{{ __('project.save_project') }}`,
        confirmDelete: `{{ __('project.confirm_delete') }}`,
        deleteFailed: `{{ __('project.delete_failed') }}`,
        uploadFailed: `{{ __('project.upload_failed') }}`,
        networkError: `{{ __('project.network_error') }}`,
        currentImage: `{{ __('project.current_image') }}`,
        chooseLocalFile: `{{ __('project.choose_local_file') }}`
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
        document.getElementById('project-modal-title').innerText = newProjectI18n.newProject;
        document.getElementById('delete-project-btn').classList.add('hidden');

        if (project) {
            document.getElementById('project-modal-title').innerText = newProjectI18n.editProject;
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
                fileDisplay.innerText = newProjectI18n.currentImage + ": " + project.image_url.split('/').pop();
            }
        } else {
            img.classList.add('hidden');
            placeholder.classList.remove('hidden');
            fileDisplay.innerText = newProjectI18n.chooseLocalFile;
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

    document.getElementById('load-more-projects')?.addEventListener('click', async function() {
        const btn = this;
        const url = btn.getAttribute('data-next-page');

        if (!url) return;

        btn.innerText = `{{ __('ui.loading') }}`;

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
@endsection
