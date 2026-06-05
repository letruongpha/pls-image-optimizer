<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'PLS_Image_Optimizer' ) ) {

    class PLS_Image_Optimizer {

        private $converter;
        private $resizer;

        public function __construct() {
            $this->converter = new PLS_Media_Converter();
            $this->resizer   = new PLS_Media_Resizer();

            add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
            add_action( 'wp_ajax_pls_imgopt_convert', [ $this, 'handle_convert' ] );
            add_action( 'wp_ajax_pls_imgopt_scan', [ $this, 'handle_scan_ids' ] );
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
        }

        public function add_menu_page() {
            add_menu_page(
                __( 'PLS Image Optimizer', 'pls-image-optimizer' ),
                __( 'Image Optimizer', 'pls-image-optimizer' ),
                'manage_options',
                'pls-image-optimizer',
                [ $this, 'render_page' ],
                'dashicons-format-gallery',
                81
            );
        }

        public function render_page() {
            $resizer = $this->resizer;
            $resize_presets = $resizer->get_presets();
            include PLS_IMGOPT_PATH . 'admin/view-optimizer-page.php';
        }

        public function enqueue_admin_scripts( $hook ) {
            if ( 'toplevel_page_pls-image-optimizer' !== $hook ) {
                return;
            }
            wp_enqueue_style( 'pls-imgopt-css', PLS_IMGOPT_URL . 'assets/css/image-optimizer.css', [], PLS_IMGOPT_VERSION );
            wp_enqueue_script( 'pls-imgopt-script', PLS_IMGOPT_URL . 'assets/js/image-optimizer.js', [ 'jquery' ], PLS_IMGOPT_VERSION, true );
            wp_localize_script( 'pls-imgopt-script', 'pls_imgopt_vars', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'pls_imgopt_nonce' ),
                'strings'  => [
                    'scanning'                => __( 'Scanning...', 'pls-image-optimizer' ),
                    'scan_images'             => __( 'Scan Images', 'pls-image-optimizer' ),
                    'no_images_found'         => __( 'No images found.', 'pls-image-optimizer' ),
                    'unknown_error'           => __( 'Unknown error', 'pls-image-optimizer' ),
                    'scan_failed_with_reason' => __( 'Scan failed: %s', 'pls-image-optimizer' ),
                    'scan_request_failed'     => __( 'Scan request failed.', 'pls-image-optimizer' ),
                    'total_images'            => __( 'Total: %d images', 'pls-image-optimizer' ),
                    'status_original'         => __( 'Original', 'pls-image-optimizer' ),
                    'status_webp'             => __( 'WebP', 'pls-image-optimizer' ),
                    'status_avif'             => __( 'AVIF', 'pls-image-optimizer' ),
                    'edit'                    => __( 'Edit', 'pls-image-optimizer' ),
                    'selected_suffix'         => __( ' selected', 'pls-image-optimizer' ),
                    'optimizing'              => __( 'Optimizing...', 'pls-image-optimizer' ),
                    'start_optimization'      => __( 'Start Optimization', 'pls-image-optimizer' ),
                    'status_done'             => __( 'Done', 'pls-image-optimizer' ),
                    'status_error'            => __( 'Error', 'pls-image-optimizer' ),
                ],
            ]);
        }

        /**
         * Handle scan images IDs
         */
        public function handle_scan_ids() {
            check_ajax_referer( 'pls_imgopt_nonce', 'nonce' );

            $filter = isset($_POST['filter']) ? sanitize_text_field($_POST['filter']) : 'all';
            $filter_attached = isset($_POST['filter_attached']) ? sanitize_text_field($_POST['filter_attached']) : 'all';
            $filter_size = isset($_POST['filter_size']) ? sanitize_text_field($_POST['filter_size']) : 'all';
            $page = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
            $per_page = 100;

            $args = [
                'post_type'      => 'attachment',
                'post_status'    => 'inherit', // Changed back to inherit as 'any' might pull non-attachments if not careful, though 'attachment' type is safe. Standard is inherit.
                'posts_per_page' => $per_page,
                'paged'          => $page,
                'fields'         => 'ids',
                'orderby'        => 'date',
                'order'          => 'DESC'
            ];

            // Filter: Converted status
            if ($filter === 'converted' || $filter === 'converted_webp') {
                $args['post_mime_type'] = ['image/webp'];
            } elseif ($filter === 'converted_avif') {
                $args['post_mime_type'] = ['image/avif'];
            } elseif ($filter === 'not_converted') {
                $args['post_mime_type'] = ['image/jpeg', 'image/png', 'image/jpg'];
            } else {
                // Include common image types by default when filter is 'all'
                $args['post_mime_type'] = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/avif'];
            }

            // Filter: Attached status
            if ($filter_attached === 'attached') {
                $args['post_parent__not_in'] = [0];
            } elseif ($filter_attached === 'unattached') {
                $args['post_parent'] = 0;
            }

            // Filter: Large Size (> 1MB) - This is tricky with WP_Query, so we scan IDs then filter
            // Or we use meta_query if size is stored (usually it's not indexed well)
            // Let's do simple query first

            $query = new WP_Query( $args );
            $ids = $query->posts;
            $data = [];

            foreach ($ids as $id) {
                $path = get_attached_file($id);
                $file_exists = file_exists($path);

                // if (!file_exists($path)) continue; // Don't skip, just mark

                $size = $file_exists ? filesize($path) : 0;

                // Size filter check (manual)
                if ($filter_size === 'large' && $size < 1048576) continue; // 1MB

                $meta = wp_get_attachment_metadata($id);
                $thumb = wp_get_attachment_image_src($id, 'thumbnail');
                $dims = isset($meta['width']) ? "{$meta['width']}x{$meta['height']}" : __( 'Unknown', 'pls-image-optimizer' );
                $filename = basename($path);
                $mime = get_post_mime_type($id);
                $is_webp = strpos($mime, 'webp') !== false;
                $is_avif = strpos($mime, 'avif') !== false;

                $data[] = [
                    'id' => $id,
                    'name' => $filename . ( $file_exists ? '' : ' ' . __( '(File Missing)', 'pls-image-optimizer' ) ),
                    'thumb' => $thumb ? $thumb[0] : '',
                    'size' => size_format($size, 2),
                    'dims' => $dims,
                    'is_webp' => $is_webp,
                    'is_avif' => $is_avif,
                    'link' => get_edit_post_link($id)
                ];
            }

            wp_send_json_success([
                'items' => $data,
                'total' => $query->found_posts,
                'max_pages' => $query->max_num_pages
            ]);
        }

        /**
         * Handle image conversion AJAX request
         */
        public function handle_convert() {
            check_ajax_referer( 'pls_imgopt_nonce', 'nonce' );

            global $wpdb;

            $id = intval( $_POST['id'] );
            $quality = intval( $_POST['quality'] );
            $format = isset( $_POST['format'] ) ? sanitize_text_field( $_POST['format'] ) : 'webp';
            $max_width = isset( $_POST['max_width'] ) ? intval( $_POST['max_width'] ) : 0;

            $main_path = get_attached_file( $id );
            if ( ! file_exists( $main_path ) ) {
                wp_send_json_error( __( 'File not found', 'pls-image-optimizer' ) );
                return;
            }

            $meta = wp_get_attachment_metadata( $id );
            $base_dir = dirname( $main_path );
            $replacements = [];
            $old_main_size = filesize( $main_path );

            // 0. Resize first (if max_width > 0)
            $resize_info = '';
            if ( $max_width > 0 ) {
                $resize_result = $this->resizer->resize( $main_path, $max_width );
                if ( $resize_result && $resize_result['resized'] ) {
                    $resize_info = __( 'Resize: ', 'pls-image-optimizer' ) . $resize_result['message'] . ' | ';
                    // Update metadata after resize
                    if ( is_array( $meta ) ) {
                        $meta['width'] = $resize_result['width'];
                        $meta['height'] = $resize_result['height'];
                        wp_update_attachment_metadata( $id, $meta );
                    }
                }
            }

            // 1. Convert main image
            $main_result = $this->convert_main_image( $id, $main_path, $quality, $old_main_size, $format );
            $main_saved_info = $main_result['info'];

            if ( $main_result['new_path'] ) {
                $old_main_name = basename( $main_path );
                $new_main_name = basename( $main_result['new_path'] );

                // Update metadata 'file' to point to new converted file
                if ( isset( $meta['file'] ) ) {
                    $meta['file'] = str_replace( $old_main_name, $new_main_name, $meta['file'] );
                }

                if ( $old_main_name !== $new_main_name ) {
                    $replacements[ $old_main_name ] = $new_main_name;
                }
            }

            // 2. Convert variants
            $variants_done = $this->convert_variants( $id, $meta, $base_dir, $quality, $replacements, $format );

            // ALWAYS update metadata to ensure main file path is saved even if no variants
            wp_update_attachment_metadata( $id, $meta );

            // 3. Replace in database
            if ( ! empty( $replacements ) ) {
                $this->replace_in_database( $replacements );
            }

            if ( $main_result['new_path'] ) {
                update_option( 'pls_img_opt_count', (int) get_option( 'pls_img_opt_count', 0 ) + 1 );
            }

            wp_send_json_success( "{$resize_info}{$main_saved_info} (+" . sprintf( __( '%s sizes', 'pls-image-optimizer' ), $variants_done ) . ")" );
        }

        /**
         * Convert main image
         */
        private function convert_main_image( $id, $path, $quality, $old_size, $format = 'webp' ) {
            $new_path = $this->converter->convert_file( $path, $quality, $format );

            if ( $new_path ) {
                $new_size = filesize( $new_path );
                $percent = $this->converter->calculate_savings( $old_size, $new_size );
                $new_ext = pathinfo( $new_path, PATHINFO_EXTENSION );
                $mime_type = 'image/' . $new_ext;

                update_attached_file( $id, $new_path );
                wp_update_post([
                    'ID' => $id,
                    'post_mime_type' => $mime_type
                ]);

                return [
                    'new_path' => $new_path,
                    'info' => ucfirst($new_ext) . " -{$percent}%"
                ];
            }

            return [
                'new_path' => false,
                'info' => __( 'Original Skipped', 'pls-image-optimizer' )
            ];
        }

        /**
         * Convert variants
         */
        private function convert_variants( $id, &$meta, $base_dir, $quality, &$replacements, $format = 'webp' ) {
            $variants_done = 0;

            if ( ! isset( $meta['sizes'] ) || ! is_array( $meta['sizes'] ) ) {
                return 0;
            }

            foreach ( $meta['sizes'] as $size_name => $size_info ) {
                $variant_name = $size_info['file'];
                $variant_path = $base_dir . '/' . $variant_name;

                // Check if file exists before trying to convert
                if ( ! file_exists( $variant_path ) ) {
                    continue;
                }

                $new_variant_path = $this->converter->convert_file( $variant_path, $quality, $format );

                if ( $new_variant_path ) {
                    $new_variant_name = basename( $new_variant_path );
                    $new_ext = pathinfo( $new_variant_path, PATHINFO_EXTENSION );
                    $meta['sizes'][ $size_name ]['file'] = $new_variant_name;
                    $meta['sizes'][ $size_name ]['mime-type'] = 'image/' . $new_ext;

                    if ( $variant_name !== $new_variant_name ) {
                        $replacements[ $variant_name ] = $new_variant_name;
                    }
                    $variants_done++;
                }
            }

            return $variants_done;
        }

        /**
         * Replace old filenames in database
         */
        private function replace_in_database( $replacements ) {
            global $wpdb;

            foreach ( $replacements as $old => $new ) {
                // Update Posts Content
                $wpdb->query( $wpdb->prepare(
                    "UPDATE $wpdb->posts SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s",
                    $old, $new, '%' . $wpdb->esc_like( $old ) . '%'
                ));

                // Update Post Meta
                $wpdb->query( $wpdb->prepare(
                    "UPDATE $wpdb->postmeta SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
                    $old, $new, '%' . $wpdb->esc_like( $old ) . '%'
                ));
            }
        }
    }
}
