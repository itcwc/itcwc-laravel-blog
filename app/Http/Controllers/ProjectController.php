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

        $projects = $query->paginate(4);

        if ($request->ajax()) {
            return view('projects.partials.list', compact('projects'))->render();
        }

        return view('projects.index', compact('projects', 'keyword'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'keywords'          => 'nullable|string',
            'image'             => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'case_url'          => 'nullable|url',
            'source_code_url'   => 'nullable|url',
            'order'             => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('projects', 'public');
            $validated['image_url'] = $path;
        }

        Project::create($validated);

        return response()->json(['success' => true, 'message' => __('project.created_successfully')]);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'keywords'          => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'case_url'          => 'nullable|url',
            'source_code_url'   => 'nullable|url',
            'order'             => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            if ($project->image_url && !str_starts_with($project->image_url, 'http')) {
                Storage::disk('public')->delete($project->image_url);
            }

            $path = $request->file('image')->store('projects', 'public');
            $validated['image_url'] = $path;
        }

        $project->update($validated);

        return response()->json(['success' => true, 'message' => __('project.updated_successfully')]);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        if ($project->image_url && !str_starts_with($project->image_url, 'http')) {
            Storage::disk('public')->delete($project->image_url);
        }

        $project->delete();

        return response()->json(['success' => true]);
    }
}
