<?php

namespace App\Http\Controllers;

use App\Models\Content;
use Illuminate\Http\Request;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

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

        // 网站基础介绍 默认开启
        $featured = 1;

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

        $article->contentMd = $this->markdownToHtml($article->content);

        return view('article', compact('article'));
    }

    public function  markdownToHtml(string $markdown): string
    {
        $environment = new Environment([]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $converter = new MarkdownConverter($environment);

        return (string) $converter->convert($markdown);
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
