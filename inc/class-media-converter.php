<?php
/**
 * Image Converter Class
 * Handles all image conversion logic
 */

if (!defined('ABSPATH')) exit;

if ( ! class_exists( 'PLS_Media_Converter' ) ) {
class PLS_Media_Converter {

    /**
     * Check requirements
     */
    public function can_convert() {
        return function_exists('imagewebp') || extension_loaded('imagick');
    }

    /**
     * Get AVIF support status with reason
     */
    public function get_avif_support_detail() {
        if (extension_loaded('imagick')) {
            try {
                $imagick = new Imagick();
                $formats = $imagick->queryFormats('AVIF');
                $version = Imagick::getVersion();
                preg_match('/ImageMagick ([^\s]+)/', $version['versionString'], $matches);
                $ver_num = isset($matches[1]) ? $matches[1] : 'Unknown';

                if (!empty($formats)) {
                    return ['supported' => true, 'reason' => 'Imagick ' . $ver_num];
                }
                return ['supported' => false, 'reason' => 'Imagick ' . $ver_num . ' no AVIF support'];
            } catch (Exception $e) {
                // Fallback to GD if Imagick fails
            }
        }
        if (function_exists('imageavif')) {
            return ['supported' => true, 'reason' => 'GD Library'];
        }
        return ['supported' => false, 'reason' => 'PHP 8.1+ required'];
    }

    /**
     * Get WebP support status with reason
     */
    public function get_webp_support_detail() {
        if (extension_loaded('imagick')) {
            $imagick = new Imagick();
            $formats = $imagick->queryFormats('WEBP');
            if (!empty($formats)) {
                return ['supported' => true, 'reason' => 'Imagick'];
            }
            return ['supported' => false, 'reason' => 'Imagick no WebP support'];
        }
        if (function_exists('imagewebp')) {
            return ['supported' => true, 'reason' => 'GD Library'];
        }
        return ['supported' => false, 'reason' => 'GD/Imagick missing'];
    }

    /**
     * Convert a single file to WebP or AVIF
     *
     * @param string $path          Path to the image file
     * @param int    $quality       Image quality (1-100)
     * @param string $target_format Target format ('webp' or 'avif')
     * @return string|false         New file path on success, false on failure
     */
    public function convert_file($path, $quality = 75, $target_format = 'webp') {
        if ( ! $this->can_convert() ) {
            return false;
        }

        if (!file_exists($path)) {
            return false;
        }

        // Handle Fallback: If AVIF requested but not supported, try WebP
        $avif_check = $this->get_avif_support_detail();
        if ($target_format === 'avif' && !$avif_check['supported']) {
            $target_format = 'webp';
        }

        // Final support check for the decided format
        $webp_check = $this->get_webp_support_detail();
        if ($target_format === 'webp' && !$webp_check['supported']) {
            return false;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $dir = pathinfo($path, PATHINFO_DIRNAME);
        $name = pathinfo($path, PATHINFO_FILENAME);

        $is_same_format = ($ext === $target_format);
        $target = $dir . '/' . $name . ($is_same_format ? '.tmp.' . $target_format : '.' . $target_format);

        $saved = false;

        // Try Imagick first if available
        if (extension_loaded('imagick')) {
            $saved = $this->convert_with_imagick($path, $target, $quality, $target_format);
        }

        // Fallback to GD if Imagick failed or not available
        if (!$saved) {
            if ($target_format === 'avif') {
                $saved = PLS_Image_Converter::to_avif($path, $target, $quality);
            } elseif ($target_format === 'webp') {
                $saved = PLS_Image_Converter::to_webp($path, $target, $quality);
            }
        }

        if (!$saved || !file_exists($target)) {
            return false;
        }

        // Check if new file is smaller
        $old_size = filesize($path);
        $new_size = filesize($target);

        // Sanity check: If new file is 0 bytes, delete it
        if ($new_size === 0) {
            @unlink($target);
            return false;
        }

        if ($new_size >= $old_size) {
            @unlink($target);
            return false;
        }

        // Replace or rename file
        if ($is_same_format) {
            rename($target, $path);
            return $path;
        } else {
            @unlink($path);
            return $target;
        }
    }

    /**
     * Convert using Imagick
     */
    private function convert_with_imagick($input, $output, $quality, $format) {
        try {
            $imagick = new Imagick($input);

            // Handle Transparency
            if ($format === 'webp') {
                $imagick->setImageFormat('webp');
                $imagick->setOption('webp:lossless', 'false');
            } elseif ($format === 'avif') {
                $imagick->setImageFormat('avif');
            }

            $imagick->setImageCompressionQuality($quality);

            // Strip metadata to save space
            $imagick->stripImage();

            $result = $imagick->writeImage($output);
            $imagick->destroy();
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Calculate compression percentage
     *
     * @param int $old_size Original file size
     * @param int $new_size New file size
     * @return int Percentage saved
     */
    public function calculate_savings($old_size, $new_size) {
        if ($old_size <= 0) {
            return 0;
        }
        return round((($old_size - $new_size) / $old_size) * 100);
    }
}
}
