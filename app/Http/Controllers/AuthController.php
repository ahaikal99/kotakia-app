<?php

namespace App\Http\Controllers;

use App\Http\AuditContext;
use App\Http\Requests\RegisterRequest;
use App\Mail\WelcomeMail;
use App\Models\AccessControl;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function register(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request): User {
            $user = User::create($request->validated());
            Mail::mailer('receipts')->to($user->email)->queue(new WelcomeMail($user->name));

            return $user;
        });
        $request->attributes->set('audit.user_id', $user->id);
        app('request')->attributes->set('audit.user_id', $user->id);
        AuditContext::mark($request, 'auth.registered', $user);

        return to_route('login')->with('status', 'Pendaftaran berjaya. Sila log masuk untuk meneruskan.');
    }

    public function login(): View
    {
        return view('auth.login', ['loginBlock' => AccessControl::blocked('login')]);
    }

    public function authenticate(Request $request): RedirectResponse
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => strtolower(trim($request->input('email')))]);
        }
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], ['required' => 'Sila isi :attribute.', 'email.email' => 'Sila masukkan emel yang sah.']);

        $key = 'login:'.hash('sha256', $credentials['email'].'|'.$request->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            AuditContext::mark($request, 'auth.login_throttled');
            throw ValidationException::withMessages(['email' => 'Terlalu banyak percubaan. Cuba semula dalam '.RateLimiter::availableIn($key).' saat.']);
        }

        if (! Auth::attempt($credentials)) {
            AuditContext::mark($request, 'auth.login_failed');
            RateLimiter::hit($key, 60);
            throw ValidationException::withMessages(['email' => 'Emel atau kata laluan tidak tepat.']);
        }

        $user = $request->user();
        $loginBlock = $user->isManager() ? null : AccessControl::blocked('login');
        if (! $user->is_active || $loginBlock) {
            $request->attributes->set('audit.user_id', $user->id);
            AuditContext::mark($request, ! $user->is_active ? 'auth.account_blocked' : 'auth.login_blocked');
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            throw ValidationException::withMessages(['email' => ! $user->is_active ? ($user->blocked_reason ?: 'Akaun anda telah disekat.') : $loginBlock->reason]);
        }
        RateLimiter::clear($key);
        $request->session()->regenerate();
        AuditContext::mark($request, 'auth.logged_in');

        return to_route($user->isManager() ? 'manager.index' : 'dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->attributes->set('audit.user_id', $request->user()->id);
        AuditContext::mark($request, 'auth.logged_out');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login')->with('status', 'Anda telah log keluar.');
    }
}
