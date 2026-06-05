<?php
/**
 * Image Resizer Class
 * Handles image resizing by max width
 */

if (!defined('ABSPATH')) exit;

if ( ! class_exists( 'PLS_Media_Resizer' ) ) {
class PLS_Media_Resizer {

    /**
     * Available max width presets
     */
    public function get_presets() {
        return [
            0    => __( 'No Resize', 'pls-image-optimizer' ),
            600  => __( 'Max 600px', 'pls-image-optimizer' ),
            900  => __( 'Max 900px', 'pls-image-optimizer' ),
            1000 => __( 'Max 1000px', 'pls-image-optimizer' ),
            1200 => __( 'Max 1200px', 'pls-image-optimizer' ),
            1600 => __( 'Max 1600px', 'pls-image-optimizer' ),
            1920 => __( 'Max 1920px (Full HD)', 'pls-image-optimizer' ),
        ];
    }

    /**
     * Resize image to max width
     *
     * @param string $path      Path to image file
     * @param int    $max_width Maximum width in pixels
     * @return array|false      Array with new dimensions or false on failure
     */
    public function resize($path, $max_width) {
        if (!file_exists($path) || $max_width <= 0) {
            return false;
        }

        $image_info = @getimagesize($path);
        if (!$image_info) {
            return false;
        }

        $orig_width = $image_info[0];
        $orig_height = $image_info[1];

        // Skip if image is already smaller
        if ($orig_width <= $max_width) {
            return [
                'resized' => false,
                'width' => $orig_width,
                'height' => $orig_height,
                'message' => sprintf( __( 'Skip (smaller than %spx)', 'pls-image-optimizer' ), $max_width )
            ];
        }

        // Calculate new dimensions
        $ratio = $max_width / $orig_width;
        $new_width = $max_width;
        $new_height = (int) round($orig_height * $ratio);

        // Create image resource
        $source = $this->create_image_from_file($path, $image_info['mime']);
        if (!$source) {
            return false;
        }

        // Create resized image
        $resized = imagecreatetruecolor($new_width, $new_height);

        // Preserve transparency for PNG
        if ($image_info['mime'] === 'image/png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefilledrectangle($resized, 0, 0, $new_width, $new_height, $transparent);
        }

        // Resize
        imagecopyresampled(
            $resized, $source,
            0, 0, 0, 0,
            $new_width, $new_height,
            $orig_width, $orig_height
        );

        // Save back to file
        $saved = $this->save_image($resized, $path, $image_info['mime']);

        imagedestroy($source);
        imagedestroy($resized);

        if (!$saved) {
            return false;
        }

        return [
            'resized' => true,
            'width' => $new_width,
            'height' => $new_height,
            'old_width' => $orig_width,
            'old_height' => $orig_height,
            'message' => $orig_width . ' -> ' . $new_width . 'px'
        ];
    }

    /**
     * Create GD image from file
     *
     * @param string $path Path to image
     * @param string $mime MIME type
     * @return resource|false
     */
    private function create_image_from_file($path, $mime) {
        switch ($mime) {
            case 'image/jpeg':
                return @imagecreatefromjpeg($path);
            case 'image/png':
                return @imagecreatefrompng($path);
            case 'image/webp':
                return @imagecreatefromwebp($path);
            default:
                return false;
        }
    }

    /**
     * Save GD image to file
     *
     * @param resource $image GD image resource
     * @param string   $path  Output path
     * @param string   $mime  MIME type
     * @return bool
     */
    private function save_image($image, $path, $mime) {
        switch ($mime) {
            case 'image/jpeg':
                return imagejpeg($image, $path, 90);
            case 'image/png':
                return imagepng($image, $path, 6);
            case 'image/webp':
                return imagewebp($image, $path, 90);
            default:
                return false;
        }
    }

    /**
     * Get available width presets
     *
     * @return array
     */
    /* Removed get_presets since it is now defined above to support translations */
}
}
