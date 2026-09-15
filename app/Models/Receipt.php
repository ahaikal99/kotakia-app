<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['payment_id', 'user_id', 'number', 'details', 'email_status', 'emailed_at'])]
class Receipt extends Model
{
    protected function casts(): array
    {
        return ['details' => 'array', 'emailed_at' => 'datetime'];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function emailStatusLabel(): string
    {
        return match ($this->email_status) {
            'sent' => 'Email telah dihantar',
            'failed' => 'Email gagal dihantar',
            'not_sent' => 'Email belum dihantar',
            default => 'Email menunggu penghantaran',
        };
    }
}
