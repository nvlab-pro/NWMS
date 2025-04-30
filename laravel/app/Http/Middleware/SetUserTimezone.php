<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class SetUserTimezone
{
    public function handle($request, Closure $next): mixed
    {
        $currentUser = Auth::user();

        if ($currentUser && $currentUser->relationLoaded('getDomain') === false) {
            $currentUser->load('getDomain');
        }

        if (isset($currentUser->getDomain->dm_timezone)) {
            $timezone = $currentUser->getDomain->dm_timezone ?? config('app.timezone');
            date_default_timezone_set($timezone);
            Config::set('app.timezone', $timezone);
        }

        return $next($request);
    }
}
