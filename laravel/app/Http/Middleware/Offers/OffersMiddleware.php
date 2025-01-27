<?php

namespace App\Http\Middleware\Offers;

use Closure;
use Illuminate\Support\Facades\Auth;

class OffersMiddleware
{
    public static function checkRule4SelectShop($offerId, $currentRole)
    {

        $currentRule = true;
        if ($offerId == 0) return false;

        $arRoles = explode(',', $currentRole);

        foreach (Auth::user()->role as $role) {
            if (in_array($role->slug, $arRoles)) $currentRule = false;
        }

        return $currentRule;
    }
}
