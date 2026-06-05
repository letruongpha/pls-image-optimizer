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
    </div>
</div>
