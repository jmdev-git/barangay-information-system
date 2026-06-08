<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Wrapper around the Cloudinary PHP SDK v2.14+.
 * Handles upload, delete, and URL generation for resident ID documents.
 *
 * Environment variables required:
 *   CLOUDINARY_CLOUD_NAME
 *   CLOUDINARY_API_KEY
 *   CLOUDINARY_API_SECRET
 */
class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        // SDK v2.14 accepts a CLOUDINARY_URL-style string or an array config
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    /**
     * Upload a file to Cloudinary.
     *
     * @param  UploadedFile  $file
     * @param  string        $folder  Cloudinary folder e.g. "resident-documents"
     * @return array{public_id: string, secure_url: string, original_name: string, mime_type: string, file_size: int}
     */
    public function upload(UploadedFile $file, string $folder = 'resident-documents'): array
    {
        $isPdf        = $file->getMimeType() === 'application/pdf';
        $resourceType = $isPdf ? 'raw' : 'image';

        $uploadApi = new UploadApi($this->cloudinary->configuration);

        $result = $uploadApi->upload(
            $file->getRealPath(),
            [
                'folder'          => $folder,
                'resource_type'   => $resourceType,
                'use_filename'    => false,
                'unique_filename' => true,
            ]
        );

        return [
            'public_id'     => $result['public_id'],
            'secure_url'    => $result['secure_url'],
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
        ];
    }

    /**
     * Delete a file from Cloudinary by its public_id.
     */
    public function delete(string $publicId, string $mimeType = 'image/jpeg'): void
    {
        $resourceType = $mimeType === 'application/pdf' ? 'raw' : 'image';

        try {
            $uploadApi = new UploadApi($this->cloudinary->configuration);
            $uploadApi->destroy($publicId, [
                'resource_type' => $resourceType,
            ]);
        } catch (\Throwable $e) {
            Log::warning("Cloudinary delete failed for [{$publicId}]: " . $e->getMessage());
        }
    }

    /**
     * Generate a secure URL for a stored public_id.
     */
    public function url(string $publicId, string $mimeType = 'image/jpeg'): string
    {
        if ($mimeType === 'application/pdf') {
            $cloudName = config('cloudinary.cloud_name');
            return "https://res.cloudinary.com/{$cloudName}/raw/upload/{$publicId}";
        }

        return (string) $this->cloudinary->image($publicId)->toUrl();
    }
}
