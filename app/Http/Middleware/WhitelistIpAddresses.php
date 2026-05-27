<?php

namespace App\Http\Middleware;

use App\Models\AllowedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

class WhitelistIpAddresses
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = AllowedIp::activeList();

        if ($allowed === [] || ! IpUtils::checkIp((string) $request->ip(), $allowed)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
