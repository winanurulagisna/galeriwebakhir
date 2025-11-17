<?php

namespace App\Helpers;

class ImageUrlHelper
{
    /**
     * Encode image URL to handle special characters like spaces, parentheses, etc.
     * 
     * @param string $url
     * @return string
     */
    public static function encodeImageUrl($url)
    {
        if (empty($url)) {
            return '';
        }
        
        // If URL already starts with http, return as is
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }
        
        // Split path and filename
        $pathInfo = pathinfo($url);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['basename'];
        
        // Only encode the filename part, not the directory path
        $encodedFilename = rawurlencode($filename);
        
        // Reconstruct the URL
        return $directory . '/' . $encodedFilename;
    }
    
    /**
     * Get safe image URL for display
     * Handles both direct file paths and Storage URLs
     * 
     * @param string $file
     * @param string $fallback
     * @return string
     */
    public static function getSafeImageUrl($file, $fallback = '/images/placeholder.svg')
    {
        if (empty($file)) {
            return $fallback;
        }
        
        // If it's already a full URL (http/https), return as is
        if (str_starts_with($file, 'http://') || str_starts_with($file, 'https://')) {
            return $file;
        }
        
        // If it starts with /, it's already a public path
        if (str_starts_with($file, '/')) {
            return self::encodeImageUrl($file);
        }
        
        // Otherwise, assume it's a storage path
        $storageUrl = \Illuminate\Support\Facades\Storage::url($file);
        return self::encodeImageUrl($storageUrl);
    }
}
