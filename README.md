# PLS Image Optimizer

Công cụ nén ảnh hàng loạt (WebP/AVIF) + resize cho thư viện Media WordPress. Hoạt động độc lập, không cần plugin khác.

## Tính năng
- Quét thư viện Media, lọc theo định dạng / trạng thái / kích thước.
- Chuyển đổi hàng loạt sang WebP hoặc AVIF (ưu tiên Imagick, fallback GD).
- Resize ảnh gốc theo max-width chuẩn.
- Tự cập nhật tên file trong nội dung bài viết và postmeta sau khi convert.

## Yêu cầu
- WordPress 5.0+, PHP 7.4+ (khuyến nghị 8.1+ cho AVIF).
- GD (WebP) hoặc Imagick. AVIF cần GD/Imagick có hỗ trợ AVIF.

## Ghi chú
- Tách ra từ module Image Optimizer Pro của PLS Optimize Performance (v1.0.0, 2026-06-05).
- Dùng chung option `pls_img_opt_count` với PLS Optimize Performance để liền mạch số liệu.
- An toàn khi cài cùng PLS Optimize Performance (class dùng chung được bảo vệ bằng class_exists).

## Tác giả
PLS — https://pha.vn
