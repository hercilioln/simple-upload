# SimpleUpload for Laravel

[![Portuguese Version](https://img.shields.io/badge/Portuguese-Version-green)](README_PT.md)

A lightweight and robust file upload manager for Laravel.

It abstracts the repetitive logic of **checking, deleting old files, and uploading new ones**, keeping your controllers clean and DRY.

It works seamlessly with **Local** and **Amazon S3** (or any driver configured in your filesystem).

## 🚀 Installation

You can install the package via composer:

```bash
composer require hercilio/simple-upload
```

## ⚙️ Configuration

The package automatically uses the default disk defined in your `.env` file.

```env
# For local development
FILESYSTEM_DISK=local

# For production (AWS S3, MinIO, DigitalOcean Spaces, etc)
FILESYSTEM_DISK=s3
```

## 💡 Usage

First, import the Facade in your Controller:

```php
use Hercilio\SimpleUpload\Facades\SimpleUpload;
```

### 1. Simple Upload (Unique Hash)

Ideal for avatars and images where the original filename doesn't matter.

```php
// Saves to storage/app/avatars/unique-hash.jpg
$path = SimpleUpload::upload($request->file('avatar'), 'avatars');
```

### 2. Upload with Custom Name

You can specify a custom filename (without extension):

```php
// Saved as: avatars/profile-photo.jpg
$path = SimpleUpload::upload(
    $request->file('avatar'), 
    'avatars', 
    'profile-photo'
);
```

### 3. Upload with Original Name (Sanitized) ✨

Ideal for documents (PDFs, spreadsheets) where you want to preserve the filename. The package automatically removes accents and spaces.

```php
// Input file: "Financial Report 2026.pdf"
// Saved as: "docs/financial-report-2026.pdf"
$path = SimpleUpload::uploadAsOriginal($request->file('doc'), 'docs');
```

### 4. Force a Specific Disk (Optional)

If you need to save to a specific disk other than the default one, pass it as the **last parameter**:

```php
// With custom name and specific disk
SimpleUpload::upload($file, 'backups', 'monthly-backup', 's3');

// Without custom name, only specific disk
SimpleUpload::upload($file, 'backups', null, 's3');

// Upload as original with specific disk
SimpleUpload::uploadAsOriginal($file, 'docs', 's3');
```

### 5. The "Killer Feature": Painless Updates 🔥

Replacing a file usually requires boilerplate code: check if the new file exists, check if the old file exists, delete the old one, upload the new one. `SimpleUpload` handles this in a single line.

```php
public function update(Request $request, User $user)
{
    // If the user uploaded a new photo:
    // 1. Deletes the old photo ($user->photo_path)
    // 2. Uploads the new one
    // 3. Returns the new path
    // If no file was uploaded, it simply returns the old path.
    
    $path = SimpleUpload::update(
        $request->file('photo'), 
        $user->photo_path, 
        'users'
    );
    
    $user->update(['photo_path' => $path]);
}
```

You can also specify a disk for updates:

```php
$path = SimpleUpload::update(
    $request->file('photo'), 
    $user->photo_path, 
    'users',
    's3'
);
```

## 📋 Method Signatures

```php
// Main upload method
upload(?UploadedFile $file, string $folder = 'uploads', ?string $customName = null, ?string $disk = null)

// Upload with original name
uploadAsOriginal(?UploadedFile $file, string $folder = 'uploads', ?string $disk = null)

// Update existing file
update(?UploadedFile $newFile, ?string $currentPath, string $folder = 'uploads', ?string $disk = null)

// Delete file
delete(?string $path, ?string $disk = null)
```

## 📝 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
