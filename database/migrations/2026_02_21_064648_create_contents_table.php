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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // article 或 note
            $table->string('title')->nullable(); // 标题，note 类型可能为空
            $table->string('slug')->nullable()->unique();
            $table->text('content'); // 内容，article 为长文本，note 为一句话
            $table->text('images')->nullable();
            $table->string('keywords')->nullable(); // 关键词，用于搜索
            $table->string('read_time')->nullable(); // 阅读时间，仅 article 类型使用
            $table->date('published_date'); // 发布日期
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
