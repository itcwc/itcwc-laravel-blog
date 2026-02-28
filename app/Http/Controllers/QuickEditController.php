<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class QuickEditController extends Controller
{
    public function save(Request $request)
    {
        $validated = $request->validate([
            'id'            => 'nullable|exists:contents,id',
            'type'          => 'required|in:note,article',
            'title'         => 'required_if:type,article|nullable|string|max:255',
            'content'       => 'required|string',
            'note_images'   => 'nullable|array',
            'note_images.*' => 'image|max:10240', // 限制单张大小，可选
            'existing_images' => 'nullable|array', // 接收前端传回的保留下来的旧图片路径
        ]);

        // 1. 获取或创建模型
        $content = $request->id ? Content::findOrFail($request->id) : new Content();

        // 2. 如果是编辑模式，处理旧图片的物理删除
        if ($request->id) {
            $this->handleImageDeletion($content, $request->existing_images ?? []);
        }

        // 3. 基础赋值
        $content->type = $validated['type'];
        $content->title = $validated['title'] ?? null; // 使用 null 合并运算符防止文章模式下标题为空报错
        $content->content = $validated['content'];

        // 4. 处理图片逻辑
        $finalImagePaths = $request->existing_images ?? []; // 初始化为保留下来的图片

        if ($request->hasFile('note_images')) {
            foreach ($request->file('note_images') as $image) {
                $finalImagePaths[] = $image->store('notes', 'public');
            }
        }

        // 将最终图片列表保存为 JSON
        $content->images = json_encode($finalImagePaths);

        // 5. Slug 和日期逻辑 (保持不变)
        if (!$request->id) {
            $content->published_date = now();
            if ($validated['type'] === 'article') {
                $content->slug = Str::slug($validated['title']) ?: time();
            }
        }

        $content->save();

        return response()->json(['success' => true]);
    }

    /**
     * 物理删除不再使用的图片
     */
    protected function handleImageDeletion(Content $content, array $retainedImages)
    {
        // 获取数据库中已存在的图片
        $currentImages = $content->images;

        // 计算出需要被删除的图片：当前存在的 - 前端提交保留的
        $imagesToDelete = array_diff($currentImages, $retainedImages);

        foreach ($imagesToDelete as $imagePath) {
            // 从 storage/app/public/... 中删除物理文件
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }
    }

    public function destroy($id)
    {
        $content = Content::findOrFail($id);

        // 删除存储在本地的文件
        if ($content->images) {

            if (is_array($content->images)) {
                foreach ($content->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            } else {
                Storage::disk('public')->delete($content->images);
            }
        }

        $content->delete();

        return response()->json(['success' => true]);
    }
}
