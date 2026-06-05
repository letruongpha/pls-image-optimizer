<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<div class="wrap pls-imgopt-admin-wrap">
    <!-- Main Dashboard Header -->
    <div class="pls-dashboard-header">
        <div class="pls-header-title">
            <span class="dashicons dashicons-admin-customizer"></span>
            <div>
                <h1><?php esc_html_e( 'PLS Image Optimizer', 'pls-image-optimizer' ); ?></h1>
                <p class="pls-header-subtitle"><?php esc_html_e( 'Công cụ tối ưu và nén ảnh hàng loạt thế hệ mới (WebP/AVIF)', 'pls-image-optimizer' ); ?></p>
            </div>
        </div>

        <div class="pls-status-bar">
            <div class="pls-status-item tool">
                <span class="dashicons dashicons-admin-settings"></span>
                <div class="pls-status-text">
                    <span class="pls-status-label"><?php _e( 'Engine:', 'pls-image-optimizer' ); ?></span>
                    <span class="pls-status-val engine"><?php echo extension_loaded('imagick') ? esc_html__( 'Imagick', 'pls-image-optimizer' ) : esc_html__( 'GD Library', 'pls-image-optimizer' ); ?></span>
                </div>
            </div>
            <?php
                $converter = new PLS_Media_Converter();
                $webp_st = $converter->get_webp_support_detail();
                $avif_st = $converter->get_avif_support_detail();
            ?>
            <div class="pls-status-item format-status">
                <span class="dashicons dashicons-yes-alt"></span>
                <div class="pls-status-text">
                    <span class="pls-status-label"><?php esc_html_e( 'WebP:', 'pls-image-optimizer' ); ?></span>
                    <?php echo $webp_st['supported'] ? '<span class="pls-badge supported">' . __( 'Supported', 'pls-image-optimizer' ) . '</span>' : '<span class="pls-badge unsupported">' . __( 'Missing', 'pls-image-optimizer' ) . ' (' . $webp_st['reason'] . ')</span>'; ?>
                </div>
            </div>
            <div class="pls-status-item format-status">
                <span class="dashicons dashicons-yes-alt"></span>
                <div class="pls-status-text">
                    <span class="pls-status-label"><?php esc_html_e( 'AVIF:', 'pls-image-optimizer' ); ?></span>
                    <?php echo $avif_st['supported'] ? '<span class="pls-badge supported avif">' . __( 'Supported', 'pls-image-optimizer' ) . '</span>' : '<span class="pls-badge unsupported">' . __( 'Missing', 'pls-image-optimizer' ) . ' (' . $avif_st['reason'] . ')</span>'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="pls-media-pro-wrap">
        
        <!-- Config & Filter Panel Grid -->
        <div class="pls-dashboard-grid">
            
            <!-- Configuration Card -->
            <div class="pls-dashboard-card config-card">
                <div class="pls-card-header">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <h3><?php _e( 'Cấu Hình Nén Ảnh', 'pls-image-optimizer' ); ?></h3>
                </div>
                <div class="pls-card-body">
                    <div class="pls-form-row">
                        <div class="pls-form-group">
                            <label for="pls-target-format"><?php _e( 'Định dạng mục tiêu:', 'pls-image-optimizer' ); ?></label>
                            <div class="pls-select-wrapper">
                                <select id="pls-target-format">
                                    <option value="webp"><?php esc_html_e( 'WebP (Khuyên dùng)', 'pls-image-optimizer' ); ?></option>
                                    <option value="avif" <?php echo !$avif_st['supported'] ? 'disabled' : ''; ?>><?php esc_html_e( 'AVIF (Nén siêu cao)', 'pls-image-optimizer' ); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="pls-form-group">
                            <label for="pls-webp-quality"><?php _e( 'Chất lượng (Quality):', 'pls-image-optimizer' ); ?></label>
                            <input type="number" id="pls-webp-quality" value="80" min="1" max="100">
                        </div>
                    </div>

                    <div class="pls-form-group width-100">
                        <label for="pls-max-width"><?php _e( 'Thay đổi kích thước tối đa (Resize):', 'pls-image-optimizer' ); ?></label>
                        <div class="pls-select-wrapper">
                            <select id="pls-max-width">
                                <?php foreach ($resize_presets as $width => $label) : ?>
                                    <option value="<?php echo esc_attr($width); ?>" <?php selected($width, 1920); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="pls-dashboard-card filter-card">
                <div class="pls-card-header">
                    <span class="dashicons dashicons-filter"></span>
                    <h3><?php _e( 'Bộ Lọc Thư Viện', 'pls-image-optimizer' ); ?></h3>
                </div>
                <div class="pls-card-body">
                    <div class="pls-form-row">
                        <div class="pls-form-group">
                            <label for="pls-filter-status"><?php _e( 'Trạng thái nén:', 'pls-image-optimizer' ); ?></label>
                            <div class="pls-select-wrapper">
                                <select id="pls-filter-status">
                                    <option value="all"><?php _e( 'Tất cả ảnh', 'pls-image-optimizer' ); ?></option>
                                    <option value="converted_webp"><?php _e( 'Đã nén (WebP)', 'pls-image-optimizer' ); ?></option>
                                    <option value="converted_avif"><?php _e( 'Đã nén (AVIF)', 'pls-image-optimizer' ); ?></option>
                                    <option value="not_converted"><?php _e( 'Chưa nén', 'pls-image-optimizer' ); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="pls-form-group">
                            <label for="pls-filter-attached"><?php _e( 'Liên kết bài viết:', 'pls-image-optimizer' ); ?></label>
                            <div class="pls-select-wrapper">
                                <select id="pls-filter-attached">
                                    <option value="all"><?php _e( 'Tất cả trạng thái', 'pls-image-optimizer' ); ?></option>
                                    <option value="attached"><?php _e( 'Đã liên kết', 'pls-image-optimizer' ); ?></option>
                                    <option value="unattached"><?php _e( 'Chưa liên kết', 'pls-image-optimizer' ); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pls-form-row align-end">
                        <div class="pls-form-group flex-grow">
                            <label for="pls-filter-size"><?php _e( 'Kích thước dung lượng:', 'pls-image-optimizer' ); ?></label>
                            <div class="pls-select-wrapper">
                                <select id="pls-filter-size">
                                    <option value="all"><?php _e( 'Tất cả dung lượng', 'pls-image-optimizer' ); ?></option>
                                    <option value="large"><?php _e( 'Ảnh nặng (> 1MB)', 'pls-image-optimizer' ); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="pls-form-group button-group">
                            <button type="button" id="pls-scan-images" class="pls-btn pls-btn-primary pls-btn-glow">
                                <span class="dashicons dashicons-search"></span>
                                <?php _e( 'Scan Images', 'pls-image-optimizer' ); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Pagination Top -->
        <div class="pls-pagination-wrapper top" style="display:none; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <div class="pls-pagination-info" style="color:#64748b; font-size:13px; font-weight: 500;"></div>
            <div class="pls-pagination-nav">
                 <button type="button" class="pls-btn-page pls-page-prev" disabled>&laquo; <?php _e('Prev', 'pls-image-optimizer'); ?></button>
                 <span class="pls-page-current" style="margin: 0 10px; font-weight:600;">1</span>
                 <button type="button" class="pls-btn-page pls-page-next" disabled><?php _e('Next', 'pls-image-optimizer'); ?> &raquo;</button>
            </div>
        </div>

        <!-- Progress Bar & Active Console -->
        <div class="pls-progress-container">
            <div class="pls-progress-bar">
                <div class="pls-progress-fill">0%</div>
            </div>
        </div>

        <!-- List Controls & Selection Header -->
        <div class="pls-list-controls" style="display:none;">
            <div class="pls-select-left">
                <label class="pls-checkbox-label">
                    <input type="checkbox" id="pls-select-all">
                    <span><?php _e( 'Chọn tất cả trên trang này', 'pls-image-optimizer' ); ?></span>
                </label>
                <span class="pls-selection-count" style="margin-left: 15px; font-weight: 600; color: #4f46e5;"><?php _e( '0 selected', 'pls-image-optimizer' ); ?></span>
            </div>
            <button type="button" id="pls-start-convert" class="pls-btn pls-btn-success pls-btn-glow" disabled>
                <span class="dashicons dashicons-performance"></span>
                <?php _e( 'Start Optimization', 'pls-image-optimizer' ); ?>
            </button>
        </div>

        <!-- Image List -->
        <div id="pls-image-list" class="pls-image-list">
            <div class="pls-empty-state">
                <span class="dashicons dashicons-images-alt2"></span>
                <p><?php _e( 'Nhấp nút "Scan Images" để tải danh sách hình ảnh cần tối ưu hóa.', 'pls-image-optimizer' ); ?></p>
            </div>
        </div>

        <!-- Pagination Bottom -->
        <div class="pls-pagination-wrapper bottom" style="display:none; justify-content: space-between; align-items: center; margin-top: 15px;">
            <div class="pls-pagination-info" style="color:#64748b; font-size:13px; font-weight: 500;"></div>
            <div class="pls-pagination-nav">
                 <button type="button" class="pls-btn-page pls-page-prev" disabled>&laquo; <?php _e('Prev', 'pls-image-optimizer'); ?></button>
                 <span class="pls-page-current" style="margin: 0 10px; font-weight:600;">1</span>
                 <button type="button" class="pls-btn-page pls-page-next" disabled><?php _e('Next', 'pls-image-optimizer'); ?> &raquo;</button>
            </div>
        </div>

        <!-- Completion Modal -->
        <div id="pls-completion-modal" class="pls-modal-overlay">
            <div class="pls-modal">
                <div class="pls-modal-icon">
                    <span class="dashicons dashicons-yes"></span>
                </div>
                <h3><?php _e( 'Tối Ưu Hóa Hoàn Tất!', 'pls-image-optimizer' ); ?></h3>
                <p id="pls-completion-msg"><?php _e( 'Các tệp hình ảnh đã được xử lý và nén thành công.', 'pls-image-optimizer' ); ?></p>
                <div class="pls-modal-buttons">
                    <button class="pls-btn pls-btn-secondary" id="pls-modal-stay"><?php _e( 'Ở lại đây', 'pls-image-optimizer' ); ?></button>
                    <button class="pls-btn pls-btn-primary" id="pls-modal-refresh"><?php _e( 'Tải lại trang', 'pls-image-optimizer' ); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
