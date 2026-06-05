jQuery(document).ready(function($) {
    var i18n = (typeof pls_imgopt_vars !== 'undefined' && pls_imgopt_vars.strings) ? pls_imgopt_vars.strings : {};
    function t(key, fallback) {
        return Object.prototype.hasOwnProperty.call(i18n, key) ? i18n[key] : fallback;
    }
    function tf(key, fallback, value) {
        var template = t(key, fallback);
        if (typeof value === 'undefined') {
            return template;
        }
        if (template.indexOf('%s') !== -1) {
            return template.replace('%s', value);
        }
        if (template.indexOf('%d') !== -1) {
            return template.replace('%d', value);
        }
        return template + ' ' + value;
    }

    if ($('.pls-media-pro-wrap').length) {
        var isScanning = false;
        var totalImages = 0;
        var processedImages = 0;
        var imagesToProcess = [];
        var currentPage = 1;
        var totalPages = 1;

        // Update Stats UI
        function updateStats(total, current) {
            var percent = total > 0 ? Math.round((current / total) * 100) : 0;
            $('.pls-progress-fill').css('width', percent + '%').text(percent + '%'); // Fixed selector
        }

        // Scan/Load Function
        function loadImages(page) {
            if (isScanning) return;
            isScanning = true;

            var $btn = $('#pls-scan-images');
            var $list = $('#pls-image-list');

            // Only show loading if it's a new scan or distinct action,
            // but for pagination maybe just opacity?
            $list.css('opacity', 0.5);
            $btn.addClass('disabled').text(t('scanning', 'Scanning...'));

            // If page 1, clear list? Yes.
            // Actually, we replace list content every time now.

            var filter = $('#pls-filter-status').val(); // Correct ID
            var filterAttached = $('#pls-filter-attached').val();
            var filterSize = $('#pls-filter-size').val();

            console.log('Loading images page ' + page, { filter, filterAttached, filterSize });

            $.ajax({
                url: pls_imgopt_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'pls_imgopt_scan',
                    nonce: pls_imgopt_vars.nonce,
                    filter: filter,
                    filter_attached: filterAttached,
                    filter_size: filterSize,
                    paged: page
                },
                success: function(res) {
                    if (res.success) {
                        imagesToProcess = res.data.items;
                        totalImages = res.data.total;
                        totalPages = res.data.max_pages;
                        currentPage = page;
                        processedImages = 0;
                        updateStats(totalImages, 0);

                        renderList(imagesToProcess);
                        updatePaginationUI();

                        if (totalImages > 0) {
                            $('.pls-list-controls').slideDown();
                            $('.pls-pagination-wrapper').css('display', 'flex'); // Show pagination
                        } else {
                            $('.pls-list-controls').slideUp();
                            $('.pls-pagination-wrapper').hide();
                            $list.html('<div class="notice notice-info inline" style="margin:20px"><p>' + t('no_images_found', 'No images found.') + '</p></div>');
                        }
                    } else {
                        alert(tf('scan_failed_with_reason', 'Scan failed: %s', (res.data || t('unknown_error', 'Unknown error'))));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert(t('scan_request_failed', 'Scan request failed.'));
                },
                complete: function() {
                    $btn.removeClass('disabled').text(t('scan_images', 'Scan Images'));
                    $list.css('opacity', 1);
                    isScanning = false;
                }
            });
        }

        function renderList(items) {
            var listHtml = '';
            items.forEach(function(img) {
                listHtml += renderImageRow(img);
            });
            $('#pls-image-list').html(listHtml);

            // Reset "Select All"
            $('#pls-select-all').prop('checked', false);
            updateSelectionCount();
        }

        function updatePaginationUI() {
            $('.pls-page-current').text(currentPage + ' / ' + totalPages);
            $('.pls-pagination-info').text(tf('total_images', 'Total: %d images', totalImages));

            $('.pls-page-prev').prop('disabled', currentPage <= 1);
            $('.pls-page-next').prop('disabled', currentPage >= totalPages);
        }

        // Button Handlers
        $('#pls-scan-images').click(function(e) {
            e.preventDefault();
            currentPage = 1;
            loadImages(1);
        });

        $('.pls-page-prev').click(function(e) {
            e.preventDefault();
            if(currentPage > 1) loadImages(currentPage - 1);
        });

        $('.pls-page-next').click(function(e) {
            e.preventDefault();
            if(currentPage < totalPages) loadImages(currentPage + 1);
        });

        function renderImageRow(img) {
            var badge = '<span class="pls-img-status-badge">' + t('status_original', 'Original') + '</span>';
            if (img.is_webp) badge = '<span class="pls-img-status-badge webp">' + t('status_webp', 'WebP') + '</span>';
            if (img.is_avif) badge = '<span class="pls-img-status-badge avif">' + t('status_avif', 'AVIF') + '</span>';

            var thumb = img.thumb ? '<img src="' + img.thumb + '" class="pls-img-thumb">' : '<div class="pls-img-thumb"></div>';

            return '<div class="pls-image-item" id="img-' + img.id + '" data-id="' + img.id + '">' +
                   '<div class="pls-img-check"><input type="checkbox" value="' + img.id + '"></div>' +
                   thumb +
                   '<div class="pls-img-details">' +
                       '<span class="pls-img-name">' + img.name + '</span>' +
                       '<span class="pls-img-meta">' + img.dims + ' | ' + img.size + '</span>' +
                   '</div>' +
                   '<div class="status-col">' + badge + '</div>' +
                   '<a href="' + img.link + '" target="_blank" class="pls-img-link">' + t('edit', 'Edit') + '</a>' +
                   '</div>';
        }

        // --- Selection Logic ---
        $('#pls-select-all').change(function() {
            var isChecked = $(this).is(':checked');
            $('.pls-img-check input').prop('checked', isChecked);
            updateSelectionCount();
        });

        $(document).on('change', '.pls-img-check input', function() {
            updateSelectionCount();
            var allChecked = $('.pls-img-check input:checked').length === $('.pls-img-check input').length;
            $('#pls-select-all').prop('checked', allChecked);
        });

        function updateSelectionCount() {
            var count = $('.pls-img-check input:checked').length;
            $('.pls-selection-count').text(count + t('selected_suffix', ' selected'));
            $('#pls-start-convert').prop('disabled', count === 0);
        }

        // --- Optimization Logic ---
        $('#pls-start-convert').click(function(e) {
            e.preventDefault();
            var selectedIds = [];
            $('.pls-img-check input:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) return;

            // Filter imagesToProcess to only include selected
            var selectedImages = imagesToProcess.filter(function(img) {
                return selectedIds.includes(img.id.toString());
            });

            startOptimization(selectedImages);
        });

        function startOptimization(queue) {
            if (queue.length === 0) return;

            var $btn = $('#pls-start-convert');
            $btn.addClass('disabled').text(t('optimizing', 'Optimizing...'));
            $('.pls-progress-bar').show();

            isScanning = true;
            processedImages = 0;
            var processQueue = queue;

            function processNext() {
                if (processedImages >= processQueue.length) {
                    isScanning = false;
                    $btn.removeClass('disabled').text(t('start_optimization', 'Start Optimization'));
                    $('#pls-completion-modal').css('display', 'flex');
                    return;
                }

                var img = processQueue[processedImages];
                var $row = $('#img-' + img.id);
                if ($row.length) {
                    $row.find('.status-col').html('<span class="spinner is-active" style="float:none; margin:0;"></span>');
                }

                $.ajax({
                    url: pls_imgopt_vars.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'pls_imgopt_convert',
                        nonce: pls_imgopt_vars.nonce,
                        id: img.id,
                        quality: $('#pls-webp-quality').val() || 80,
                        format: $('#pls-target-format').val() || 'webp',
                        max_width: $('#pls-max-width').val() || 0
                    },
                    success: function(res) {
                        processedImages++;
                        updateStats(processQueue.length, processedImages);

                        if ($row.length) {
                            if (res.success) {
                                $row.find('.status-col').html('<span class="pls-img-status-badge webp">' + t('status_done', 'Done') + '</span> <small style="color:green">' + res.data + '</small>');
                                $row.find('.pls-img-check input').prop('checked', false).prop('disabled', true);
                            } else {
                                $row.find('.status-col').html('<span class="pls-img-status-badge" style="background:#fee; color:red">' + t('status_error', 'Error') + '</span>');
                                console.error('Image ' + img.id + ' error:', res.data);
                            }
                        }
                        processNext();
                    },
                    error: function() {
                        processedImages++;
                        processNext();
                    }
                });
            }

            processNext();
        }

        // Modal Actions
        $('#pls-modal-stay').click(function(e) {
            e.preventDefault();
            $('#pls-completion-modal').hide();
            $('.pls-img-check input').prop('checked', false);
            updateSelectionCount();
        });

        $('#pls-modal-refresh').click(function(e) {
             location.reload();
        });
    }
});
