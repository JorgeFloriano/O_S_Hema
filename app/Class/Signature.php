<?php

namespace App\Class;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Signature
{
    public function compress($base64Image, $prefix, $noteId = null)
    {
        try {
            // Remove base64 header
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
            $imageData = base64_decode($image);

            if (!$imageData) {
                logger_main('error', 'Invalid base64 data');
                throw new \Exception('Invalid base64 data');
            }

            // Create image from string
            $sourceImage = imagecreatefromstring($imageData);

            if (!$sourceImage) {
                logger_main('error', 'Could not create image from string');
                throw new \Exception('Could not create image from string');
            }

            // Get original dimensions
            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);

            // Ultra-compact dimensions for mobile signatures
            $targetWidth = 250;   // Reduced from 1080 (77% smaller)
            $targetHeight = 100;  // Reduced from 550 (82% smaller)

            // Create new optimized image
            $optimizedImage = imagecreatetruecolor($targetWidth, $targetHeight);

            // Fill with white background
            $white = imagecolorallocate($optimizedImage, 255, 255, 255);
            imagefill($optimizedImage, 0, 0, $white);

            // Resize with smoothing
            imagecopyresampled(
                $optimizedImage,
                $sourceImage,
                0,
                0,
                0,
                0,
                $targetWidth,
                $targetHeight,
                $originalWidth,
                $originalHeight
            );

            // Optional: Convert to grayscale for even smaller size
            // imagefilter($optimizedImage, IMG_FILTER_GRAYSCALE);

            // Capture output with maximum compression
            ob_start();
            imagepng($optimizedImage, null, 9); // 9 = maximum compression (0-9 scale)
            $optimizedData = ob_get_clean();

            // Clean up memory
            $sourceImage = null;
            $optimizedImage = null;

            if (!$optimizedData) {
                logger_main('error', 'Could not generate optimized image');
                throw new \Exception('Could not generate optimized image');
            }

            // Generate filename and path
            $filename = $prefix . '_' . uniqid() . '.png';
            $directory = 'signatures/' . date('Y/m');
            $fullPath = $directory . '/' . $filename;

            // Ensure directory exists
            Storage::disk('public')->makeDirectory($directory);

            // Store optimized file
            Storage::disk('public')->put($fullPath, $optimizedData);

            // Log optimization results
            $originalSize = strlen($imageData);
            $optimizedSize = strlen($optimizedData);
            $reduction = round((1 - $optimizedSize / $originalSize) * 100, 2);

            Log::info("Signature aggressively optimized: {$originalSize} → {$optimizedSize} bytes ({$reduction}% reduction)");

            return $fullPath;
        } catch (\Exception $e) {
            Log::error('Error in ultraOptimizeSignature: ' . $e->getMessage());

            // Fallback: store original without optimization
            Log::warning('Using fallback storage for signature');
            return $this->storeSignatureFallback($base64Image, $prefix);
        }
    }

    // Fallback method in case optimization fails
    private function storeSignatureFallback($base64Image, $prefix)
    {
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $imageData = base64_decode($image);

        $filename = $prefix . '_' . uniqid() . '.png';
        $directory = 'signatures/' . date('Y/m');
        $fullPath = $directory . '/' . $filename;

        Storage::disk('public')->makeDirectory($directory);
        Storage::disk('public')->put($fullPath, $imageData);

        return $fullPath;
    }
}
