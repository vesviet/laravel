# Hướng dẫn Deploy Laravel 13 Monolith bằng Docker Compose

Bản hướng dẫn này thay thế cho bản deploy cPanel cũ, hướng tới triển khai Production tiêu chuẩn sử dụng Docker Compose trên các máy chủ VPS (Ubuntu/Rocky Linux).

## Yêu cầu hệ thống
- Server cài đặt Docker Engine và Docker Compose (v2.x).
- Port `80` (HTTP) và `443` (HTTPS) trống trên Host.
- (Tùy chọn) Traefik hoặc Nginx Proxy Manager để quản lý SSL/TLS certificates nếu chạy nhiều dự án.

## Bước 1: Chuẩn bị Source Code
Clone source code về server:
```bash
git clone <repository_url> /var/www/laravel
cd /var/www/laravel
```

## Bước 2: Cấu hình Môi trường
Copy file environment mẫu:
```bash
cp .env.example .env
```

Cập nhật các biến môi trường cho Docker (bắt buộc):
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret_password_here

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## Bước 3: Khởi động hệ thống
Khởi tạo và chạy ngầm các containers:
```bash
docker compose up -d --build
```
Lệnh này sẽ tải các image (PHP 8.4, Nginx, MySQL 8, Redis) và mount code trực tiếp vào container.

## Bước 4: Chạy Migrations và Tối ưu (Duy nhất lần đầu)
Truy cập vào container `app` (chạy PHP):
```bash
docker compose exec app composer install --optimize-autoloader --no-dev
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan storage:link
docker compose exec app php artisan optimize
```

> **Lưu ý chống Oversell**: Hệ thống sử dụng khóa `DB::transaction()` và `lockForUpdate()`. Redis không tham gia vào việc lock số lượng tồn kho để đảm bảo ACID (Atomicity) 100% trong MySQL.

## Bước 5 (Tùy chọn): Khởi động Queue Worker
Nếu bạn sử dụng email hoặc xử lý ngầm (VD: gửi Email xác nhận), hãy chạy worker trong background:
```bash
docker compose exec -d app php artisan queue:work --tries=3
```

## Troubleshooting
- **Lỗi 502 Bad Gateway**: Kiểm tra log của PHP-FPM (`docker compose logs app`).
- **Lỗi không kết nối DB**: Xác nhận `DB_HOST=db` thay vì `127.0.0.1` trong file `.env`.
- **Cập nhật code mới**: 
```bash
git pull origin main
docker compose exec app composer install --no-dev
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize
```
