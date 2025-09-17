<?php

namespace Base\Admin\Middleware;

use Illuminate\Http\Request;

class Session
{
    public function handle(Request $request, \Closure $next)
    {
        $path = '/'.trim(config('backend.route.prefix'), '/');

        config(['session.path' => $path]);

        if ($domain = config('backend.route.domain')) {
            config(['session.domain' => $domain]);
        }

        return $next($request);
    }
}
