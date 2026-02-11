<?php

/**
 * Image Processor Utility
 * ========================
 * Handle image compression, metadata extraction (EXIF), and resizing.
 */

trait ImageProcessor
{
    /**
     * Compress and save image to specific size (max 400KB)
     */
    public function compressImage(string $sourcePath, string $destinationPath, int $maxFileSize = 512000): bool
    {
        $quality = 85; // Initial quality
        $minQuality = 10;
        $maxIterations = 10;
        $iteration = 0;

        $imgInfo = getimagesize($sourcePath);
        if (!$imgInfo) return false;

        $mime = $imgInfo['mime'];
        $width = $imgInfo[0];
        $height = $imgInfo[1];

        // Load image
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                // Convert to true color to support transparency/JPG conversion
                $bg = imagecreatetruecolor($width, $height);
                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                imagecopy($bg, $image, 0, 0, 0, 0, $width, $height);
                imagedestroy($image);
                $image = $bg;
                break;
            default:
                return false;
        }

        // Fix orientation if EXIF is available
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $image = imagerotate($image, 180, 0);
                        break;
                    case 6:
                        $image = imagerotate($image, -90, 0);
                        break;
                    case 8:
                        $image = imagerotate($image, 90, 0);
                        break;
                }
            }
        }

        // Iterative compression to reach target size
        do {
            ob_start();
            imagejpeg($image, null, $quality);
            $imgData = ob_get_clean();
            $currentSize = strlen($imgData);

            if ($currentSize <= $maxFileSize || $quality <= $minQuality) {
                file_put_contents($destinationPath, $imgData);
                break;
            }

            $quality -= 10;
            $iteration++;
        } while ($iteration < $maxIterations);

        imagedestroy($image);
        return true;
    }

    /**
     * Extract metadata (Date and Location) from image EXIF
     */
    protected function extractExifData(string $filePath): array
    {
        $data = [
            'date' => null,
            'latitude' => null,
            'longitude' => null,
            'location_str' => null
        ];

        if (!function_exists('exif_read_data')) return $data;

        $exif = @exif_read_data($filePath);
        if (!$exif) return $data;

        // Date extraction
        if (!empty($exif['DateTimeOriginal'])) {
            $data['date'] = date('Y-m-d', strtotime($exif['DateTimeOriginal']));
        } elseif (!empty($exif['DateTime'])) {
            $data['date'] = date('Y-m-d', strtotime($exif['DateTime']));
        }

        // GPS extraction
        if (isset($exif['GPSLatitude']) && isset($exif['GPSLongitude']) && isset($exif['GPSLatitudeRef']) && isset($exif['GPSLongitudeRef'])) {
            $lat = $this->getGps($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
            $lng = $this->getGps($exif['GPSLongitude'], $exif['GPSLongitudeRef']);

            $data['latitude'] = $lat;
            $data['longitude'] = $lng;
            $data['location_str'] = "{$lat}, {$lng}";
        }

        return $data;
    }

    private function getGps($exifCoord, $hemi): float
    {
        $degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
        $minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
        $seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;

        $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

        return $flip * ($degrees + ($minutes / 60) + ($seconds / 3600));
    }

    private function gps2Num($coordPart): float
    {
        $parts = explode('/', $coordPart);
        if (count($parts) <= 0) return 0;
        if (count($parts) == 1) return (float)$parts[0];
        return (float)$parts[0] / (float)$parts[1];
    }
}
