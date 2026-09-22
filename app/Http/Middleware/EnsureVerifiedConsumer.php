<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVerifiedConsumer
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $user = $request->user();

        if (!$user) {
            return redirect()
                ->route('consumer.login');
        }

        if (!$user->hasRole('Consumer')) {
            abort(403);
        }

        $consumer = $user->consumer;

        if (!$consumer) {
            abort(
                403,
                'No consumer record is associated with this account.'
            );
        }

        if (
            $consumer->verification_status !== 'Verified' ||
            !$consumer->is_active ||
            !$user->is_active
        ) {
            return redirect()
                ->route('consumer.registration.status');
        }

        return $next($request);
    }
}
