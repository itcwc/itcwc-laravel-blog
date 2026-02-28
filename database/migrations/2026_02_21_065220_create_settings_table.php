<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name'); // 网站名称
            $table->string('site_slogan')->nullable(); // 网站标语
            $table->string('site_url'); // 网站 URL
            $table->string('site_icon')->nullable(); // 网站图标
            $table->string('seo_title')->nullable(); // SEO 标题
            $table->text('seo_description')->nullable(); // SEO 描述
            $table->string('seo_keywords')->nullable(); // SEO 关键词
            $table->text('footer_text')->nullable(); // 尾部信息
            $table->string('copyright'); // 版权信息
            $table->string('social_facebook')->nullable(); // Facebook 链接
            $table->string('social_twitter')->nullable(); // Twitter 链接
            $table->string('social_github')->nullable(); // GitHub 链接
            $table->string('social_linkedin')->nullable(); // LinkedIn 链接
            $table->string('social_instagram')->nullable(); // Instagram 链接
            $table->string('share_title')->nullable(); // 分享标题
            $table->text('share_description')->nullable(); // 分享描述
            $table->string('share_image')->nullable(); // 分享图片
            $table->boolean('maintenance_mode')->default(false); // 维护模式
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
