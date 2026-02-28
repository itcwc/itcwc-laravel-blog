<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * 可批量赋值的属性
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'keywords',
        'image_url',
        'case_url',
        'source_code_url',
        'order',
    ];

    /**
     * 获取所有项目，按排序顺序和创建时间排序
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function allProjects()
    {
        return self::orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * 搜索项目
     *
     * @param string $keyword
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function search($keyword)
    {
        return self::where(function ($query) use ($keyword) {
            $query->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%')
                  ->orWhere('keywords', 'like', '%' . $keyword . '%');
        })->orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }
}
