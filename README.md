<div align="center">

# 🖼️ PLS Image Optimizer

### Nén ảnh hàng loạt thế hệ mới cho WordPress — WebP & AVIF

**Giảm tới 80% dung lượng ảnh • Tăng tốc website • Cải thiện điểm Core Web Vitals & SEO**

[![Version](https://img.shields.io/badge/version-1.0.0-4f46e5.svg?style=for-the-badge)](https://github.com/letruongpha/pls-image-optimizer)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-21759b.svg?style=for-the-badge&logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4.svg?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net)
[![License](https://img.shields.io/badge/license-GPLv2-blue.svg?style=for-the-badge)](https://www.gnu.org/licenses/gpl-2.0.html)

[Tính năng](#-tính-năng-nổi-bật) • [Cài đặt](#-cài-đặt) • [Hướng dẫn](#-hướng-dẫn-sử-dụng) • [Yêu cầu](#-yêu-cầu-hệ-thống) • [FAQ](#-câu-hỏi-thường-gặp-faq)

</div>

---

## 📖 Giới thiệu

**PLS Image Optimizer** là plugin tối ưu hình ảnh hoạt động **hoàn toàn độc lập** cho WordPress, được phát triển bởi [Pha Le Solution](https://phalesolution.com). Plugin giúp bạn chuyển đổi hàng loạt ảnh trong thư viện Media sang định dạng nén hiện đại **WebP** hoặc **AVIF**, kết hợp **resize** kích thước gốc — tất cả chỉ với vài cú nhấp chuột, ngay trong trang quản trị WordPress.

> 💡 Ảnh thường chiếm **50–70% dung lượng tải trang**. Tối ưu ảnh là cách nhanh nhất và hiệu quả nhất để tăng tốc website của bạn.

---

## ✨ Tính năng nổi bật

| Tính năng | Mô tả |
|-----------|-------|
| 🚀 **Nén hàng loạt WebP / AVIF** | Chuyển đổi nhiều ảnh cùng lúc sang định dạng nén tối tân, giảm dung lượng tới **80%** mà mắt thường khó phân biệt. |
| ⚙️ **Tự động chọn engine tối ưu** | Ưu tiên **Imagick**, tự động **fallback sang GD Library** khi cần — luôn dùng công cụ tốt nhất máy chủ có. |
| 📐 **Resize ảnh gốc** | Tự động thu nhỏ ảnh quá khổ về max-width chuẩn (600px → 1920px Full HD), bảo toàn tỷ lệ và **giữ kênh trong suốt (alpha)**. |
| 🔄 **Đồng bộ cơ sở dữ liệu thông minh** | Sau khi convert, plugin **tự cập nhật tên file** trong nội dung bài viết và `postmeta`. |
| 🛡️ **An toàn dữ liệu serialized** | Cập nhật `postmeta` qua WordPress API để **không phá hỏng dữ liệu serialized** của Elementor, Divi, ACF... (giải quyết lỗi `s:N:"..."` kinh điển). |
| 🎯 **Bộ lọc thư viện mạnh mẽ** | Lọc theo trạng thái nén (WebP/AVIF/chưa nén), liên kết bài viết (đã/chưa gắn) và dung lượng (ảnh nặng > 1MB). |
| 🎨 **Giao diện hiện đại** | Bảng điều khiển trực quan với thanh tiến trình real-time, phân trang, chọn hàng loạt và xem trước thumbnail. |
| 🔌 **Hoạt động độc lập** | Không cần cài thêm bất kỳ plugin nào. Tương thích an toàn khi chạy song song với **PLS Optimize Performance**. |
| 🌐 **Sẵn sàng đa ngôn ngữ** | Toàn bộ chuỗi văn bản dùng Text Domain `pls-image-optimizer`, sẵn sàng dịch (i18n-ready). |

---

## 🆚 WebP vs AVIF — Nên chọn gì?

| | **WebP** | **AVIF** |
|---|----------|----------|
| **Tỷ lệ nén** | Tốt (~25–35% nhỏ hơn JPEG) | Xuất sắc (~50% nhỏ hơn JPEG) |
| **Tương thích trình duyệt** | Rất rộng (gần như mọi trình duyệt hiện đại) | Tốt (trình duyệt mới) |
| **Tốc độ encode** | Nhanh | Chậm hơn |
| **Yêu cầu** | GD hoặc Imagick | PHP 8.1+ với GD/Imagick hỗ trợ AVIF |
| **Khuyến nghị** | ✅ Lựa chọn an toàn cho mọi website | ⚡ Khi cần nén tối đa & server đủ mạnh |

> Plugin sẽ **tự động fallback về WebP** nếu bạn chọn AVIF nhưng máy chủ chưa hỗ trợ — bạn không bao giờ bị kẹt.

---

## 📦 Cài đặt

### Cách 1 — Tải file ZIP
1. Tải plugin về dưới dạng file `.zip`.
2. Vào **WordPress Admin → Plugins → Add New → Upload Plugin**.
3. Chọn file ZIP, nhấn **Install Now**, sau đó **Activate**.

### Cách 2 — Cài thủ công qua FTP
```bash
# Giải nén và copy vào thư mục plugins
wp-content/plugins/pls-image-optimizer/
```
Sau đó vào **Plugins** trong Admin và kích hoạt.

### Cách 3 — Clone từ GitHub
```bash
cd wp-content/plugins/
git clone https://github.com/letruongpha/pls-image-optimizer.git
```

Sau khi kích hoạt, một mục menu mới **Image Optimizer** (icon thư viện ảnh) sẽ xuất hiện trên thanh quản trị bên trái.

---

## 🚀 Hướng dẫn sử dụng

1. **Mở** menu **Image Optimizer** trong trang quản trị WordPress.
2. **Kiểm tra trạng thái engine** ở thanh trên cùng: Imagick/GD, hỗ trợ WebP và AVIF.
3. **Cấu hình nén:**
   - Chọn **định dạng mục tiêu** (WebP – khuyên dùng, hoặc AVIF – nén siêu cao).
   - Đặt **chất lượng** (mặc định `80`, khoảng `75–85` cân bằng tốt nhất).
   - Chọn **kích thước resize tối đa** (mặc định 1920px Full HD).
4. **Lọc thư viện** theo trạng thái, liên kết bài viết, dung lượng nếu cần.
5. Nhấn **Scan Images** để tải danh sách ảnh.
6. **Chọn ảnh** (hoặc "Chọn tất cả trên trang này").
7. Nhấn **Start Optimization** và theo dõi **thanh tiến trình real-time**.
8. Hoàn tất! Plugin tự động cập nhật cơ sở dữ liệu và hiển thị mức dung lượng tiết kiệm được cho từng ảnh.

> ⚠️ **Khuyến nghị:** Luôn **sao lưu (backup)** website trước khi nén hàng loạt. Quá trình convert sẽ thay thế file gốc.

---

## 🔧 Yêu cầu hệ thống

| Thành phần | Yêu cầu |
|------------|---------|
| **WordPress** | 5.0 trở lên |
| **PHP** | 7.4 trở lên (khuyến nghị **8.1+** để dùng AVIF) |
| **Thư viện ảnh** | **GD Library** (cho WebP) hoặc **Imagick** |
| **AVIF** | GD/Imagick có biên dịch hỗ trợ AVIF (thường cần PHP 8.1+) |
| **Quyền** | `manage_options` (Quản trị viên) |

---

## 🏗️ Kiến trúc & Cách hoạt động

```
pls-image-optimizer/
├── pls-image-optimizer.php          # Entry point, định nghĩa constant & khởi tạo
├── inc/
│   ├── class-image-optimizer.php    # Controller: menu, AJAX scan/convert, đồng bộ DB
│   ├── class-media-converter.php    # Engine convert (Imagick → fallback GD)
│   ├── class-media-resizer.php      # Resize ảnh theo max-width, giữ alpha
│   └── lib/
│       └── class-pls-image-converter.php   # Helper GD thuần (WebP/AVIF), tái sử dụng được
├── admin/
│   └── view-optimizer-page.php      # Giao diện bảng điều khiển
└── assets/
    ├── css/image-optimizer.css      # Style giao diện admin
    └── js/image-optimizer.js        # Logic scan/convert phía client (AJAX)
```

**Luồng xử lý một ảnh:**

```
Scan (WP_Query)  →  Resize (nếu vượt max-width)  →  Convert (Imagick/GD)
      ↓                                                    ↓
  Hiển thị list                            Kiểm tra file mới có nhỏ hơn?
                                                           ↓ (có)
                              Cập nhật metadata + post_content + postmeta (serialized-safe)
                                                           ↓
                                              Tăng bộ đếm pls_img_opt_count
```

---

## ❓ Câu hỏi thường gặp (FAQ)

<details>
<summary><strong>Plugin có làm hỏng ảnh gốc của tôi không?</strong></summary>

Plugin **chỉ giữ file mới khi nó thực sự nhỏ hơn** file gốc, và có sanity-check loại bỏ file 0 byte. Tuy nhiên file gốc sẽ bị thay thế, nên hãy **backup trước khi chạy hàng loạt**.
</details>

<details>
<summary><strong>Tôi dùng Elementor / Divi / ACF, plugin có làm vỡ layout không?</strong></summary>

Không. Đây là điểm mạnh của plugin: việc cập nhật `postmeta` được thực hiện qua **WordPress API** thay vì câu lệnh `REPLACE()` thô, nên dữ liệu **serialized không bị hỏng** (lỗi `s:N:"..."` kinh điển được xử lý triệt để).
</details>

<details>
<summary><strong>Chọn AVIF nhưng server không hỗ trợ thì sao?</strong></summary>

Plugin **tự động fallback về WebP** để quá trình không bao giờ thất bại.
</details>

<details>
<summary><strong>Có dùng được cùng plugin PLS Optimize Performance không?</strong></summary>

Có. Các class dùng chung được bảo vệ bằng `class_exists()`, và plugin dùng chung option `pls_img_opt_count` để **liền mạch số liệu thống kê** giữa hai plugin.
</details>

<details>
<summary><strong>Tại sao một số ảnh bị bỏ qua (Skipped)?</strong></summary>

Vì file sau khi convert **lớn hơn hoặc bằng** file gốc — giữ file gốc sẽ tối ưu hơn nên plugin bỏ qua.
</details>

---

## 🌍 Tổng quan (English Summary)

**PLS Image Optimizer** is a standalone WordPress plugin by [Pha Le Solution](https://phalesolution.com) for **bulk image optimization**. It batch-converts your Media Library to next-gen **WebP / AVIF** formats and resizes oversized originals — directly from the WordPress admin.

**Key features:**
- Bulk WebP/AVIF conversion (Imagick with automatic GD fallback)
- Smart resizing with alpha-channel preservation
- **Serialized-data-safe** database sync (Elementor / Divi / ACF compatible)
- Powerful Media Library filters (format / attachment / size)
- Modern dashboard with real-time progress, pagination & bulk selection
- Fully standalone, i18n-ready, GPLv2 licensed

**Requirements:** WordPress 5.0+, PHP 7.4+ (8.1+ recommended for AVIF), GD or Imagick.

---

## 📝 Ghi chú phiên bản

- **v1.0.0** — Phiên bản đầu tiên. Tách độc lập từ module *Image Optimizer Pro* của **PLS Optimize Performance** (2026). Dùng chung option `pls_img_opt_count` để liền mạch số liệu; an toàn khi cài song song.

---

## 📄 Giấy phép

Plugin được phát hành theo giấy phép **[GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)**.

---

<div align="center">

## 👨‍💻 Tác giả

### **Pha Le Solution**

🌐 [phalesolution.com](https://phalesolution.com)

*Giải pháp WordPress & tối ưu hiệu năng website chuyên nghiệp*

---

⭐ **Nếu plugin hữu ích, hãy để lại một Star trên GitHub để ủng hộ chúng tôi!** ⭐

Made with ❤️ by Pha Le Solution

</div>
