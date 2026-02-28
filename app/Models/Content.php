<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    /**
     * 可批量赋值的属性
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'title',
        'content',
        'slug',
        'images',
        'keywords',
        'read_time',
        'published_date',
    ];

    /**
     * 属性类型转换
     *
     * @var array
     */
    protected $casts = [
        'published_date' => 'date',
    ];

    /**
     * 获取所有文章
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function articles()
    {
        return self::where('type', 'article')->orderBy('published_date', 'desc');
    }

    /**
     * 获取所有笔记
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function notes()
    {
        return self::where('type', 'note')->orderBy('published_date', 'desc');
    }

    /**
     * 搜索内容
     *
     * @param string $keyword
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function search($keyword)
    {
        return self::where(function ($query) use ($keyword) {
            $query->where('title', 'like', '%' . $keyword . '%')
                ->orWhere('content', 'like', '%' . $keyword . '%')
                ->orWhere('keywords', 'like', '%' . $keyword . '%');
        })->orderBy('published_date', 'desc');
    }

    public function getImagesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
}
