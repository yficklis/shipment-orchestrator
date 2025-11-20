<?php

namespace App\Http\Middleware;

use App\Models\Shipment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShipmentOwner
{
    /**
     * Handle an incoming request.
     *
     * Ensures that the authenticated user owns the shipment they're trying to access.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the shipment from route parameter
        $shipment = $request->route('shipment');

        // If shipment exists and is a model instance
        if ($shipment instanceof Shipment) {
            // Check if the authenticated user owns this shipment
            if ($shipment->user_id !== $request->user()->id) {
                abort(403, 'Unauthorized access to shipment.');
            }
        }

        return $next($request);
    }
}
