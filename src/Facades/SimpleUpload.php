<?php

namespace Hercilio\SimpleUpload\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string|false upload(\Illuminate\Http\UploadedFile|null $file, string $folder = 'uploads', ?string $disk = null, ?string $customName = null)
 * @method static string|false uploadAsOriginal(\Illuminate\Http\UploadedFile|null $file, string $folder = 'uploads', ?string $disk = null)
 * @method static string|null update(\Illuminate\Http\UploadedFile|null $newFile, ?string $currentPath, string $folder = 'uploads', ?string $disk = null)
 * @method static bool delete(?string $path, ?string $disk = null)
 * * @see \Hercilio\SimpleUpload\SimpleUpload
 */
class SimpleUpload extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'simpleupload';
    }
}