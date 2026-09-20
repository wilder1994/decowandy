<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogCategorySetting extends Model
{
    protected $fillable = [
        'slug',
        'cover_path',
    ];

    public function coverUrl(): ?string
    {
        if (! $this->cover_path) {
            return null;
        }

        if (Str::startsWith($this->cover_path, ['http://', 'https://', '/storage'])) {
            return $this->cover_path;
        }

        return Storage::disk('public')->url($this->cover_path);
    }
}
