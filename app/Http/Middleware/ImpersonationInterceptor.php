<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\LoginToken;

class ImpersonationInterceptor
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $loginToken = $request->session()->get('login_token');

        $storedLoginToken = LoginToken::firstWhere('token', $loginToken);
        if ($storedLoginToken) {
            $appUrl = config('config.domain');
            $currentUri = str_replace($appUrl, '', url()->current());
            $blockedApi = config('config.impersonationBlockedApi');

            if (in_array($currentUri, $blockedApi)) {
                return response()->json('Unauthorized api.', 401);
            }
        }

        return $next($request);
    }
}
