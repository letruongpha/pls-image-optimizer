<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Standalone GD-based WebP/AVIF encoding helpers.
 * Deliberately free of plugin constants so it can be reused/extracted.
 */
if ( ! class_exists( 'PLS_Image_Converter' ) ) {
class PLS_Image_Converter {

    /**
     * @param string $format 'webp' | 'avif'
     * @return bool whether GD can encode this format
     */
    public static function supports( $format ) {
        if ( 'webp' === $format ) {
            return function_exists( 'imagewebp' );
        }
        if ( 'avif' === $format ) {
            return function_exists( 'imageavif' );
        }
        return false;
    }

    /**
     * Create a GD image resource from a JPEG/PNG/WebP file.
     * Returns a truecolor, alpha-preserving resource for PNG.
     *
     * @param string $path
     * @return \GdImage|resource|false
     */
    public static function create_gd_from_file( $path ) {
        if ( ! file_exists( $path ) ) {
            return false;
        }
        $ext = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );

        if ( in_array( $ext, array( 'jpg', 'jpeg' ), true ) ) {
            $img = @imagecreatefromjpeg( $path );
        } elseif ( 'png' === $ext ) {
            $img = @imagecreatefrompng( $path );
            if ( $img ) {
                imagepalettetotruecolor( $img );
                imagealphablending( $img, true );
                imagesavealpha( $img, true );
            }
        } elseif ( 'webp' === $ext && function_exists( 'imagecreatefromwebp' ) ) {
            $img = @imagecreatefromwebp( $path );
        } else {
            return false;
        }

        return $img ? $img : false;
    }

    /**
     * Encode an existing image file to WebP at $dest.
     *
     * @return bool
     */
    public static function to_webp( $src_path, $dest_path, $quality = 80 ) {
        if ( ! self::supports( 'webp' ) ) {
            return false;
        }
        $img = self::create_gd_from_file( $src_path );
        if ( ! $img ) {
            return false;
        }
        $ok = @imagewebp( $img, $dest_path, $quality );
        imagedestroy( $img );
        return (bool) $ok;
    }

    /**
     * Encode an existing image file to AVIF at $dest.
     *
     * @return bool
     */
    public static function to_avif( $src_path, $dest_path, $quality = 80 ) {
        if ( ! self::supports( 'avif' ) ) {
            return false;
        }
        $img = self::create_gd_from_file( $src_path );
        if ( ! $img ) {
            return false;
        }
        $ok = @imageavif( $img, $dest_path, $quality );
        imagedestroy( $img );
        return (bool) $ok;
    }
}
}
