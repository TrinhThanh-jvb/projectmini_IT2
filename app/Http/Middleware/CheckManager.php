<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckManager
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'manager') {
            abort(403, 'Bạn không có quyền truy cập chức năng này (chỉ dành cho Quản lý).');
        }

        return $next($request);
    }
}
