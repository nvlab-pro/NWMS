<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentUser = Auth::user();

        if (isset($currentUser->lang) && $currentUser->lang != '')
            App::setLocale($currentUser->lang);
        else
            App::setLocale('en');

//        if (isset($currentUser->id)) {
//            User::where('id', $currentUser->id)->update([
//                'login_count' => $currentUser->login_count + 1,
//                'last_login_date' => date('Y-m-d H:i:s'),
//            ]);
//        }

        return $next($request);
    }
}
