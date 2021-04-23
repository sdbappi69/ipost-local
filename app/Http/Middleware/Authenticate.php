<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }
            /**
            * If the request comes from auth:api
            * @return bad request intead of login page
            */
            elseif( $guard == 'api' )
            {
               $status        =  'Bad Request';
               $status_code   =  401;
               $message[]      =  'API Token invalid';

               $feedback['status']        =  $status;
               $feedback['status_code']   =  $status_code;
               $feedback['message']       =  $message;
               // $feedback['response']      =  [];

               return response($feedback, 200);
            }
            else {
                return redirect()->guest('login');
            }
        }

        return $next($request);
    }
}
