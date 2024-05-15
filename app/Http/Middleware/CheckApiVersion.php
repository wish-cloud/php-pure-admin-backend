<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $version = (int) $request->header('X-Api-Version', 0);
        $platform = $request->header('X-Api-Platform', '');
        $roles_secret = $request->header('X-Api-Roles-Secret', '');

        $data = [
            'version' => $version,
            'platform' => $platform,
            'roles_secret' => $roles_secret,
        ];
        $request->attributes->add($data);

        return $next($request);
    }
}
