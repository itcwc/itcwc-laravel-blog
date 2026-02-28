<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function update(Request $request)
    {

        $settings = Setting::first() ?: new Setting();

        // 获取所有请求数据
        $data = $request->all();

        // dd($data);



        // 特殊处理 Checkbox：如果 checkbox 没勾选，请求里不会有这个字段，需要手动设为 0
        // $data['maintenance_mode'] = $request->has('maintenance_mode') ? true : false;

        // dd($data);

        // 如果你有文件上传 (site_icon, share_image)，在这里处理
        if ($request->hasFile('site_icon')) {
            $data['site_icon'] = $request->file('site_icon')->store('settings', 'public');
        }

        if ($request->hasFile('share_image')) {
            $data['share_image'] = $request->file('share_image')->store('settings', 'public');
        }

        $settings->fill($data);
        $settings->save();

        return response()->json(['success' => true]);
    }
}
