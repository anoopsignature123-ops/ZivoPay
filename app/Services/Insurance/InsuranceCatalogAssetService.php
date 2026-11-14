<?php

namespace App\Services\Insurance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class InsuranceCatalogAssetService
{
    public function storeImage(UploadedFile $image, string $directory): string
    {
        $path = $image->store('insurance/'.$directory, 'public');

        if ($path === false) {
            throw new RuntimeException('The insurance catalog image could not be stored.');
        }

        return $path;
    }

    public function deleteImage(?string $path): void
    {
        if ($path !== null && str_starts_with($path, 'insurance/')) {
            Storage::disk('public')->delete($path);
        }
    }

    /** @param class-string<Model> $modelClass */
    public function uniqueSlug(string $value, string $modelClass, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value) ?: 'insurance-item';
        $slug = $baseSlug;
        $suffix = 2;

        while ($modelClass::query()->where('slug', $slug)
            ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
