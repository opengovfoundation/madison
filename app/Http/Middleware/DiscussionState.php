<?php

namespace App\Http\Middleware;

use Closure;

class DiscussionState
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $state)
    {
        $document = null;
        foreach (['document', 'documentTrashed'] as $key) {
            if (!empty($request->route()->parameter($key))) {
                $document = $request->route()->parameter($key);
                break;
            }
        }

        if (!$document) {
            abort(400);
        }

        $checkPass = false;
        if (starts_with($state, '!')) {
            $checkPass = $document->discussion_state !== ltrim($state, '!');
        } else {
            $checkPass = $document->discussion_state === $state;
        }

        if (!$checkPass) {
            abort(403);
        }

        return $next($request);
    }
}
