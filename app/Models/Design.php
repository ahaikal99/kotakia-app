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

    public function previewUrl(): string
    {
        return $this->hasTemplate()
            ? route('designs.preview', ['code' => strtolower($this->code)])
            : $this->thumbnailUrl();
    }

    public function templateView(): string
    {
        return 'designs.'.strtolower($this->code);
    }

    public function hasTemplate(): bool
    {
        return preg_match('/\A[A-Za-z][A-Za-z0-9_-]{0,29}\z/', $this->code) === 1
            && view()->exists($this->templateView());
    }

    /** @return list<string> */
    public function assetEntrypoints(): array
    {
        $assets = ['resources/css/app.css', 'resources/js/app.js'];
        if (! $this->hasTemplate()) {
            return $assets;
        }

        foreach (['css', 'js'] as $extension) {
            $path = 'resources/'.$extension.'/designs/'.strtolower($this->code).'.'.$extension;
            if (is_file(base_path($path))) {
                $assets[] = $path;
            }
        }

        return $assets;
    }
}
