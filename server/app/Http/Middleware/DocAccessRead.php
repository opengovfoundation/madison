<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use App\Models\Doc;
use Auth;

class DocAccessRead
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->doc) {
            $doc = Doc::find($request->doc);
        } else {
            $doc = Doc::findDocBySlug($request->slug);
        }

        $user = Auth::user();

        if (!$doc->canUserView($user)) {
            if ($request->ajax()) {
                return response('Unauthorized.', 403);
            } else {
                return redirect()->guest('auth/login');
            }
        }

        return $next($request);
    }
}
