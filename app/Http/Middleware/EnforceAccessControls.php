<?php

namespace App\Http\Middleware;

use App\Http\AuditContext;
use App\Models\AccessControl;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceAccessControls
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && ! $user->is_active) {
            $request->attributes->set('audit.user_id', $user->id);
            AuditContext::mark($request, 'access.account_blocked');
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->view('access-blocked', ['reason' => $user->blocked_reason ?: 'Akaun anda telah dinyahaktifkan. Sila hubungi pihak Kotakia.', 'title' => 'Akaun disekat'], 403);
        }
        if ($user?->isManager() || $request->routeIs('logout', 'login', 'login.store')) {
            return $next($request);
        }
        $key = match (true) {
            $user !== null && AccessControl::blocked('login') !== null => 'login',
            $request->routeIs('register', 'register.store') => 'registration',
            $request->routeIs('invitations.*') || $request->is('order') => 'orders',
            $request->routeIs('payments.*') => 'payments',
            default => null,
        };
        if ($key && ($control = AccessControl::blocked($key))) {
            AuditContext::mark($request, 'access.maintenance_blocked', $control);

            return response()->view('access-blocked', ['reason' => $control->reason, 'title' => 'Akses dihentikan sementara'], 503);
        }

        return $next($request);
    }
}
