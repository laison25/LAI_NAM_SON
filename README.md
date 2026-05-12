# Shop Lzon - Website bán mô hình sưu tầm

## 1. Tên đề tài

**Xây dựng website bán mô hình sưu tầm Shop Lzon bằng WordPress và WooCommerce**

## 2. Giới thiệu website/hệ thống

Shop Lzon là website thương mại điện tử phục vụ việc bán các sản phẩm mô hình sưu tầm như anime figure, Pokemon, One Piece, resin và mini figure.

Website được xây dựng dựa trên nền tảng mã nguồn mở WordPress, kết hợp WooCommerce để quản lý sản phẩm, giỏ hàng, thanh toán và đơn hàng. Ngoài các chức năng có sẵn của WooCommerce, dự án có thêm plugin tự lập trình riêng để nhập/xuất sản phẩm bằng file Excel-compatible, thanh toán VNPay sandbox và QR Demo.

Mục tiêu của hệ thống:

- Giới thiệu và bán sản phẩm mô hình sưu tầm.
- Cho phép khách hàng xem sản phẩm, thêm vào giỏ hàng và đặt hàng.
- Hỗ trợ đăng ký, đăng nhập tài khoản khách hàng.
- Hỗ trợ quản trị viên quản lý sản phẩm, đơn hàng và dữ liệu sản phẩm.
- Bổ sung chức năng nhập/xuất file phục vụ quản lý sản phẩm.
- Mô phỏng quy trình thanh toán online bằng VNPay sandbox và QR Demo.

## 3. Danh sách thành viên

| STT | Họ và tên | MSSV | Vai trò |
| --- | --- | --- | --- |
| 1 | Lại Nam Sơn | 23810310088 | Nhóm trưởng |
| 2 | Nguyễn Thành Vinh | 23810310107 | Thành viên |
| 3 | Nguyễn Văn Phương | 23810310101 | Thành viên |

## 4. Phân công nhiệm vụ cụ thể

| Thành viên | Nhiệm vụ |
| --- | --- |
| Lại Nam Sơn | Cài đặt WordPress, cấu hình WooCommerce, thiết kế giao diện, tùy biến theme, xây dựng plugin Lzon Project Tools, cấu hình VNPay sandbox, QR Demo, nhập/xuất Excel-compatible, deploy hosting và viết báo cáo. |
| Nguyễn Thành Vinh | Chuẩn bị dữ liệu sản phẩm, hỗ trợ tạo danh mục sản phẩm, kiểm tra giao diện người dùng và chụp ảnh minh họa các chức năng chính. |
| Nguyễn Văn Phương | Hỗ trợ cấu hình WooCommerce, kiểm tra giỏ hàng, thanh toán, đơn hàng và hỗ trợ hoàn thiện báo cáo, README. |

## 5. Công nghệ sử dụng

- WordPress
- WooCommerce
- PHP
- MySQL/MariaDB
- HTML, CSS
- Elementor
- Classic Editor
- Theme StoreCommerce
- Child theme Storekeeper
- Plugin tùy biến Lzon Project Tools
- XAMPP
- phpMyAdmin
- Hosting InfinityFree

## 6. Chức năng chính của hệ thống

### 6.1. Chức năng người dùng

- Xem trang chủ website.
- Xem danh sách sản phẩm.
- Xem sản phẩm theo danh mục.
- Xem chi tiết sản phẩm.
- Thêm sản phẩm vào giỏ hàng.
- Cập nhật số lượng sản phẩm trong giỏ hàng.
- Xóa sản phẩm khỏi giỏ hàng.
- Đặt hàng tại trang thanh toán.
- Đăng ký tài khoản.
- Đăng nhập tài khoản.
- Xem thông tin đơn hàng sau khi đặt.

### 6.2. Chức năng quản trị

- Quản lý sản phẩm WooCommerce.
- Quản lý danh mục sản phẩm.
- Quản lý đơn hàng.
- Quản lý tài khoản người dùng.
- Nhập sản phẩm từ file CSV mở được bằng Excel.
- Xuất danh sách sản phẩm ra file CSV mở được bằng Excel.
- Cấu hình phương thức thanh toán COD, chuyển khoản, VNPay sandbox và QR Demo.

### 6.3. Chức năng tự lập trình thêm

Plugin tự viết:

```text
wp-content/plugins/lzon-project-tools/lzon-project-tools.php
```

Chức năng plugin:

- Xuất danh sách sản phẩm ra file CSV UTF-8 BOM, mở được bằng Microsoft Excel.
- Nhập sản phẩm từ file CSV.
- Cập nhật sản phẩm cũ nếu trùng SKU.
- Tạo sản phẩm mới nếu SKU chưa tồn tại.
- Tích hợp cổng thanh toán VNPay sandbox.
- Tạo cổng thanh toán QR Demo để mô phỏng thanh toán online.

