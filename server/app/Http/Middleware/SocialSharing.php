<?php

/**
 * A middleware layer to sniff for useragents belonging to social sharing
 * crawlers (Facebook, Twitter, etc) and returns a summary document to them.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Auth;
use App\Models\Doc;

class SocialSharing
{
    public $botAgents = array(
        '/^facebookexternalhit\/[0-9\.]+/',
        '/^Twitterbot\/.*/',
        '/^Pinterest\/[0-9\.]+/',
        '/^Google \(\+https:\/\/developers\.google\.com\/\+\/web\/snippet\/\)$/',
        '/^Google-StructuredDataTestingTool;/',
        '/^LinkedInBot\/[0-9\.]+/',
        '/^Slackbot-LinkExpanding [0-9\.]+/',
        '/^Slackbot [0-9\.]+/'
    );

    /**
     * Create a new filter instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        // Only redirect on GETs.
        if(strtoupper($request->method()) == 'GET')
        {
            // If we have a request from a bot, send them a custom, social-sharing-
            // friendly version of the website.
            foreach ($this->botAgents as $pattern)
            {
                if(preg_match($pattern, $request->header('User-Agent')))
                {
                    // Add the social/ prefix on the requested url.
                    $dupRequest = $request->duplicate();
                    $dupRequest->server->set('REQUEST_URI', 'social/' . $request->path());
                    return $next($dupRequest);
                }
            }
        }

        return $next($request);
    }
}
