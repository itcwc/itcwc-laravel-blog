<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // app/Http/Middleware/CheckInstalled.php

    public function handle($request, Closure $next)
    {
        $isInstallPath = $request->is('install*');
        $isInstalled = file_exists(storage_path('installed.lock'));

        if (!$isInstalled && !$isInstallPath) {
            return redirect('/install');
        }

        if ($isInstalled && $isInstallPath) {
            return redirect('/'); // 已安装则禁止进入安装页
        }

        return $next($request);
    }
}
