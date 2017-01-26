<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\View;
use App\Models\Page;
use Closure;

class LoadPages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        View::share('headerPages', Page::where('header_nav_link', true)->get());
        View::share('footerPages', Page::where('footer_nav_link', true)->get());

        return $next($request);
    }
}
