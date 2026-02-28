<div id="quick-post-overlay" class="fixed inset-0 z-[110] bg-white/90 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-500 flex items-center justify-center p-6">

    <div class="w-full max-w-4xl bg-white border border-zinc-100 shadow-2xl p-8 md:p-12 relative">
        <button onclick="toggleQuickPost()" class="absolute top-6 right-6 text-zinc-300 hover:text-black transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <div class="flex gap-8 mb-10 border-b border-zinc-50 pb-4">
            <button onclick="switchType('note')" id="tab-note" class="text-xs tracking-[0.3em] uppercase font-bold text-black border-b border-black pb-4 -mb-[17px] transition-all">Note</button>
            <button onclick="switchType('article')" id="tab-article" class="text-xs tracking-[0.3em] uppercase text-zinc-300 hover:text-black pb-4 -mb-[17px] transition-all">Article</button>
        </div>

        <form id="quick-post-form" class="space-y-6">
            @csrf
            <!-- <input type="hidden" name="id" id="post-type" value="note"> -->

            <input type="hidden" name="id" id="post-id" value=""> <input type="hidden" name="type" id="post-type" value="note">

            <div id="title-field" class="hidden">
                <input type="text" name="title" placeholder="Enter Title..."
                    class="w-full text-2xl font-light tracking-tight border-none focus:ring-0 placeholder-zinc-100 outline-none">
            </div>

            <div>
                <textarea name="content" id="post-content" rows="6" placeholder="What's on your mind?"
                    class="w-full text-lg font-light leading-relaxed border-none focus:ring-0 placeholder-zinc-100 outline-none resize-none"></textarea>
            </div>

            <div class="flex justify-between items-center pt-6 border-t border-zinc-50">
                <div id="note-image-tools" class="flex gap-4 text-zinc-300">
                    <input type="file" id="image-upload-input" multiple accept="image/*" class="hidden" onchange="handleFileSelect(this)">

                    <button type="button" onclick="document.getElementById('image-upload-input').click()" class="hover:text-black transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                    </button>

                </div>

                <div id="image-preview-grid" class="grid grid-cols-3 gap-2 mt-4 hidden"></div>

                <div class="ml-auto">
                    <button type="button" onclick="submitPost()" class="text-[10px] tracking-[0.4em] uppercase bg-black text-white px-8 py-3 hover:bg-zinc-800 transition shadow-lg active:scale-95">
                        Publish
                    </button>

                    <button type="button" id="delete-article-btn" onclick="deleteCurrentPost()" class="ml-4 px-8 py-3 border border-red-100 text-red-400 text-[10px] uppercase tracking-widest hover:bg-red-50 transition">
                        Delete
                    </button>

                </div>
            </div>
        </form>
    </div>
</div>



