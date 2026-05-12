# Lzon Project Tools

Plugin phuc vu do an Shop Lzon.

## 1. Nhap / xuat file Excel

Duong dan admin:

`WooCommerce > Lzon Excel`

Chuc nang:

- Xuat danh sach san pham ra file CSV co BOM UTF-8, mo duoc bang Microsoft Excel.
- Nhap lai file CSV de tao/cap nhat san pham.
- Neu dong CSV co SKU trung voi san pham cu, plugin cap nhat san pham do.
- Neu SKU chua ton tai, plugin tao san pham moi.

Cot du lieu:

`sku,name,regular_price,sale_price,stock_quantity,categories,short_description,description,image_url`

## 2. Thanh toan VNPay sandbox

Duong dan cau hinh:

`WooCommerce > Settings > Payments > VNPay Sandbox`

Can dien:

- `vnp_TmnCode`
- `vnp_HashSecret`
- `Payment URL`: mac dinh la `https://sandbox.vnpayment.vn/paymentv2/vpcpay.html`

Sau khi kich hoat, khach hang co the chon `Thanh toan VNPay` tai trang checkout. Plugin tao URL thanh toan, chuyen sang VNPay sandbox, nhan ket qua tra ve va cap nhat trang thai don hang.
