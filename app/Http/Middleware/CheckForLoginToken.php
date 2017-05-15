<?php

namespace App\Http\Middleware;

use App\Models\LoginToken;
use Auth;
use Carbon\Carbon;
use Closure;

class CheckForLoginToken
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
        if (!Auth::check()) {
            $maybeToken = $request->input('login_token');
            $maybeEmail = $request->input('login_email');

            if ($maybeToken && $maybeEmail) {
                $token = LoginToken
                    ::where('token', $maybeToken)
                    ->where('expires_at', '>=', Carbon::now())
                    ->first()
                    ;

                if ($token && $maybeEmail === $token->user->email) {
                    Auth::login($token->user);
                    $token->delete();
                }
            }
        }

        return $next($request);
    }
}
