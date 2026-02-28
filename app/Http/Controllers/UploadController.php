<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 限制 2MB
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // 存储到 public/uploads 目录下
            $path = $file->store('uploads', 'public');

            return response()->json([
                'url' => env('APP_URL') . Storage::url($path)
            ]);
        }
        return response()->json(['error' => 'Upload failed'], 400);
    }
}
