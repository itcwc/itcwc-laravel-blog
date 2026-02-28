<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class InstallSystem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    // App/Jobs/InstallSystem.php

    public function handle()
    {
        set_time_limit(300);

        // 1. 更新 .env 并重置连接
        $this->updateEnvFile($this->data);

        // ... Config::set 数据库配置 ...
        DB::purge('mysql');
        DB::reconnect('mysql');

        try {
            // 2. 执行迁移
            Artisan::call('migrate:fresh', ['--force' => true]);

            // 3. 【移除了事务】直接执行填充逻辑
            if ($this->data['import_demo']) {
                $locale = $this->data['language'];
                $this->addContentData($locale);
                $this->addProjectData($locale);
                $this->addSettingData($locale);
                Setting::query()->update(['site_name' => $this->data['site_name']]);
            } else {
                Setting::create([
                    'site_url' => config('app.url'),
                    'site_name' => $this->data['site_name'],
                    'copyright' => '© ' . date('Y') . ' ' . $this->data['site_name'],
                ]);
            }

            User::create([
                'name' => 'Admin',
                'email' => $this->data['admin_email'],
                'password' => Hash::make($this->data['admin_password']),
            ]);

            file_put_contents(storage_path('installed.lock'), date('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            // 4. 记录具体的 SQL 错误
            Log::error('Installation failed: ' . $e->getMessage());
            throw $e;
        }
    }


    protected function updateEnvFile($data)
    {
        $envPath = base_path('.env');
        $content = file_get_contents($envPath);

        // 独占锁定文件写入，防止 ERR_CONNECTION_RESET
        $fp = fopen($envPath, 'r+');
        if (flock($fp, LOCK_EX)) {
            $dataToUpdate = [
                'DB_HOST' => $data['db_host'],
                'DB_PORT' => $data['db_port'],
                'DB_DATABASE' => $data['db_database'],
                'DB_USERNAME' => $data['db_username'],
                'DB_PASSWORD' => $data['db_password'] ?? '',
                'APP_LOCALE' => $data['language'],
                'APP_NAME' => $data['site_name'],
                'ADMIN_LOGIN_PATH' => $data['login_path']
            ];

            foreach ($dataToUpdate as $key => $value) {
                $content = preg_replace("/^{$key}=.*$/m", "{$key}={$value}", $content);
            }

            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, $content);
            fflush($fp);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }


    public function addContentData($locale)
    {
        DB::table('contents')->truncate();

        $contents = ($locale === 'zh_CN') ? [
            // --- 中文全量数据 ---
            ['id' => 1, 'type' => 'note', 'content' => '最好的设计，是除了必要的功能之外，再无一物。', 'keywords' => '设计, 极简', 'published_date' => '2026-02-23', 'images' => json_encode(['notes/example.jpg'])],
            ['id' => 2, 'type' => 'article', 'title' => 'Laravel 极简开发实践', 'slug' => 'laravel-minimalist-practice', 'content' => "Laravel 作为现代 PHP 框架，以优雅的语法深受喜爱。我在开发中如何保持代码极简...\n\n### 核心原则\n1. 约定优于配置\n2. 只做必要的事", 'keywords' => 'PHP, Laravel', 'read_time' => '8 min', 'published_date' => '2026-02-20'],
            ['id' => 3, 'type' => 'note', 'content' => '代码是写给人看的，顺便能运行。', 'keywords' => '编程, 态度', 'published_date' => '2026-02-18'],
            ['id' => 4, 'type' => 'article', 'title' => '极简设计的核心原则', 'slug' => 'core-principles-of-minimalist-design', 'content' => '极简设计不是简单的少，而是恰到好处的多。平衡功能与简洁是永恒话题。', 'keywords' => 'UI, 设计', 'read_time' => '5 min', 'published_date' => '2026-02-15'],
            ['id' => 5, 'type' => 'note', 'content' => '所谓大师，就是把一件简单的事做到极致。', 'keywords' => '励志', 'published_date' => '2026-02-10'],
            ['id' => 6, 'type' => 'note', 'content' => '今天在代码里发现了一个极其优雅的递归逻辑。', 'keywords' => '心情, 编程', 'published_date' => '2026-02-05'],
            ['id' => 7, 'type' => 'note', 'content' => '所谓自由，不是随心所欲，而是自我主宰。', 'keywords' => '哲学, 自由', 'published_date' => '2026-02-01'],
            ['id' => 8, 'type' => 'article', 'title' => '为什么我选择 Tailwind CSS', 'slug' => 'why-i-choose-tailwind-css', 'content' => 'Tailwind 能让我保持 HTML 语义化同时，极大减少 CSS 体积。', 'keywords' => 'Tailwind, CSS', 'read_time' => '6 min', 'published_date' => '2026-01-28'],
            ['id' => 9, 'type' => 'note', 'content' => '小步快跑，胜过原地踏步。', 'keywords' => '励志, 效率', 'published_date' => '2026-01-25'],
            ['id' => 10, 'type' => 'article', 'title' => '深入浅出 Eloquent ORM', 'slug' => 'deep-dive-eloquent-orm', 'content' => 'Eloquent 不仅是工具，更是一种思维方式。', 'keywords' => 'Laravel, 数据库', 'read_time' => '12 min', 'published_date' => '2026-01-20'],
            ['id' => 11, 'type' => 'note', 'content' => '最好的重构就是删除没用的代码。', 'keywords' => '重构, 编程', 'published_date' => '2026-01-15'],
            ['id' => 12, 'type' => 'article', 'title' => '2025 年终总结：存白', 'slug' => '2025-annual-review', 'content' => '回顾过去的一年，学会了如何与枯燥的开发过程相处。', 'keywords' => '总结, 生活', 'read_time' => '15 min', 'published_date' => '2026-01-05'],
            ['id' => 13, 'type' => 'note', 'content' => '保持饥渴，保持愚蠢。', 'keywords' => '语录', 'published_date' => '2026-01-01'],
            ['id' => 14, 'type' => 'article', 'title' => '如何优化 Laravel 查询性能', 'slug' => 'optimize-laravel-queries', 'content' => '使用 eager loading 解决 N+1 性能杀手。', 'keywords' => 'Laravel, 优化', 'read_time' => '9 min', 'published_date' => '2025-12-25'],
            ['id' => 15, 'type' => 'note', 'content' => '所有的复杂，本质上都是简单的累加。', 'keywords' => '思考', 'published_date' => '2025-12-20'],
            ['id' => 16, 'type' => 'article', 'title' => '我的数字花园搭建指南', 'slug' => 'digital-garden-guide', 'content' => '数字花园强调生长过程，这不仅是展示，更是思考的地方。', 'keywords' => '花园, 博客', 'read_time' => '7 min', 'published_date' => '2025-12-15'],
        ] : [
            // --- English Full Dataset ---
            ['id' => 1, 'type' => 'note', 'content' => 'Good design is as little design as possible.', 'keywords' => 'Design, Minimal', 'published_date' => '2026-02-23', 'images' => json_encode(['notes/example.jpg'])],
            ['id' => 2, 'type' => 'article', 'title' => 'Minimalist Laravel Development', 'slug' => 'laravel-minimalist-practice', 'content' => "Laravel is loved for its elegant syntax. Here is how I stay minimalist...\n\n### Principles\n1. Convention over Configuration\n2. Do only what is necessary", 'keywords' => 'PHP, Laravel', 'read_time' => '8 min', 'published_date' => '2026-02-20'],
            ['id' => 3, 'type' => 'note', 'content' => 'Code is for humans to read, and only incidentally for machines to execute.', 'keywords' => 'Coding, Mindset', 'published_date' => '2026-02-18'],
            ['id' => 4, 'type' => 'article', 'title' => 'Core Principles of Minimalist Design', 'slug' => 'core-principles-of-minimalist-design', 'content' => 'Minimalism is not just about less, it is about the right amount of more.', 'keywords' => 'UI, Design', 'read_time' => '5 min', 'published_date' => '2026-02-15'],
            ['id' => 5, 'type' => 'note', 'content' => 'A master is someone who does simple things to perfection.', 'keywords' => 'Inspiration', 'published_date' => '2026-02-10'],
            ['id' => 6, 'type' => 'note', 'content' => 'Found an incredibly elegant recursive logic in my code today.', 'keywords' => 'Coding, Mood', 'published_date' => '2026-02-05'],
            ['id' => 7, 'type' => 'note', 'content' => 'Freedom is not being able to do whatever you want, but being master of yourself.', 'keywords' => 'Philosophy', 'published_date' => '2026-02-01'],
            ['id' => 8, 'type' => 'article', 'title' => 'Why I Choose Tailwind CSS', 'slug' => 'why-i-choose-tailwind-css', 'content' => 'Tailwind keeps my HTML semantic while reducing CSS bundle size.', 'keywords' => 'Tailwind, CSS', 'read_time' => '6 min', 'published_date' => '2026-01-28'],
            ['id' => 9, 'type' => 'note', 'content' => 'Running in small steps is better than standing still.', 'keywords' => 'Efficiency', 'published_date' => '2026-01-25'],
            ['id' => 10, 'type' => 'article', 'title' => 'Eloquent ORM Deep Dive', 'slug' => 'deep-dive-eloquent-orm', 'content' => 'Eloquent is not just a tool, it is a way of thinking.', 'keywords' => 'Laravel, DB', 'read_time' => '12 min', 'published_date' => '2026-01-20'],
            ['id' => 11, 'type' => 'note', 'content' => 'The best refactoring is deleting unused code.', 'keywords' => 'Refactoring', 'published_date' => '2026-01-15'],
            ['id' => 12, 'type' => 'article', 'title' => '2025 Annual Review', 'slug' => '2025-annual-review', 'content' => 'Looking back at the past year, I learned to embrace the quiet process of dev.', 'keywords' => 'Review, Life', 'read_time' => '15 min', 'published_date' => '2026-01-05'],
            ['id' => 13, 'type' => 'note', 'content' => 'Stay hungry, stay foolish.', 'keywords' => 'Quotes', 'published_date' => '2026-01-01'],
            ['id' => 14, 'type' => 'article', 'title' => 'Optimizing Laravel Queries', 'slug' => 'optimize-laravel-queries', 'content' => 'Kill the N+1 problem using eager loading.', 'keywords' => 'Laravel, Perf', 'read_time' => '9 min', 'published_date' => '2025-12-25'],
            ['id' => 15, 'type' => 'note', 'content' => 'All complexity is essentially the accumulation of simplicity.', 'keywords' => 'Thinking', 'published_date' => '2025-12-20'],
            ['id' => 16, 'type' => 'article', 'title' => 'Digital Garden Guide', 'slug' => 'digital-garden-guide', 'content' => 'A garden focuses on growth rather than just publication.', 'keywords' => 'Garden, Blog', 'read_time' => '7 min', 'published_date' => '2025-12-15'],
        ];

        foreach ($contents as $data) {
            DB::table('contents')->insert(array_merge([
                'title' => null,
                'slug' => null,
                'images' => null,
                'read_time' => null,
                'created_at' => now(),
                'updated_at' => now()
            ], $data));
        }
    }

    public function addProjectData($locale)
    {
        // 清空现有项目
        DB::table('projects')->truncate();

        $projects = ($locale === 'zh_CN') ? [
            // --- 中文全量数据 ---
            [
                'title' => '极简待办 (Minimalist Todo)',
                'description' => '基于 Laravel + Vue 开发。剥离了所有繁杂的分类与推送，只保留“输入、执行、归档”三个核心动作。支持响应式布局，专注解决最本质的生产力问题。',
                'keywords' => 'Laravel, Vue, 生产力, 极简',
                'image_url' => 'https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?q=80&w=2067',
                'order' => 1,
            ],
            [
                'title' => 'RESTful API 调试工具',
                'description' => '轻量级的接口测试助手。去掉了重度功能，支持接口快速调试与响应对比。采用了极致的单页 UI，让联调回归简单，不再被复杂的配置困扰。',
                'keywords' => 'API, 工具, 后端, 调试',
                'image_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=2070',
                'order' => 2,
            ],
            [
                'title' => '沉浸式 Markdown 编辑器',
                'description' => '专注写作体验。内置 GitHub 风格 CSS，支持一键导出 PDF。所有的排版细节都经过微调，通过优雅的间距与排版，让文字本身成为视觉的主角。',
                'keywords' => 'Markdown, 写作, 文本编辑器',
                'image_url' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=2046',
                'order' => 3,
            ],
            [
                'id' => 4,
                'title' => '个人数字面板 (Data Dashboard)',
                'description' => '极简风格的数据中心。集成 GitHub 贡献图、阅读进度及习惯打卡系统。通过高度抽象的图表展示核心数据，拒绝信息过载带来的焦虑感。',
                'keywords' => '可视化, 数据, 习惯, Laravel',
                'image_url' => 'https://images.unsplash.com/photo-1551288049-bbbda546697c?q=80&w=2070',
                'order' => 4,
            ]
        ] : [
            // --- English Full Dataset ---
            [
                'title' => 'Focus Task Manager',
                'description' => 'A minimalist todo tool built with Laravel + Vue. Stripped of all noise, focusing purely on "Input, Execute, Archive". Designed to solve productivity problems at their core.',
                'keywords' => 'Laravel, Vue, Productivity, Minimal',
                'image_url' => 'https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?q=80&w=2067',
                'order' => 1,
            ],
            [
                'title' => 'Architect API Tester',
                'description' => 'A lightweight companion for API development. Designed for speed and simplicity, it features a distraction-free SPA interface to make debugging efficient and intuitive.',
                'keywords' => 'API, Tools, Backend, Debug',
                'image_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=2070',
                'order' => 2,
            ],
            [
                'title' => 'Zen Markdown Writer',
                'description' => 'An editor focused on the writing experience. Features built-in GitHub styling and one-click PDF export. Every pixel is fine-tuned to ensure typography takes center stage.',
                'keywords' => 'Markdown, Writing, Editor, UI',
                'image_url' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=2046',
                'order' => 3,
            ],
            [
                'title' => 'Pulse Personal Dashboard',
                'description' => 'A minimalist digital command center. Integrates GitHub contributions, reading progress, and habit tracking via abstract visualizations to prevent information overload.',
                'keywords' => 'Dashboard, Data, Visualization, Laravel',
                'image_url' => 'https://images.unsplash.com/photo-1551288049-bbbda546697c?q=80&w=2070',
                'order' => 4,
            ]
        ];

        foreach ($projects as $project) {
            DB::table('projects')->insert(array_merge($project, [
                'case_url' => 'https://github.com/itcwc', // 默认占位符
                'source_code_url' => 'https://github.com/itcwc',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function addSettingData($locale)
    {
        DB::table('settings')->truncate();

        $data = $locale === 'zh_CN' ? [
            'site_name'        => 'Itcwc',
            'site_slogan'      => '大道至简。',
            'seo_title'        => 'Itcwc | 极简数字花园',
            'seo_description'  => '一个关于代码、设计与极简生活的数字花园。',
            'footer_text'      => '由 Laravel 驱动。',
        ] : [
            'site_name'        => 'Itcwc',
            'site_slogan'      => 'Less, but better.',
            'seo_title'        => 'Itcwc | Minimalist Digital Garden',
            'seo_description'  => 'A digital garden about code, design, and minimalist living.',
            'footer_text'      => 'Driven by Laravel.',
        ];

        DB::table('settings')->insert(array_merge($data, [
            'site_url'         => 'https://itcwc.test',
            'social_github'    => 'https://github.com/yourname',
            'social_twitter'   => 'https://twitter.com/yourname',
            'seo_keywords'     => 'Laravel, Minimalist, PHP, Blog',
            'copyright'        => '© Itcwc STUDIO',
            'maintenance_mode' => false,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]));
    }
}
