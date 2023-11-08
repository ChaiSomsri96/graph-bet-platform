<?php

namespace App\Http\Middleware;

use Closure;

class GlobalAuth
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
        if(($request->session()->has('is_login') && $request->session()->get('is_login')) or $request->is('login')) {
            if(($request->session()->has('is_login') && $request->session()->get('is_login')) and $request->is('login'))
                return redirect('/');
            else
                return $next($request);
        }
        else
            return redirect('/login');
    }
}
