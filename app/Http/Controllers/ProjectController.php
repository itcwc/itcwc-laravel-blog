<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');

        $query = Project::orderBy('order', 'asc')->orderBy('created_at', 'desc');

        if ($keyword) {
            $query->where('keywords', 'like', "%{$keyword}%")
                ->orWhere('title', 'like', "%{$keyword}%");
        }

        // 使用分页，以便配合你的 Load More 功能
        $projects = $query->paginate(4);

        if ($request->ajax()) {
            return view('projects.partials.list', compact('projects'))->render();
        }

        return view('projects.index', compact('projects', 'keyword'));
    }

    /**
     * 保存新项目
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'keywords'          => 'nullable|string',
            'image'             => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 必填图片
            'case_url'          => 'nullable|url',
            'source_code_url'   => 'nullable|url',
            'order'             => 'nullable|integer',
        ]);

        // 处理文件上传
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('projects', 'public');
            $validated['image_url'] = $path;
        }

        Project::create($validated);

        return response()->json(['success' => true, 'message' => 'Project created successfully.']);
    }

    /**
     * 更新现有项目
     */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'keywords'          => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 更新时可选
            'case_url'          => 'nullable|url',
            'source_code_url'   => 'nullable|url',
            'order'             => 'nullable|integer',
        ]);

        // 处理文件更新
        if ($request->hasFile('image')) {
            // 1. 删除旧图片（如果存在且是本地存储的文件）
            if ($project->image_url && !str_starts_with($project->image_url, 'http')) {
                Storage::disk('public')->delete($project->image_url);
            }

            // 2. 存储新图片
            $path = $request->file('image')->store('projects', 'public');
            $validated['image_url'] = $path;
        }

        $project->update($validated);

        return response()->json(['success' => true, 'message' => 'Project updated successfully.']);
    }

    /**
     * 删除项目
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        // 删除存储在本地的文件
        if ($project->image_url && !str_starts_with($project->image_url, 'http')) {
            Storage::disk('public')->delete($project->image_url);
        }

        $project->delete();

        return response()->json(['success' => true]);
    }
}
