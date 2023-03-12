<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyXenditCallback
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($callbackToken = config('xendit.callback_token')) {
            if ($request->header('X-CALLBACK-TOKEN') != $callbackToken) {
                abort(401, 'The verification code is invalid.');
            }
        }

        return $next($request);
    }
}
