<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

#[Fillable(['title', 'theme', 'host_name', 'celebrant_name', 'event_date', 'start_time', 'end_time', 'venue', 'address', 'map_url', 'contact_name', 'contact_phone', 'message'])]
class Invitation extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation): void {
            $invitation->public_token ??= Str::random(40);
        });
    }

    protected function casts(): array
    {
        return ['event_date' => 'date', 'amount_cents' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'awaiting_payment' => 'Menunggu bayaran',
            'demo_complete' => 'Tempahan direkodkan',
            default => 'Draf',
        };
    }

    public function publicUrl(): string
    {
        return route('invitations.public', $this->public_token);
    }

    public function whatsappMessage(): string
    {
        $celebrants = preg_replace('/\s+(?:&|dan)\s+/iu', "\ndan\n♡ ", $this->celebrant_name);
        $start = Carbon::createFromFormat('H:i', substr($this->start_time, 0, 5))->format('g:i A');
        $end = $this->end_time ? ' - '.Carbon::createFromFormat('H:i', substr($this->end_time, 0, 5))->format('g:i A') : '';
        $message = "╭────────────────╮\n*{$this->title}*\n╰────────────────╯\n\n"
            ."بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْم\n\n"
            ."Assalamualaikum w.b.t & salam sejahtera,\n\n"
            ."Dengan penuh kesyukuran ke hadrat Allah SWT,\n\n"
            ."*{$this->host_name}*\n\n"
            ."dengan segala hormatnya menjemput Dato’ | Datin | Tuan | Puan | Encik | Cik ke majlis bagi meraikan:\n\n"
            ."♡ *{$celebrants}*\n\n"
            ."🗓️ *Tarikh:*\n{$this->event_date->locale('ms')->translatedFormat('l, j F Y')}\n\n"
            ."🕙 *Masa:*\n{$start}{$end}\n\n"
            ."📍 *Tempat:*\n{$this->venue}\n{$this->address}\n\n"
            ."Sila klik pautan berikut untuk butiran majlis:\n🔗 {$this->publicUrl()}";

        if ($this->message) {
            $message .= "\n\n{$this->message}";
        }

        return $message;
    }
}
