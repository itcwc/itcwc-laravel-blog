<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\HtmlString;

class Highlight extends Component
{
    public $content;

    public function __construct($text, $term)
    {
        // 1. 先进行基本的安全处理：去掉 HTML 标签并转义特殊字符
        $safeText = e(strip_tags($text));

        if (empty($term)) {
            $this->content = $safeText;
            return;
        }

        // 2. 转义搜索词，防止正则注入
        $quotedTerm = preg_quote(e($term), '/');

        // 3. 执行替换，并包裹高亮标签
        // 这里使用 \b 匹配单词边界（可选），或者直接全局匹配
        $highlighted = preg_replace(
            "/($quotedTerm)/i",
            '<mark class="bg-zinc-100 text-zinc-900 px-0.5 rounded-sm">$1</mark>',
            $safeText
        );

        // 4. 将结果标记为安全的 HTML 字符串（因为我们已经手动 e() 过原始文本了）
        $this->content = new HtmlString($highlighted);
    }

    public function render()
    {
        return '{{ $content }}';
    }
}