Cột dữ liệu file nhập/xuất:

```text
sku,name,regular_price,sale_price,stock_quantity,categories,short_description,description,image_url
```

## 7. Cấu trúc thư mục quan trọng

```text
wp-content/
  themes/
    storekeeper/
      functions.php
      style.css
      page-cart.php
      page-checkout.php
      woocommerce/
        cart/cart.php
        checkout/review-order.php

  plugins/
    lzon-project-tools/
      lzon-project-tools.php
      README.md

  mu-plugins/
    admin-cleanup.php

.htaccess-hosting.txt
README.md
```

## 8. Hướng dẫn cài đặt

### 8.1. Yêu cầu môi trường

- Cài đặt XAMPP.
- Bật Apache và MySQL trong XAMPP.
- Có trình duyệt web.
- Có file source code WordPress của project.
- Có file database `.sql` của project.

### 8.2. Cài source code

Copy source code vào thư mục:

```text
C:\xampp\htdocs\LAI_NAM_SON
```

### 8.3. Tạo và import database

Mở phpMyAdmin:

```text
http://localhost/phpmyadmin/
```

Tạo database:

```text
website_ban_mo_hinh
```

Import file database `.sql` của project vào database vừa tạo.

### 8.4. Cấu hình wp-config.php

Kiểm tra file:

```text
C:\xampp\htdocs\LAI_NAM_SON\wp-config.php
```

Cấu hình database local:

```php
define('DB_NAME', 'website_ban_mo_hinh');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
```

### 8.5. Cấu hình đường dẫn local

Nếu import database từ máy khác, chạy SQL:

```sql
UPDATE wp_options
SET option_value = 'http://localhost/LAI_NAM_SON'
WHERE option_name IN ('siteurl', 'home');
```

## 9. Hướng dẫn chạy project

### 9.1. Chạy trên localhost

1. Mở XAMPP.
2. Bật Apache.
3. Bật MySQL.
4. Mở website:

```text
http://localhost/LAI_NAM_SON/
```

5. Mở trang quản trị:

```text
http://localhost/LAI_NAM_SON/wp-admin/
```

### 9.2. Các plugin cần kích hoạt

Trong WordPress Admin, vào:

```text
Plugins > Installed Plugins
```

Kích hoạt các plugin:

- WooCommerce
- Elementor
- Classic Editor
- Lzon Project Tools

### 9.3. Cấu hình trang WooCommerce

Nếu trang giỏ hàng, thanh toán hoặc tài khoản hiển thị sai nội dung, chạy SQL:

```sql
UPDATE wp_posts
SET post_content = '[woocommerce_cart]',
    post_title = 'Giỏ hàng'
WHERE post_name IN ('cart', 'gio-hang')
  AND post_type = 'page';

UPDATE wp_posts
SET post_content = '[woocommerce_checkout]',
    post_title = 'Thanh toán'
WHERE post_name IN ('checkout', 'thanh-toan')
  AND post_type = 'page';

UPDATE wp_posts
SET post_content = '[woocommerce_my_account]',
    post_title = 'Tài khoản'
WHERE post_name IN ('my-account', 'tai-khoan')
  AND post_type = 'page';
```

### 9.4. Tắt chế độ Coming Soon của WooCommerce

Nếu website hiển thị thông báo "Những điều tuyệt vời đang ở phía trước", chạy SQL:

```sql
UPDATE wp_options
SET option_value = 'no'
WHERE option_name IN (
  'woocommerce_coming_soon',
  'woocommerce_feature_site_visibility_badge_enabled'
);
```

### 9.5. Bật đăng ký tài khoản và hiển thị ô mật khẩu

```sql
UPDATE wp_options SET option_value = '1'
WHERE option_name = 'users_can_register';

UPDATE wp_options SET option_value = 'yes'
WHERE option_name = 'woocommerce_enable_myaccount_registration';

UPDATE wp_options SET option_value = 'no'
WHERE option_name = 'woocommerce_registration_generate_password';

UPDATE wp_options SET option_value = 'yes'
WHERE option_name = 'woocommerce_registration_generate_username';
```

## 10. Hướng dẫn sử dụng chức năng chính

### 10.1. Thêm sản phẩm vào giỏ hàng

1. Vào trang cửa hàng hoặc trang danh mục sản phẩm.
2. Chọn sản phẩm.
3. Bấm thêm vào giỏ hàng.
4. Vào trang giỏ hàng để kiểm tra sản phẩm.

### 10.2. Đặt hàng

