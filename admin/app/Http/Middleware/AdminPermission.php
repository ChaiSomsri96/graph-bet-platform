<?php

namespace App\Http\Middleware;

use Closure;

class AdminPermission
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
        if( $request->session()->has('type') && $request->session()->get('type') == 'admin' )
        {
            return $next($request);
        }
        return redirect('/user_affiliates');
    }
}
