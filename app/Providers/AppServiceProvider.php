<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function (object $notifiable, string $token): MailMessage {
            $url = rtrim(config('app.url'), '/').route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()], false);

            return (new MailMessage)
                ->mailer('receipts')
                ->from('kotakiahq@gmail.com', 'Kotakia')
                ->subject('Tetapkan semula kata laluan | Kotakia')
                ->view('emails.password-reset', ['url' => $url, 'minutes' => config('auth.passwords.users.expire')]);
        });
    }
}
