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
    protected function compressImage(string $sourcePath, string $destinationPath, int $maxSizeBytes = 409600): bool
    {
        $info = getimagesize($sourcePath);
        if (!$info) return false;

        $mime = $info['mime'];

        // Create image from source
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                // Convert PNG to JPEG to ensure better compression
                $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                imagedestroy($image);
                $image = $bg;
                break;
            default:
                return false;
        }

        // Auto-rotate based on EXIF
        if (function_exists('exif_read_data')) {
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

        // Initial quality
        $quality = 90;
        $saved = false;

        // Iteratively reduce quality until under max size
        do {
            ob_start();
            imagejpeg($image, null, $quality);
            $size = ob_get_length();
            ob_end_clean();

            if ($size <= $maxSizeBytes || $quality <= 10) {
                imagejpeg($image, $destinationPath, $quality);
                $saved = true;
                break;
            }
            $quality -= 10;
        } while ($quality > 0);

        imagedestroy($image);
        return $saved;
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
