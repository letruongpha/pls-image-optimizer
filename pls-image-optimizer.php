<?php
/**
 * Plugin Name: PLS Image Optimizer
 * Plugin URI:  https://phalesolution.com
 * Description: Công cụ nén ảnh hàng loạt (WebP/AVIF) và resize cho thư viện Media WordPress. Hoạt động độc lập.
 * Version:     1.0.0
 * Author:      Pha Le Solution
 * Author URI:  https://phalesolution.com
 * License:     GPLv2 or later
 * Text Domain: pls-image-optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'PLS_IMGOPT_VERSION', '1.0.0' );
define( 'PLS_IMGOPT_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLS_IMGOPT_URL', plugin_dir_url( __FILE__ ) );

require_once PLS_IMGOPT_PATH . 'inc/lib/class-pls-image-converter.php';
require_once PLS_IMGOPT_PATH . 'inc/class-media-converter.php';
require_once PLS_IMGOPT_PATH . 'inc/class-media-resizer.php';
require_once PLS_IMGOPT_PATH . 'inc/class-image-optimizer.php';

function pls_imgopt_init() {
    new PLS_Image_Optimizer();
}
add_action( 'plugins_loaded', 'pls_imgopt_init' );

register_activation_hook( __FILE__, 'pls_imgopt_activate' );
function pls_imgopt_activate() {
    // Preserve the shared counter key for continuity with PLS Optimize Performance.
    if ( false === get_option( 'pls_img_opt_count', false ) ) {
        add_option( 'pls_img_opt_count', 0 );
    }
}
