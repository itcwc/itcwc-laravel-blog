<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * 可批量赋值的属性
     *
     * @var array
     */
    protected $fillable = [
        'site_name',
        'site_slogan',
        'site_url',
        'site_icon',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'footer_text',
        'copyright',
        'social_facebook',
        'social_twitter',
        'social_github',
        'social_linkedin',
        'social_instagram',
        'share_title',
        'share_description',
        'share_image',
        'maintenance_mode',
    ];

    /**
     * 属性类型转换
     *
     * @var array
     */
    protected $casts = [
        'maintenance_mode' => 'boolean',
    ];

    /**
     * 获取系统配置
     *
     * @return \App\Models\Setting|null
     */
    public static function getSettings()
    {
        return self::first();
    }

    /**
     * 更新或创建系统配置
     *
     * @param array $data
     * @return \App\Models\Setting
     */
    public static function updateOrCreateSettings(array $data)
    {
        return self::updateOrCreate([], $data);
    }

    /**
     * 检查是否处于维护模式
     *
     * @return bool
     */
    public static function isMaintenanceMode()
    {
        $settings = self::getSettings();
        return $settings ? $settings->maintenance_mode : false;
    }
}
