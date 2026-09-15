<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class RecordUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if ($request->is('up')) {
            return $response;
        }

        $exception = $response->exception ?? null;
        $context = $request->attributes->get('audit.context', []);
        $failed = $exception !== null || $response->getStatusCode() >= 400;
        $action = match (true) {
            isset($context['action']) => $context['action'],
            $response->getStatusCode() === 429 => 'request.throttled',
            $exception instanceof AuthenticationException => 'access.unauthenticated',
            $exception instanceof ValidationException => 'validation.failed',
            $response->getStatusCode() === 419 => 'session.expired',
            $response->getStatusCode() === 403 => 'access.denied',
            $response->getStatusCode() === 404 => 'resource.not_found',
            $failed => 'request.failed',
            $request->isMethod('GET') || $request->isMethod('HEAD') => 'page.viewed',
            default => 'request.completed',
        };
        $metadata = [];
        if (! $failed) {
            $metadata = array_intersect_key($context, array_flip(['before', 'after']));
        }
        if ($exception instanceof ValidationException) {
            $metadata['invalid_fields'] = array_keys($exception->errors());
        }
        if ($request->routeIs('login.store') && filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
            $metadata['attempted_email'] = mb_substr($request->input('email'), 0, 255);
        }
        if ($request->routeIs('catalog')) {
            foreach (['tema', 'susun'] as $filter) {
                $value = $request->query($filter);
                $allowed = $filter === 'tema' ? ['semua', ...array_keys(config('catalog.themes'))] : ['kod', 'nama-az', 'nama-za'];
                if (is_string($value) && in_array($value, $allowed, true)) {
                    $metadata[$filter] = $value;
                }
            }
        }
        $ip = $request->ip();
        AuditLog::create([
            'user_id' => $request->attributes->get('audit.user_id') ?? $request->user()?->getAuthIdentifier(),
            'action' => $action,
            'outcome' => $failed ? 'failed' : ($response->isRedirection() && ! isset($context['action']) ? 'redirected' : 'success'),
            'ip_address' => filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null,
            'user_agent' => mb_substr($request->userAgent() ?? '', 0, 1000),
            'method' => $request->method(),
            'route_name' => $request->route()?->getName(),
            'route_path' => $request->route() ? '/'.$request->route()->uri() : null,
            'status_code' => $response->getStatusCode(),
            'subject_type' => $context['subject_type'] ?? ($request->route('invitation') ? 'invitation' : null),
            'subject_id' => $context['subject_id'] ?? (ctype_digit((string) $request->route('invitation')) ? (int) $request->route('invitation') : null),
            'metadata' => $metadata ?: null,
        ]);

        return $response;
    }
}
