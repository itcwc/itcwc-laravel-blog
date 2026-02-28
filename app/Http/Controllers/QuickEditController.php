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
            'id'      => 'nullable|exists:contents,id', // 如果有 ID，就是编辑模式
            'type'    => 'required|in:note,article',
            'title'   => 'required_if:type,article|nullable|string|max:255',
            'content' => 'required|string',
            'note_images'  => 'nullable|array',
        ]);

        // 如果有 ID 则寻找模型，否则新建
        $content = $request->id ? Content::findOrFail($request->id) : new Content();

        $content->type = $validated['type'];
        $content->title = $validated['title'];
        $content->content = $validated['content'];


        $data = [];

        if ($request->hasFile('note_images')) {
            $paths = [];
            foreach ($request->file('note_images') as $image) {
                $paths[] = $image->store('notes', 'public');
            }
            $data['images'] = $paths; // 确保模型中 images 字段已加 cast 为 array 或 json
        }

        if (count($data)) {
            $content->images = json_encode($data['images']);
        }

        // 仅在新建且为文章时生成日期和 Slug
        if (!$request->id) {
            $content->published_date = now();
            if ($validated['type'] === 'article') {
                $content->slug = Str::slug($validated['title']) ?: time();
            }
        }

        $content->save();

        return response()->json(['success' => true]);
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
