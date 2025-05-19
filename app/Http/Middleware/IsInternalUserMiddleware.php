<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsInternalUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        //This will exclude all external user with a 403 Forbidden message
        if(!Auth::user() instanceof App\Models\User) {
            return abort(Response::HTTP_FORBIDDEN);
        // If this is a User check they are authorised
        } else if(!Auth::check()) {
            return redirect()->route('login');
        } else {
            return $next($request);
        }

        // Otherwise abort with Forbidden Error
        return abort(Response::HTTP_FORBIDDEN);

    }
}