1. Vào giỏ hàng.
2. Bấm tiến hành thanh toán.
3. Nhập thông tin thanh toán.
4. Chọn phương thức thanh toán.
5. Bấm đặt hàng.

### 10.3. Nhập/xuất Excel-compatible

Đường dẫn:

```text
WooCommerce > Lzon Excel
```

Chức năng:

- Bấm Download Excel CSV để xuất sản phẩm.
- Chọn file CSV và bấm Import products để nhập sản phẩm.

### 10.4. Thanh toán VNPay sandbox

Đường dẫn cấu hình:

```text
WooCommerce > Settings > Payments > VNPay Sandbox
```

Cần điền:

- `vnp_TmnCode`
- `vnp_HashSecret`
- `Payment URL`: `https://sandbox.vnpayment.vn/paymentv2/vpcpay.html`

### 10.5. Thanh toán QR Demo

Đường dẫn cấu hình:

```text
WooCommerce > Settings > Payments > QR Demo
```

QR Demo chỉ dùng để mô phỏng thanh toán, không trừ tiền thật.

## 11. Tài khoản demo

### 11.1. Tài khoản quản trị

```text
URL admin: http://localhost/LAI_NAM_SON/wp-admin/
Username: dien_tai_khoan_admin
Password: dien_mat_khau_admin
```

### 11.2. Tài khoản khách hàng

```text
URL tài khoản: http://localhost/LAI_NAM_SON/my-account/
Username/Email: dien_email_khach_hang
Password: dien_mat_khau_khach_hang
```

> Lưu ý: Không nên đẩy mật khẩu thật lên GitHub public. Nếu nộp bài qua file thì có thể điền tài khoản demo riêng.

## 12. Hình ảnh minh họa hệ thống

 ảnh chụp màn hình vào thư mục:

```text
docs/images/
```

Danh sách ảnh :

| STT | Màn hình | File minh họa |
| --- | --- | --- |
| 1 | Trang chủ | `docs/images/trang-chu.png` |
| 2 | Danh sách sản phẩm | `docs/images/san-pham.png` |
| 3 | Chi tiết sản phẩm | `docs/images/chi-tiet-san-pham.png` |
| 4 | Giỏ hàng | `docs/images/gio-hang.png` |
| 5 | Thanh toán | `docs/images/thanh-toan.png` |
| 6 | Đăng nhập / đăng ký | `docs/images/tai-khoan.png` |
| 7 | Lzon Excel import/export | `docs/images/lzon-excel.png` |
| 8 | VNPay sandbox | `docs/images/vnpay-sandbox.png` |
| 9 | QR Demo | `docs/images/qr-demo.png` |


## 13. Link video demo

```text gg drive
https://drive.google.com/file/d/1raN5D5AR5FcjEM_bicmWHaALhOLy7TOV/view?usp=sharing
```

```text youtube
https://www.youtube.com/watch?v=atUTlC01_Tc
```

## 14. Link online đã deploy

Website đã deploy:

```text
http://laison.fwh.is/
```

Trang quản trị hosting:

```text
http://laison.fwh.is/wp-admin/
```

## 15.  deploy hosting

### 15.2. Upload plugin tự viết

Đảm bảo hosting có file:

```text
htdocs/wp-content/plugins/lzon-project-tools/lzon-project-tools.php
```

Sau đó vào WordPress Admin trên hosting và kích hoạt plugin:

```text
Plugins > Lzon Project Tools > Activate
```

### 15.3. Cấu hình .htaccess hosting

Dùng nội dung trong file:

```text
.htaccess-hosting.txt
```

Nội dung:

```apache
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
```

### 15.4. Cấu hình URL hosting

Trong phpMyAdmin hosting, chạy:

```sql
UPDATE wp_options
SET option_value = 'http://laison.fwh.is'
WHERE option_name IN ('siteurl', 'home');
```

## 16. Các mục đáp ứng đề cương

| Yêu cầu trong đề cương | Trạng thái |
| --- | --- |
| Cài đặt và chạy được mã nguồn mở | Đã đáp ứng |
| Cấu hình website bán hàng | Đã đáp ứng |
| Tùy biến giao diện/skin | Đã đáp ứng |
| Lập trình chức năng mới | Đã đáp ứng |
| Nhập/xuất file Excel | Đã đáp ứng bằng CSV mở được trong Excel |
| Thanh toán online | Đã đáp ứng ở mức VNPay sandbox và QR Demo |
| Đưa lên hosting thật | Đã đáp ứng nếu website chạy ổn định trên InfinityFree |
| Báo cáo đồ án | Đã có, cần cập nhật thêm ảnh và mô tả chức năng mới |


