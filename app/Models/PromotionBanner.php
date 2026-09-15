<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'image_path', 'is_active', 'expires_at', 'created_by', 'updated_by'])]
class PromotionBanner extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'expires_at' => 'datetime'];
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('expires_at', '>', now());
    }

    public function statusLabel(): string
    {
        return ! $this->is_active ? 'Tidak aktif' : ($this->expires_at->isPast() ? 'Tamat tempoh' : 'Aktif');
    }
}
