<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['key', 'is_blocked', 'reason', 'updated_by'])]
class AccessControl extends Model
{
    use HasFactory;

    public const LABELS = ['login' => 'Login pelanggan', 'registration' => 'Pendaftaran pelanggan', 'orders' => 'Cipta & ubah tempahan', 'payments' => 'Halaman & proses bayaran'];

    protected function casts(): array
    {
        return ['is_blocked' => 'boolean'];
    }

    public static function blocked(string $key): ?self
    {
        return static::where('key', $key)->where('is_blocked', true)->first();
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
