<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use Spatie\LaravelMarkdown\MarkdownRenderer; // 假设你之后会用这个，或用原生解析

class ContentsController extends Controller
{
    /**
     * 首页：混合内容流
     */
    public function index(Request $request)
    {
        $query = Content::query();

        // 1. 获取搜索关键词
        $keyword = $request->input('q');

        // 2. 如果有关键词，执行模糊查询
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%")
                    ->orWhere('keywords', 'like', "%{$keyword}%");
            });
        }

        // 3. 排序并分页 (分页会自动保留查询参数)
        $contents = $query->orderBy('published_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // 关键：让分页链接带上 ?q=xxx

        // 只有在没有搜索时才显示最新的一句话
        $featured = $keyword ? null : Content::where('type', 'note')->latest()->first();

        return view('index', compact('contents', 'featured', 'keyword'));
    }

    /**
     * 文章详情页
     */
    public function show($slug)
    {
        // 兼容 Slug 或 ID 查询
        $article = Content::where('type', 'article')
            ->where(function ($query) use ($slug) {
                $query->where('slug', $slug)->orWhere('id', $slug);
            })->firstOrFail();

        return view('article', compact('article'));
    }

    /**
     * 笔记页
     */
    public function note()
    {
        $contents = Content::where('type', 'note')
            ->orderBy('published_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('note', compact('contents'));
    }

    /**
     * 博客页
     */
    public function blog()
    {
        $contents = Content::where('type', 'article')
            ->orderBy('published_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('blog', compact('contents'));
    }
}
