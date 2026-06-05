<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="wrap">
    <h1><?php esc_html_e( 'PLS Image Optimizer', 'pls-image-optimizer' ); ?></h1>

    <div class="pls-media-pro-wrap">
        <div class="pls-status-bar">
            <div class="pls-status-item">
                <span class="dashicons dashicons-format-image"></span>
                <strong><?php _e( 'Tool:', 'pls-image-optimizer' ); ?></strong> <?php echo extension_loaded('imagick') ? esc_html__( 'Imagick', 'pls-image-optimizer' ) : esc_html__( 'GD Library', 'pls-image-optimizer' ); ?>
            </div>
            <?php
                $converter = new PLS_Media_Converter();
                $webp_st = $converter->get_webp_support_detail();
                $avif_st = $converter->get_avif_support_detail();
            ?>
            <div class="pls-status-item">
                <span class="dashicons dashicons-yes-alt"></span>
                <strong><?php esc_html_e( 'WebP:', 'pls-image-optimizer' ); ?></strong> <?php echo $webp_st['supported'] ? '<span style="color:green">' . __( 'Supported', 'pls-image-optimizer' ) . '</span>' : '<span style="color:red">' . __( 'Missing', 'pls-image-optimizer' ) . ' (' . $webp_st['reason'] . ')</span>'; ?>
            </div>
            <div class="pls-status-item">
                <span class="dashicons dashicons-yes-alt"></span>
                <strong><?php esc_html_e( 'AVIF:', 'pls-image-optimizer' ); ?></strong> <?php echo $avif_st['supported'] ? '<span style="color:green">' . __( 'Supported', 'pls-image-optimizer' ) . '</span>' : '<span style="color:red">' . __( 'Missing', 'pls-image-optimizer' ) . ' (' . $avif_st['reason'] . ')</span>'; ?>
            </div>
        </div>

        <div class="pls-settings-bar">
            <div class="pls-setting-group">
                <div class="pls-setting-item">
                    <label><?php _e( 'Format:', 'pls-image-optimizer' ); ?></label>
                    <select id="pls-target-format" style="max-width: 100px;">
                        <option value="webp"><?php esc_html_e( 'WebP', 'pls-image-optimizer' ); ?></option>
                        <option value="avif" <?php echo !$avif_st['supported'] ? 'disabled' : ''; ?>><?php esc_html_e( 'AVIF (Next-Gen)', 'pls-image-optimizer' ); ?></option>
                    </select>
                </div>
                <div class="pls-setting-item">
                    <label><?php _e( 'Quality:', 'pls-image-optimizer' ); ?></label>
                    <input type="number" id="pls-webp-quality" value="80" min="1" max="100" style="width: 60px;">
                </div>
                <div class="pls-setting-item">
                    <label><?php _e( 'Resize:', 'pls-image-optimizer' ); ?></label>
                    <select id="pls-max-width" style="max-width: 120px;">
                        <?php foreach ($resize_presets as $width => $label) : ?>
                            <option value="<?php echo esc_attr($width); ?>" <?php selected($width, 1920); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="pls-setting-group">
                <div class="pls-setting-item">
                    <select id="pls-filter-status">
                        <option value="all"><?php _e( 'All Images', 'pls-image-optimizer' ); ?></option>
                        <option value="converted_webp"><?php _e( 'Converted (WebP)', 'pls-image-optimizer' ); ?></option>
                        <option value="converted_avif"><?php _e( 'Converted (AVIF)', 'pls-image-optimizer' ); ?></option>
                        <option value="not_converted"><?php _e( 'Not Converted', 'pls-image-optimizer' ); ?></option>
                    </select>
                </div>
                <div class="pls-setting-item">
                    <select id="pls-filter-attached">
                        <option value="all"><?php _e( 'All Status', 'pls-image-optimizer' ); ?></option>
                        <option value="attached"><?php _e( 'Attached', 'pls-image-optimizer' ); ?></option>
                        <option value="unattached"><?php _e( 'Unattached', 'pls-image-optimizer' ); ?></option>
                    </select>
                </div>
                <div class="pls-setting-item">
                    <select id="pls-filter-size">
                        <option value="all"><?php _e( 'All Sizes', 'pls-image-optimizer' ); ?></option>
                        <option value="large"><?php _e( 'Large (>1MB)', 'pls-image-optimizer' ); ?></option>
                    </select>
                </div>
            </div>

            <div class="pls-setting-item">
                <button type="button" id="pls-scan-images" class="button button-primary"><?php _e( 'Scan Images', 'pls-image-optimizer' ); ?></button>
            </div>
        </div>

        <!-- Pagination Top -->
        <div class="pls-pagination-wrapper top" style="display:none; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <div class="pls-pagination-info" style="color:#666; font-size:13px;"></div>
            <div class="pls-pagination-nav">
                 <button type="button" class="button pls-page-prev" disabled>&laquo; <?php _e('Prev', 'pls-image-optimizer'); ?></button>
                 <span class="pls-page-current" style="margin: 0 10px; font-weight:600;">1</span>
                 <button type="button" class="button pls-page-next" disabled><?php _e('Next', 'pls-image-optimizer'); ?> &raquo;</button>
            </div>
        </div>

        <!-- List Controls -->
        <div class="pls-list-controls" style="display:none;">
            <div style="display:flex; align-items:center;">
                <label style="font-weight:600;"><input type="checkbox" id="pls-select-all"> <?php _e( 'Select All', 'pls-image-optimizer' ); ?></label>
                <span class="pls-selection-count" style="margin-left: 15px; color: #666;"><?php _e( '0 selected', 'pls-image-optimizer' ); ?></span>
            </div>
            <button type="button" id="pls-start-convert" class="button button-primary button-small" disabled><?php _e( 'Start Optimization', 'pls-image-optimizer' ); ?></button>
        </div>

        <div class="pls-progress-bar">
            <div class="pls-progress-fill">0%</div>
        </div>

        <div id="pls-image-list" class="pls-image-list">
            <p style="text-align:center; color:#666; margin-top:30px;"><?php _e( 'Click "Scan Images" to start optimizing your library.', 'pls-image-optimizer' ); ?></p>
        </div>

        <div class="pls-pagination-wrapper bottom" style="display:none; justify-content: space-between; align-items: center; margin-top: 15px;">
            <div class="pls-pagination-info" style="color:#666; font-size:13px;"></div>
            <div class="pls-pagination-nav">
                 <button type="button" class="button pls-page-prev" disabled>&laquo; <?php _e('Prev', 'pls-image-optimizer'); ?></button>
                 <span class="pls-page-current" style="margin: 0 10px; font-weight:600;">1</span>
                 <button type="button" class="button pls-page-next" disabled><?php _e('Next', 'pls-image-optimizer'); ?> &raquo;</button>
            </div>
        </div>

        <!-- Completion Modal -->
        <div id="pls-completion-modal" class="pls-modal-overlay">
            <div class="pls-modal">
                <h3><span class="dashicons dashicons-yes" style="color: green; font-size: 24px; width: 24px; height: 24px;"></span> <?php _e( 'Optimization Complete!', 'pls-image-optimizer' ); ?></h3>
                <p id="pls-completion-msg"><?php _e( 'Processed successfully.', 'pls-image-optimizer' ); ?></p>
                <div class="pls-modal-buttons">
                    <button class="button button-secondary" id="pls-modal-stay"><?php _e( 'Stay Here', 'pls-image-optimizer' ); ?></button>
                    <button class="button button-primary" id="pls-modal-refresh"><?php _e( 'Refresh Page', 'pls-image-optimizer' ); ?></button>
                </div>
            </div>
        </div>

        <style>
            .pls-media-pro-wrap { padding: 10px 0; }
            .pls-status-bar { display: flex; gap: 20px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
            .pls-status-item { display: flex; align-items: center; gap: 5px; }

            .pls-settings-bar { background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; align-items: center; justify-content: space-between; }
            .pls-setting-group { display: flex; gap: 10px; align-items: center; }
            .pls-setting-item { display: flex; align-items: center; gap: 8px; font-weight: 500; }

            .pls-list-controls { display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; background: #fff; border: 1px solid #ddd; border-bottom: none; border-radius: 4px 4px 0 0; }

            .pls-progress-bar { width: 100%; background: #e5e7eb; height: 24px; border-radius: 0; margin-bottom: 0; overflow: hidden; display: none; border: 1px solid #ddd; border-top: none; }
            .pls-progress-fill { width: 0%; height: 100%; background: #2271b1; color: #fff; text-align: center; line-height: 24px; font-size: 12px; transition: width 0.3s; }

            .pls-image-list { max-height: 600px; overflow-y: auto; border: 1px solid #ddd; border-radius: 0 0 4px 4px; background: #fff; }
            .pls-image-item { display: flex; align-items: center; padding: 10px 15px; border-bottom: 1px solid #eee; transition: background 0.2s; }
            .pls-image-item:last-child { border-bottom: none; }
            .pls-image-item:hover { background-color: #f9f9f9; }
            .pls-image-item.selected { background-color: #f0f6fc; }

            .pls-img-check { margin-right: 15px !important; }
            .pls-img-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; background: #eee; margin-right: 15px; border: 1px solid #ddd; }
            .pls-img-details { flex: 1; }
            .pls-img-name { font-weight: 600; color: #1d2327; display: block; margin-bottom: 4px; font-size: 13px; }
            .pls-img-meta { font-size: 12px; color: #646970; }
            .pls-img-status-badge { display: inline-block; font-size: 10px; padding: 2px 6px; border-radius: 3px; background: #f0f0f1; color: #50575e; margin-left: 5px; text-transform: uppercase; }
            .pls-img-status-badge.webp { background: #d1fae5; color: #065f46; }
            .pls-img-status-badge.avif { background: #dbeafe; color: #1e40af; }
            .pls-img-link { float: right; color: #2271b1; text-decoration: none; font-size: 12px; visibility: hidden; }
            .pls-image-item:hover .pls-img-link { visibility: visible; }

            /* Modal */
            .pls-modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 100000; display: none; align-items: center; justify-content: center; }
            .pls-modal { background: #fff; padding: 30px; border-radius: 8px; width: 400px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); text-align: center; }
            .pls-modal h3 { margin-top: 0; display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 18px; }
            .pls-modal-buttons { margin-top: 25px; display: flex; justify-content: center; gap: 15px; }
        </style>
    </div>
</div>
