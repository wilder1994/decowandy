<?php

namespace App\Services;

use App\Models\CatalogCategorySetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CatalogCoverService
{
    private const MAX_KB = 2048;

    private const ALLOWED = ['jpg', 'jpeg', 'png', 'webp'];

    public function upsertCover(string $slug, UploadedFile $file): CatalogCategorySetting
    {
        $this->assertAllowedSlug($slug);
        $this->assertValidImage($file);

        $setting = CatalogCategorySetting::query()->firstOrNew(['slug' => $slug]);
        $previous = $setting->cover_path;

        $path = $file->storeAs(
            'catalog/covers',
            $slug.'-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension(),
            'public'
        );

        $setting->cover_path = $path;
        $setting->save();

        if ($previous && $previous !== $path) {
            Storage::disk('public')->delete($previous);
        }

        return $setting->fresh();
    }

    public function clearCover(string $slug): CatalogCategorySetting
    {
        $this->assertAllowedSlug($slug);

        $setting = CatalogCategorySetting::query()->firstOrNew(['slug' => $slug]);
        if ($setting->cover_path) {
            Storage::disk('public')->delete($setting->cover_path);
            $setting->cover_path = null;
            $setting->save();
        } elseif (! $setting->exists) {
            $setting->save();
        }

        return $setting->fresh();
    }

    private function assertAllowedSlug(string $slug): void
    {
        $allowed = collect(config('decowandy.catalog_categories'))
            ->pluck('slug')
            ->all();

        if (! in_array($slug, $allowed, true)) {
            throw ValidationException::withMessages([
                'slug' => 'Categoría no válida.',
            ]);
        }
    }

    private function assertValidImage(UploadedFile $file): void
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, self::ALLOWED, true)) {
            throw ValidationException::withMessages([
                'cover' => 'La imagen debe ser JPG, PNG o WebP.',
            ]);
        }

        if ($file->getSize() > self::MAX_KB * 1024) {
            throw ValidationException::withMessages([
                'cover' => 'La imagen no puede superar 2 MB.',
            ]);
        }
    }
}
