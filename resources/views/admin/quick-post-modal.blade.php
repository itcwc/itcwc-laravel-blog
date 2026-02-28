<div id="quick-post-overlay" class="fixed inset-0 z-[110] bg-white/90 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-500 flex items-center justify-center p-6">
    <div class="w-full max-w-4xl bg-white border border-zinc-100 shadow-2xl p-8 md:p-12 relative">
        <button onclick="toggleQuickPost()" class="absolute top-6 right-6 text-zinc-300 hover:text-black transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <div class="flex gap-8 mb-10 border-b border-zinc-50 pb-4">
            <button onclick="switchType('note')" id="tab-note" class="text-xs tracking-[0.3em] uppercase font-bold text-black border-b border-black pb-4 -mb-[17px] transition-all">{{ __('content.note') }}</button>
            <button onclick="switchType('article')" id="tab-article" class="text-xs tracking-[0.3em] uppercase text-zinc-300 hover:text-black pb-4 -mb-[17px] transition-all">{{ __('content.article') }}</button>
        </div>

        <form id="quick-post-form" class="space-y-6">
            @csrf
            <input type="hidden" name="id" id="post-id" value="">
            <input type="hidden" name="type" id="post-type" value="note">

            <div id="title-field" class="hidden">
                <input type="text" name="title" placeholder="{{ __('content.enter_title') }}"
                    class="w-full text-2xl font-light tracking-tight border-none focus:ring-0 placeholder-zinc-100 outline-none">
            </div>

            <div>
                <textarea name="content" id="post-content" rows="6" placeholder="{{ __('content.whats_on_your_mind') }}"
                    class="w-full text-lg font-light leading-relaxed border-none focus:ring-0 placeholder-zinc-100 outline-none resize-none"></textarea>
            </div>

            <div id="image-preview-grid" class="grid grid-cols-4 md:grid-cols-6 gap-3 mb-6 hidden">
            </div>

            <div class="pt-6 border-t border-zinc-50 space-y-8">
                <div id="note-image-tools" class="flex items-center gap-4 text-zinc-300">
                    <input type="file" id="image-upload-input" multiple accept="image/*" class="hidden" onchange="handleFileSelect(this)">
                    <button type="button" onclick="document.getElementById('image-upload-input').click()" class="hover:text-black transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <span class="text-[10px] uppercase tracking-widest">{{ __('content.add_images') }}</span>
                    </button>
                </div>

                <div class="flex items-center justify-end gap-4 w-full">
                    <button type="button" id="delete-article-btn" onclick="deleteCurrentPost()" class="hidden px-8 py-3 border border-red-100 text-red-400 text-[10px] uppercase tracking-widest hover:bg-red-50 transition active:scale-95">
                        {{ __('content.delete') }}
                    </button>

                    <button type="button" onclick="submitPost()" class="bg-black text-white px-10 py-3 text-[10px] tracking-[0.4em] uppercase hover:bg-zinc-800 transition shadow-lg active:scale-95">
                        {{ __('content.publish') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>



<script>
    const i18n = {
        note: '{{ __('content.note') }}',
        article: '{{ __('content.article') }}',
        enterTitle: '{{ __('content.enter_title') }}',
        whatsOnYourMind: '{{ __('content.whats_on_your_mind') }}',
        startWritingArticle: '{{ __('content.start_writing_article') }}',
        publish: '{{ __('content.publish') }}',
        update: '{{ __('content.update') }}',
        delete: '{{ __('content.delete') }}',
        publishFailed: '{{ __('content.publish_failed') }}',
        jsonParseFailed: '{{ __('content.json_parse_failed') }}',
        uploadFailed: '{{ __('content.upload_failed') }}',
        networkError: '{{ __('content.network_error') }}',
        securityTokenMissing: '{{ __('content.security_token_missing') }}',
        cannotGetContentId: '{{ __('content.cannot_get_content_id') }}',
        confirmDelete: '{{ __('content.confirm_delete') }}',
        deleteFailed: '{{ __('content.delete_failed ') }}'
    };

    let currentType = 'note';

    const overlay = document.getElementById('quick-post-overlay');
    const typeInput = document.getElementById('post-type');
    const titleField = document.getElementById('title-field');
    const contentArea = document.getElementById('post-content');

    async function submitPost() {
        if (easyMDE && currentType === 'article') {
            document.getElementById('post-content').value = easyMDE.value();
        }

        const form = document.getElementById('quick-post-form');
        const formData = new FormData(form);

        selectedFiles.forEach(file => {
            formData.append('note_images[]', file);
        });

        existingImages.forEach(img => {
            formData.append('existing_images[]', img);
        });

        try {
            const response = await fetch('/api/content/save', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                location.reload();
            } else {
                alert(i18n.publishFailed);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function handleEdit(button) {
        const rawData = button.getAttribute('data-item');

        try {
            const data = JSON.parse(rawData);
            editContent(data);
        } catch (e) {
            console.error(i18n.jsonParseFailed, e);
        }
    }

    function editContent(data) {
        document.getElementById('post-id').value = data.id;
        switchType(data.type);

        document.querySelector('button[onclick="submitPost()"]').innerText = i18n.update;

        // 处理编辑模式下的图片预览
        selectedFiles = []; // 清空新选择
        existingImages = data.images || []; // 假设数据返回了 images 数组

        if (data.type === 'note') {
            renderPreview();
        }

        if (data.type === 'article') {
            document.querySelector('[name="title"]').value = data.title || '';
            setTimeout(() => {
                if (easyMDE) {
                    easyMDE.value(data.content || '');
                } else {
                    document.getElementById('post-content').value = data.content || '';
                }
            }, 150);
        } else {
            document.getElementById('post-content').value = data.content || '';
        }

        toggleQuickPost(true);
    }

    function toggleQuickPost(isEdit = false) {
        const isActive = overlay.classList.contains('opacity-100');
        const deleteBtn = document.getElementById('delete-article-btn');

        if (isActive) {
            if (easyMDE) {
                easyMDE.toTextArea();
                easyMDE = null;
            }
            document.getElementById('quick-post-form').reset();
            document.getElementById('post-id').value = "";

            overlay.classList.replace('opacity-100', 'opacity-0');
            overlay.classList.add('pointer-events-none');
            if (deleteBtn) deleteBtn.classList.add('hidden');
        } else {
            overlay.classList.replace('opacity-0', 'opacity-100');
            overlay.classList.remove('pointer-events-none');
            if (deleteBtn) {
                deleteBtn.classList.toggle('hidden', !isEdit);
            }
            initEditor();
        }
    }

    let easyMDE = null;

    function initEditor() {
        const contentArea = document.getElementById('post-content');

        if (currentType === 'article' && !easyMDE) {
            easyMDE = new EasyMDE({
                element: contentArea,
                uploadImage: true,
                imageAccept: "image/png, image/jpeg, image/gif",
                imageUploadFunction: function(file, onSuccess, onError) {
                    const formData = new FormData();
                    formData.append('image', file);

                    const token = document.querySelector('meta[name="csrf-token"]')?.content;

                    if (!token) {
                        console.error('CSRF token not found');
                        return onError(i18n.securityTokenMissing);
                    }

                    fetch("{{ route('image.upload') }}", {
                            method: "POST",
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.url) {
                                onSuccess(data.url);
                            } else {
                                onError(i18n.uploadFailed);
                            }
                        })
                        .catch(() => onError(i18n.networkError));
                },
                spellChecker: false,
                toolbar: ["bold", "italic", "heading", "|", "quote", "unordered-list", "link", "image", "|", "preview", "side-by-side", "fullscreen"],
            });
        } else if (currentType === 'note' && easyMDE) {
            easyMDE.toTextArea();
            easyMDE = null;
        }
    }

    function switchType(type) {
        currentType = type;

        // 注意：这里不要重置 selectedFiles 和 existingImages，否则切换选项卡数据就丢了
        // 如果你希望切换选项卡时保留图片，请注释掉下面这两行：
        // selectedFiles = [];
        // existingImages = [];

        if (typeInput) {
            typeInput.value = type;
        }

        const tabArticle = document.getElementById('tab-article');
        const tabNote = document.getElementById('tab-note');

        // 切换 Tab 样式
        [tabArticle, tabNote].forEach(tab => {
            tab.classList.remove('font-bold', 'text-black', 'border-b', 'border-black');
            tab.classList.add('text-zinc-300');
        });

        const activeTab = type === 'article' ? tabArticle : tabNote;
        activeTab.classList.add('font-bold', 'text-black', 'border-b', 'border-black');
        activeTab.classList.remove('text-zinc-300');

        titleField.classList.toggle('hidden', type === 'note');

        const noteImageTools = document.getElementById('note-image-tools');
        const imagePreviewGrid = document.getElementById('image-preview-grid');

        if (type === 'article') {
            noteImageTools.classList.add('hidden');
            imagePreviewGrid.classList.add('hidden');
            contentArea.placeholder = i18n.startWritingArticle;
        } else {
            noteImageTools.classList.remove('hidden');
            contentArea.placeholder = i18n.whatsOnYourMind;
        }

        initEditor();

        if (type === 'note' && (selectedFiles.length > 0 || (typeof existingImages !== 'undefined' && existingImages.length > 0))) {
            imagePreviewGrid.classList.remove('hidden');
            renderPreview();
        }
    }
    let selectedFiles = []; // 存储新选择的文件
    let existingImages = []; // 存储编辑模式下的旧图片

    function handleFileSelect(input) {
        const grid = document.getElementById('image-preview-grid');
        const files = Array.from(input.files);

        if (currentType === 'note') {
            selectedFiles = files.slice(0, 9);
        } else {
            selectedFiles = [files[0]];
        }

        renderPreview();
    }

    function renderPreview() {
        const grid = document.getElementById('image-preview-grid');
        grid.innerHTML = '';

        // 如果没有图片，隐藏容器
        if (selectedFiles.length === 0 && existingImages.length === 0) {
            grid.classList.add('hidden');
            return;
        }

        grid.classList.remove('hidden');

        // 渲染已有的图片 (编辑模式)
        existingImages.forEach((imgUrl, index) => {
            const div = createPreviewElement(imgUrl, () => {
                existingImages.splice(index, 1);
                renderPreview();
            });
            grid.appendChild(div);
        });

        // 渲染新上传的图片预览
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const div = createPreviewElement(e.target.result, () => {
                    selectedFiles.splice(index, 1);
                    renderPreview();
                });
                grid.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    // 辅助函数：创建预览 DOM
    function createPreviewElement(src, onRemove) {
        const isBase64 = src.startsWith('data:');
        const isFullUrl = src.startsWith('http');

        const finalSrc = (isBase64 || isFullUrl)
            ? src
            : `{{ asset('storage') }}/${src.replace(/^\//, '')}`;

        const div = document.createElement('div');
        // 增加阴影和圆角，让容器更有质感
        div.className = "relative aspect-square bg-zinc-50 border border-zinc-100 overflow-hidden group transition-all hover:shadow-md hover:border-zinc-300 rounded-sm";

        div.innerHTML = `
            <img src="${finalSrc}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">

            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>

            <button type="button"
                class="absolute top-1.5 right-1.5 bg-black text-white w-6 h-6 rounded-full flex items-center justify-center
                       shadow-xl opacity-0 group-hover:opacity-100 transition-all transform hover:scale-110 active:scale-90 z-10"
                title="Remove image">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        `;

        div.querySelector('button').onclick = (e) => {
            e.preventDefault();
            e.stopPropagation(); // 防止触发其他事件
            onRemove();
        };

        return div;
    }

    function removeImage(index) {
        selectedFiles.splice(index, 1);
        renderPreview();
    }

    async function deleteCurrentPost() {
        const id = document.getElementById('post-id').value;

        if (!id) {
            alert(i18n.cannotGetContentId);
            return;
        }

        if (!confirm(i18n.confirmDelete)) return;

        try {
            const response = await fetch(`/api/content/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                toggleQuickPost();
                location.reload();
            }
        } catch (error) {
            alert(i18n.deleteFailed + ': ' + error);
        }
    }

    async function confirmGeneralDelete(id, element = null) {
        if (!confirm(i18n.confirmDelete)) return;

        try {
            const response = await fetch(`/api/content/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (response.ok && result.success) {
                if (element) {
                    element.style.opacity = '0';
                    element.style.transform = 'translateX(20px)';
                    element.style.transition = 'all 0.4s ease';
                    setTimeout(() => element.remove(), 400);
                } else {
                    location.reload();
                }
            }
        } catch (error) {
            alert(i18n.deleteFailed + ': ' + error);
        }
    }
</script>

<style>
    .editor-toolbar {
        border-color: #f4f4f5 !important;
        border-radius: 0 !important;
        opacity: 0.6;
        transition: opacity 0.3s;
    }

    .editor-toolbar:hover {
        opacity: 1;
    }

/* 限制编辑器最大高度，超出显示滚动条 */
    .CodeMirror {
        min-height: 150px; /* 设置一个最小高度 */
        max-height: 500px; /* 设置一个最大高度，根据你的界面调整 */
        height: auto;      /* 允许初始高度根据内容自适应 */
        border-color: #f4f4f5 !important;
        border-radius: 0 !important;
        font-family: 'Inter', sans-serif;
        overflow-y: hidden; /* 隐藏CodeMirror本身的溢出 */
    }

    /* 限制滚动区域的高度 */
    .CodeMirror-scroll {
        max-height: 500px; /* 必须与 .CodeMirror 的 max-height 一致 */
        overflow-y: auto;  /* 允许垂直滚动 */
    }

    /* 隐藏原生的 textarea */
    .CodeMirror-editor-wrapper+textarea {
        display: none !important;
    }

    /* 修复全屏模式下的层级问题 */
    .CodeMirror-fullscreen {
        z-index: 9999 !important;
    }
</style>
