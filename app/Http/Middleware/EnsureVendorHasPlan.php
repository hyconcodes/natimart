<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorHasPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->hasRole('vendor') && $user->shop) {
            // If they don't have a plan and aren't already going to the selection page
            if (! $user->shop->pricing_plan_id && ! $request->routeIs('vendor.plans*')) {
                return redirect()->route('vendor.plans', ['shop_slug' => $user->shop->slug]);
            }
        }

        return $next($request);
    }
}
