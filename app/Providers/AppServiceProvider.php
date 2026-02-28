<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        config(['auth.admin_path' => env('ADMIN_LOGIN_PATH', 'login')]);

        // 1. 定义一个用于检测连接是否可用的闭包
        $isDatabaseConfigured = function () {
            try {
                // 尝试获取 PDO 实例，如果连不上，这里会抛出异常
                DB::connection()->getPdo();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        };

        // 2. 只有当数据库配置存在且连接成功时，才检查表
        if ($isDatabaseConfigured() && Schema::hasTable('settings')) {
            try {
                $site = Setting::first() ?? new Setting();
                View::share('site', $site);

                if ($site->maintenance_mode && !Auth::check() && !request()->is('login*')) {
                    abort(503, 'System under maintenance.');
                }
            } catch (\Exception $e) {
                // 如果表存在但查询失败 (例如正在迁移中)，也分享空模型
                View::share('site', new Setting());
            }
        } else {
            // 3. 数据库未配置或表不存在，分享空模型，避免视图报错
            View::share('site', new Setting());
        }
    }
}
