<?php

namespace App\Services;
use Illuminate\Support\Facades\Storage;

class CommonService
{
    /**
     * This service can be used for common functionalities across different services.
     * Currently, it does not have any methods but can be extended in the future.
     */
    public function __construct()
    {
        // Initialization code if needed
    }

    public function uploadFile($file, string $folder): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

        $path = $file->store($folder, 'public');
        $path = Storage::url($path);
        return asset($path);
        
    }

    public function deleteFile(string $path)
    {
        $relativePath = str_replace('/storage/', '', parse_url($path, PHP_URL_PATH));
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
        return true; 
    }

}
