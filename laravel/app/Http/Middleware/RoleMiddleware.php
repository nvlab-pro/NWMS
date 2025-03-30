<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $currentRole)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $access = 0;
        foreach (Auth::user()->role as $role) {
            if ($role->slug == $currentRole) $access = 1;
        }

        if ($access == 0) {
            abort(403, 'Доступ запрещен');
        }

        return $next($request);
    }

    public static function checkUserPermission($currentRole)
    {

        $arRoles = explode(',', $currentRole);

        foreach (Auth::user()->role as $role) {
            if (in_array($role->slug, $arRoles)) return true;;
        }

        return false;
    }

    public static function checkUserCountry($currentCountries)
    {

        $arCountries = explode(',', $currentCountries);

        $arCountry = explode(',', $currentCountries);
        $currentCountry = Auth::user()->getDomain->getCountry->first()->lco_id;

        if (in_array($currentCountry, $arCountries)) return true;;

        return false;
    }
}
