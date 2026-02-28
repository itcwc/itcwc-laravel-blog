<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContentsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuickEditController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\InstallController;

$adminPath = config('auth.admin_path', 'login');


Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'checkRequirements'])->name('index');
    Route::get('/setup', [InstallController::class, 'showSetupForm'])->name('setup');
    Route::post('/do-setup', [InstallController::class, 'setup'])->name('do-setup');
    Route::get('/success', [InstallController::class, 'success'])->name('success');
});


Route::middleware('web')->group(function () use ($adminPath) {
    // 动态登录路由
    Route::get($adminPath, [AuthController::class, 'showLogin'])->name('login');
    Route::post($adminPath, [AuthController::class, 'login']);

    // 如果用户修改了默认地址，原 /login 直接报 404
    if ($adminPath !== 'login') {
        Route::redirect('/login', '/404');
    }

    Route::get('/', [ContentsController::class, 'index'])->name('index')->name('home');
});

// 原有的 /login 建议重定向到 404 或者首页，增加隐蔽性
if ($adminPath !== 'login') {
    Route::any('/login', function () {
        abort(404);
    });
}
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [ContentsController::class, 'index'])->name('index');
Route::get('/article/{slug}', [ContentsController::class, 'show'])->name('article.show');
Route::get('/note', [ContentsController::class, 'note'])->name('note');
Route::get('/blog', [ContentsController::class, 'blog'])->name('blog');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects');

Route::middleware('auth')->group(function () {

    Route::post('/api/projects/store', [ProjectController::class, 'store']);
    Route::post('/api/projects/update/{id}', [ProjectController::class, 'update']);
    Route::delete('/api/projects/delete/{id}', [ProjectController::class, 'destroy']);

    Route::post('/api/upload-image', [UploadController::class, 'uploadImage'])->name('image.upload');
    Route::post('/api/content/save', [QuickEditController::class, 'save']);
    Route::delete('/api/content/delete/{id}', [QuickEditController::class, 'destroy']);
    Route::post('/api/settings/update', [SettingsController::class, 'update']);
});
