<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdminMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        //If not logged in redirect to login
        if(!Auth::check()){
            return redirect()->route('login');
        }

        //If Admin return next request
        if(Auth::user()->is_admin){
            return $next($request);
        }

        // Otherwise abort with Forbidden Error
        return abort(Response::HTTP_FORBIDDEN);
    }
}
