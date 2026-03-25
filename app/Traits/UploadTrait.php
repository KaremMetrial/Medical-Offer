<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

trait UploadTrait
{
    /**
     * Get image validation rules.
     */
    public function imageRules($required = false, $max = 50480)
    {
        return [
            $required ? 'required' : 'nullable',
            'image',
            'mimes:jpeg,png,jpg,gif,svg,webp',
            "max:$max"
        ];
    }

    /**
     * Get file validation rules.
     */
    public function fileRules($required = false, $max = 10240)
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            "max:$max"
        ];
    }

    /**
     * Get video validation rules.
     */
    public function videoRules($required = false, $max = 51200)
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-flv,video/webm',
            "max:$max"
        ];
    }

    /**
     * Upload and process an image.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param int|null $width
     * @param int|null $height
     * @return string
     */
    public static function storeImage($file, $directory = 'images', $storage = 'public', $width = 512, $height = 512)
    {
        try {
            ini_set('memory_limit', '512M');
            Log::info('Uploading image', ['directory' => $directory, 'storage' => $storage]);
            $filename = Str::random(20) . '.' . ($file->getClientOriginalExtension() ?: 'jpg');
            $path = $directory . '/' . $filename;

            // Read image
            $image = Image::read($file->getRealPath());

            // Resize if dimensions are provided
            if ($width || $height) {
                $image->scale($width, $height);
            }

            // Store using stream for better memory management
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $encoded = $image->encodeByExtension($extension);

            Storage::disk($storage)->put($path, $encoded);

            Log::info('Image uploaded successfully', ['path' => $path]);
            return $path;
        } catch (\Exception $e) {
            Log::error('Image upload failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Upload a general file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string
     */
    public static function storeFile($file, $directory = 'files', $storage = 'public')
    {
        try {
            Log::info('Uploading file', ['directory' => $directory]);
            $filename = Str::random(20) . '.' . ($file->getClientOriginalExtension() ?: 'file');
            $path = $file->storeAs($directory, $filename, $storage);
            Log::info('File uploaded successfully', ['path' => $path]);
            return $path;
        } catch (\Exception $e) {
            Log::error('File upload failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Upload a video file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string
     */
    public static function storeVideo($file, $directory = 'videos', $storage = 'public')
    {
        try {
            Log::info('Uploading video', ['directory' => $directory]);
            $filename = Str::random(20) . '.' . ($file->getClientOriginalExtension() ?: 'mp4');
            $path = $file->storeAs($directory, $filename, $storage);
            Log::info('Video uploaded successfully', ['path' => $path]);
            return $path;
        } catch (\Exception $e) {
            Log::error('Video upload failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
    /**
     * Delete a file from storage.
     *
     * @param string|null $path
     * @return void
     */
    public function deleteFile($path, $storage = 'public')
    {
        if ($path && Storage::disk($storage)->exists($path)) {
            Storage::disk($storage)->delete($path);
        }
    }
}
