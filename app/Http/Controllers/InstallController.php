<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\InstallSystem;
use Illuminate\Support\Facades\Bus;

class InstallController extends Controller
{
    public function setup(Request $request)
    {
        if (session('install_locale')) {
            app()->setLocale(session('install_locale'));
        }

        $data = $request->validate([
            'db_host' => 'required',
            'db_port' => 'required|integer',
            'db_database' => 'required',
            'db_username' => 'required',
            'db_password' => 'nullable',
            'site_name' => 'required|string|max:50',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:8',
            'login_path' => 'required|alpha_dash',
            'language' => 'required|in:en,zh_CN',
            'import_demo' => 'nullable|boolean',
        ]);

        try {
            $dsn = "mysql:host={$data['db_host']};port={$data['db_port']};charset=utf8mb4";
            $pdo = new \PDO($dsn, $data['db_username'], $data['db_password'] ?? '', [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 5,
            ]);

            $dbName = $data['db_database'];
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (\PDOException $e) {
            return response()->json([
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ], 422);
        }

        Bus::chain([
            new InstallSystem($data),
        ])->dispatch();

        return response()->json([
            'status' => 'queued',
            'message' => 'Installation started in background.',
            'redirect' => route('install.success', ['email' => $data['admin_email']])
        ]);
    }

    public function success(Request $request)
    {
        if (session('install_locale')) {
            app()->setLocale(session('install_locale'));
        }

        $admin_email = $request->get('email');
        return view('install.success', compact('admin_email'));
    }


    public function showSetupForm(Request $request)
    {
        if ($request->has('lang') && in_array($request->lang, ['en', 'zh_CN'])) {
            app()->setLocale($request->lang);
            session(['install_locale' => $request->lang]);
        } elseif (session('install_locale')) {
            app()->setLocale(session('install_locale'));
        }

        return view('install.setup');
    }

    public function checkRequirements(Request $request)
    {
        if ($request->has('lang') && in_array($request->lang, ['en', 'zh_CN'])) {
            app()->setLocale($request->lang);
            session(['install_locale' => $request->lang]);
        } elseif (session('install_locale')) {
            app()->setLocale(session('install_locale'));
        }

        // 检查 Redis 是否可用 (如果配置了 Redis 队列)
        $redisPassed = true;
        if (config('queue.default') === 'redis') {
            try {
                \Illuminate\Support\Facades\Redis::ping();
            } catch (\Exception $e) {
                $redisPassed = false;
            }
        }

        $requirements = [
            'php' => [
                'name' => 'PHP Version >= 8.2',
                'status' => version_compare(PHP_VERSION, '8.2.0', '>='),
                'current' => PHP_VERSION
            ],
            'extensions' => [
                'pdo_mysql' => extension_loaded('pdo_mysql'),
                'mbstring'  => extension_loaded('mbstring'),
                'openssl'   => extension_loaded('openssl'),
                'gd'        => extension_loaded('gd'),
                'fileinfo'  => extension_loaded('fileinfo'),
                'redis'     => extension_loaded('redis'), // 检查PHP Redis扩展
            ],
            'permissions' => [
                'storage' => is_writable(storage_path()),
                'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
                '.env' => is_writable(base_path('.env')) || is_writable(base_path()),
            ],
            'redis' => [
                'name' => 'Redis Connection',
                'status' => $redisPassed,
            ]
        ];

        // 检查所有项目是否通过
        $allPassed = $requirements['php']['status'] &&
            !in_array(false, $requirements['extensions']) &&
            !in_array(false, $requirements['permissions']) &&
            $requirements['redis']['status'];

        return view('install.check', compact('requirements', 'allPassed'));
    }
}
