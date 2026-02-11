<?php

namespace Hercilio\SimpleUpload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class SimpleUpload
{
    /**
     * Upload a file with a generated hash name.
     *
     * @param UploadedFile|null $file The file from the request.
     * @param string $folder Destination folder.
     * @param string|null $disk Disk name (s3, local). If null, uses default from config.
     * @param string|null $customName Custom filename (without extension).
     * @return string|false The file path or false on failure.
     */
    public function upload(?UploadedFile $file, string $folder = 'uploads', ?string $disk = null, ?string $customName = null): string|false
    {
        if (!$file instanceof UploadedFile) {
            return false;
        }

        $disk = $disk ?? config('filesystems.default');

        try {
            $filename = $customName
                ? $customName . '.' . $file->getClientOriginalExtension()
                : $file->hashName(); // Generates a unique hash

            return $file->storeAs($folder, $filename, ['disk' => $disk]);

        } catch (Exception $e) {
            Log::error("[SimpleUpload] Upload error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload a file keeping the original name (Sanitized).
     * Example: "My Resume.pdf" becomes "my-resume.pdf".
     */
    public function uploadAsOriginal(?UploadedFile $file, string $folder = 'uploads', ?string $disk = null): string|false
    {
        if (!$file instanceof UploadedFile) {
            return false;
        }

        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Slugify the name to avoid special characters and spaces (S3 friendly)
        $safeName = Str::slug($name);

        return $this->upload($file, $folder, $disk, $safeName);
    }

    /**
     * Handle file updates: Deletes the old file and uploads the new one.
     * Returns the new path or the current path if no new file is provided.
     */
    public function update(?UploadedFile $newFile, ?string $currentPath, string $folder = 'uploads', ?string $disk = null): string|null
    {
        if (!$newFile) {
            return $currentPath;
        }

        $disk = $disk ?? config('filesystems.default');

        // Remove the old file
        $this->delete($currentPath, $disk);

        // Upload the new file
        return $this->upload($newFile, $folder, $disk);
    }

    /**
     * Delete a file from storage.
     */
    public function delete(?string $path, ?string $disk = null): bool
    {
        if (!$path) return false;

        $disk = $disk ?? config('filesystems.default');

        try {
            return Storage::disk($disk)->delete($path);
        } catch (Exception $e) {
            Log::error("[SimpleUpload] Delete error: " . $e->getMessage());
            return false;
        }
    }
}