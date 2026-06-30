<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrustLicense
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
