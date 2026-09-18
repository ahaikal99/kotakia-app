<?php

namespace App\Models;

use Database\Factories\DesignFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    /** @use HasFactory<DesignFactory> */
    use HasFactory;

    protected $fillable = ['code', 'name', 'theme', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function thumbnailUrl(): string
    {
        $path = 'image/KATALOG/'.$this->code.'.png';

        return asset(is_file(public_path($path)) ? $path : 'image/kotakia.png');
    }
}