<script>
    let currentType = 'note';

    const overlay = document.getElementById('quick-post-overlay');
    const typeInput = document.getElementById('post-type');
    const titleField = document.getElementById('title-field');
    const contentArea = document.getElementById('post-content');

    // function toggleQuickPost() {
    //     const isActive = overlay.classList.contains('opacity-100');
    //     overlay.classList.toggle('opacity-0', isActive);
    //     overlay.classList.toggle('pointer-events-none', isActive);
    //     overlay.classList.toggle('opacity-100', !isActive);
    //     overlay.classList.toggle('pointer-events-auto', !isActive);
    //     if (!isActive) setTimeout(() => contentArea.focus(), 300);
    // }

    async function submitPost() {

        // // 如果编辑器存在，将 Markdown 内容同步回原生的 textarea
        // if (easyMDE) {
        //     document.getElementById('post-content').value = easyMDE.value();
        // }

        // const formData = new FormData(document.getElementById('quick-post-form'));


        if (easyMDE && currentType === 'article') {
            document.getElementById('post-content').value = easyMDE.value();
        }

        const form = document.getElementById('quick-post-form');
        const formData = new FormData(form);

        // 将选中的图片文件加入 FormData
        selectedFiles.forEach(file => {
            formData.append('note_images[]', file);
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
                location.reload(); // 发布成功后直接刷新页面
            } else {
                alert('Publish failed, please check fields.');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function handleEdit(button) {
        // 1. 从 data-item 属性中获取字符串
        const rawData = button.getAttribute('data-item');

        // console.log(rawData);

        try {
            // 2. 将字符串解析为 JSON 对象
            const data = JSON.parse(rawData);
            // console.log(data);

            // 3. 调用你原来的 editContent 函数
            editContent(data);
        } catch (e) {
            console.error("JSON 解析失败，内容可能包含特殊字符:", e);
        }
    }

    function editContent(data) {
        document.getElementById('post-id').value = data.id;

        switchType(data.type);

        document.querySelector('button[onclick="submitPost()"]').innerText = "Update";

        document.getElementById('image-preview-grid').classList.add('hidden');

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
        }
    }

    let easyMDE = null;

    function initEditor() {
        const contentArea = document.getElementById('post-content');

        if (currentType === 'article' && !easyMDE) {
            easyMDE = new EasyMDE({
                element: contentArea,
                uploadImage: true, // 开启上传功能
                imageAccept: "image/png, image/jpeg, image/gif",
                imageUploadFunction: function(file, onSuccess, onError) {
                    const formData = new FormData();
                    formData.append('image', file);

                    const token = document.querySelector('meta[name="csrf-token"]')?.content;

                    if (!token) {
                        console.error('CSRF token not found');
                        return onError("Security token missing");
                    }

                    console.log(token);

                    fetch("{{ route('image.upload') }}", {
                            method: "POST",
                            body: formData,

                            headers: {
                                // 关键：在 Header 中加入 Token
                                'X-CSRF-TOKEN': token,
                                // 确保这是一个 AJAX 请求
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.url) {
                                onSuccess(data.url); // 将返回的 URL 插入编辑器
                            } else {
                                onError("上传失败");
                            }
                        })
                        .catch(() => onError("网络错误"));
                },
                // 其他配置保持不变...
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

        // 切换时清空 Note 模式下的图片缓存
        selectedFiles = [];
        document.getElementById('image-preview-grid').innerHTML = '';
        document.getElementById('image-preview-grid').classList.add('hidden');

        // 关键修复：同步隐藏域的值，这样后端才能收到正确的 type
        if (typeInput) {
            typeInput.value = type;
        }

        // UI Tab 样式切换 (保持你原有的逻辑)
        const tabArticle = document.getElementById('tab-article');
        const tabNote = document.getElementById('tab-note');

        [tabArticle, tabNote].forEach(tab => {
            tab.classList.remove('font-bold', 'text-black', 'border-b', 'border-black');
            tab.classList.add('text-zinc-300');
        });

        const activeTab = type === 'article' ? tabArticle : tabNote;
        activeTab.classList.add('font-bold', 'text-black', 'border-b', 'border-black');
        activeTab.classList.remove('text-zinc-300');

        // 显示/隐藏标题
        titleField.classList.toggle('hidden', type === 'note');

        const noteImageTools = document.getElementById('note-image-tools');
        const imagePreviewGrid = document.getElementById('image-preview-grid');

        // 切换占位符
        // contentArea.placeholder = type === 'article' ? "Start writing your article..." : "What's on your mind?";

        if (type === 'article') {
            noteImageTools.classList.add('hidden'); // 隐藏上传按钮
            imagePreviewGrid.classList.add('hidden'); // 隐藏预览网格
            contentArea.placeholder = "Start writing your article...";
        } else {
            noteImageTools.classList.remove('hidden'); // 显示上传按钮
            // 如果有已选图片，显示预览网格
            if (selectedFiles.length > 0) {
                imagePreviewGrid.classList.remove('hidden');
            }
            contentArea.placeholder = "What's on your mind?";
        }

        initEditor();
    }

    let selectedFiles = [];

    function handleFileSelect(input) {
        const grid = document.getElementById('image-preview-grid');
        const files = Array.from(input.files);

        // 如果是 Note，支持多图（限制9张）；如果是 Article，你可以选择单图作为封面，或者直接忽略（因为 Article 图片通常走编辑器）
        if (currentType === 'note') {
            selectedFiles = files.slice(0, 9);
        } else {
            selectedFiles = [files[0]]; // 文章类型暂设为单封面图
        }

        renderPreview();
    }

    function renderPreview() {
        const grid = document.getElementById('image-preview-grid');
        grid.innerHTML = '';

        if (selectedFiles.length > 0) {
            grid.classList.remove('hidden');
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = "relative aspect-square bg-zinc-100 overflow-hidden group";
                    div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 bg-black/50 text-white p-1 opacity-0 group-hover:opacity-100 transition">×</button>
                `;
                    grid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        } else {
            grid.classList.add('hidden');
        }
    }

    function removeImage(index) {
        selectedFiles.splice(index, 1);
        renderPreview();
    }

    /**
     * 通用删除处理
     * @param {string} type - 'articles' 或 'notes'
     * @param {number} id - ID
     * @param {HTMLElement} element - 要移除的 DOM 元素引用
     */
    async function deleteCurrentPost() {
        const id = document.getElementById('post-id').value;

        if (!id) {
            alert('无法获取内容ID');
            return;
        }

        if (!confirm('确定要删除这篇内容吗？')) return;

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
            alert('删除失败，请重试: ' + error);
        }
    }

    async function confirmGeneralDelete(id, element = null) {

        if (!confirm(`确定要删除这篇内容吗？`)) return;

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
                // 如果传入了元素，执行淡出动画
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
            alert('删除失败，请重试' + error);
        }
    }
</script>

<style>
    .editor-toolbar {
        border-color: #f4f4f5 !important;
        /* zinc-100 */
        border-radius: 0 !important;
        opacity: 0.6;
        transition: opacity 0.3s;
    }

    .editor-toolbar:hover {
        opacity: 1;
    }

    .CodeMirror {
        border-color: #f4f4f5 !important;
        border-radius: 0 !important;
        font-family: 'Inter', sans-serif;
    }

    .CodeMirror-editor-wrapper+textarea {
        display: none !important;
    }
</style>
