<?php

namespace App\Http\Controllers;

use App\Http\AuditContext;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PasswordResetController extends Controller
{
    public function request(): View
    {
        return view('auth.forgot-password');
    }

    public function send(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        try {
            Password::sendResetLink($data);
        } catch (TransportExceptionInterface $exception) {
            Log::warning('Password reset email transport failed.');
        }
        AuditContext::mark($request, 'password.reset_requested');

        return back()->with('status', 'Jika emel tersebut berdaftar, pautan reset akan dihantar. Semak peti masuk atau folder spam.');
    }

    public function edit(Request $request, string $token): Response
    {
        return response()->view('auth.reset-password', ['token' => $token, 'email' => $request->query('email', '')])
            ->header('Referrer-Policy', 'no-referrer')->header('Cache-Control', 'no-store');
    }

    public function update(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);
        $data = $request->validate([
            'token' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], ['password.confirmed' => 'Pengesahan kata laluan tidak sepadan.', 'password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.']);
        $status = Password::reset($data, function (User $user, string $password) use ($request): void {
            DB::transaction(function () use ($user, $password): void {
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
                if (config('session.driver') === 'database') {
                    DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
                }
            });
            $request->attributes->set('audit.user_id', $user->id);
            AuditContext::mark($request, 'password.reset_completed', $user);
            event(new PasswordReset($user));
        });
        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => 'Pautan reset tidak sah atau telah tamat tempoh. Sila mohon pautan baharu.'])->onlyInput('email');
        }

        return to_route('login')->with('status', 'Kata laluan berjaya ditetapkan semula. Sila log masuk dengan kata laluan baharu.');
    }

    private function normalizeEmail(Request $request): void
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => strtolower(trim($request->input('email')))]);
        }
    }
}
