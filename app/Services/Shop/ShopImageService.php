<?php

namespace App\Services\Shop;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ShopImageService
{
    /** @param list<UploadedFile> $images @return list<string> */
    public function uploadMany(array $images, string $directory = 'shop/products'): array
    {
        $paths = [];
        foreach ($images as $image) {
            $path = $image->store($directory, 'public');
            if ($path !== false) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    public function upload(UploadedFile $image, string $directory): string
    {
        $path = $image->store($directory, 'public');
        if ($path === false) {
            throw new RuntimeException('The image could not be stored.');
        }

        return $path;
    }

    public function url(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    /** @param list<string> $paths */
    public function deleteMany(array $paths): void
    {
        $safePaths = array_values(array_filter($paths, fn (string $path): bool => str_starts_with($path, 'shop/')));
        if ($safePaths !== []) {
            Storage::disk('public')->delete($safePaths);
        }
    }

    public function delete(?string $path): void
    {
        if ($path && str_starts_with($path, 'shop/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
