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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // 项目标题
            $table->text('description'); // 项目描述
            $table->string('keywords'); // 关键词，用于搜索
            $table->string('image_url'); // 项目图片 URL
            $table->string('case_url')->nullable(); // 查看案例的 URL
            $table->string('source_code_url')->nullable(); // 源代码 URL
            $table->integer('order')->default(0); // 排序顺序
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
