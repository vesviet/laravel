# cPanel Deployment Guide — Laravel E-Commerce

Hướng dẫn này dùng cho hosting **cPanel Shared/VPS** với PHP 8.4 và MySQL.

---

## 📋 Yêu cầu hệ thống
- cPanel với **PHP 8.4** (cài Extension: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `intl`)
- MySQL 5.7+ / MariaDB 10.3+
- Git Terminal trong cPanel (hoặc SSH access)

---

## 🚀 Bước 1: Clone repo vào cPanel

Trên cPanel → **Terminal** (hoặc SSH):

```bash
# Đặt code vào home directory (KHÔNG phải public_html)
cd ~
git clone https://github.com/vesviet/laravel ecommerce
```

Cấu trúc thư mục sẽ là:
```
/home/username/
├── ecommerce/          ← code Laravel (toàn bộ)
│   ├── public/         ← webroot thực sự
│   ├── app/
│   ├── .htaccess       ← redirect / → public/
│   └── ...
└── public_html/        ← hoặc domain addon folder
```

---

## 🔗 Bước 2: Trỏ Document Root về `public/`

### Cách A: Domain chính `demo.tanhdev.com`
Trong cPanel → **Domains** → Chỉnh sửa domain → Set **Document Root** thành:
```
/home/username/ecommerce/public
```

### Cách B: Addon Domain
Khi tạo Addon Domain, set Document Root thành:
```
/home/username/ecommerce/public
```

> ⚠️ Nếu không thể đổi Document Root, file `.htaccess` tại root (`~/ecommerce/.htaccess`) đã được cấu hình để redirect vào `public/`.

---

## ⚙️ Bước 3: Tạo Database MySQL

Trong cPanel → **MySQL Databases**:
1. Tạo database: `username_ecommerce`
2. Tạo user: `username_dbuser` với password mạnh
3. **Add User to Database** với quyền **ALL PRIVILEGES**

---

## 🔧 Bước 4: Cấu hình file `.env`

```bash
cd ~/ecommerce
cp .env.example .env
nano .env   # hoặc vi .env
```

Điền các thông tin sau:

```env
APP_NAME="E-Commerce Store"
APP_ENV=production
APP_KEY=                        # Sẽ generate ở bước sau
APP_DEBUG=false
APP_URL=https://demo.tanhdev.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_ecommerce
DB_USERNAME=username_dbuser
DB_PASSWORD=your_db_password

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

MAIL_MAILER=sendmail
MAIL_FROM_ADDRESS="noreply@demo.tanhdev.com"
MAIL_FROM_NAME="E-Commerce Store"
```

---

## 📦 Bước 5: Cài Dependencies

```bash
cd ~/ecommerce
php8.4 /usr/local/bin/composer install --optimize-autoloader --no-dev
```

> Nếu cPanel không có `composer`, tải về:
> ```bash
> curl -sS https://getcomposer.org/installer | php8.4
> php8.4 composer.phar install --optimize-autoloader --no-dev
> ```

---

## 🗄️ Bước 6: Migrate Database & Seed

```bash
cd ~/ecommerce

# Generate APP_KEY
php8.4 artisan key:generate

# Chạy migrations
php8.4 artisan migrate --force

# Seed dữ liệu mẫu (Admin + Products)
php8.4 artisan db:seed --force

# Publish Filament assets (CSS/JS Admin Panel)
php8.4 artisan filament:assets

# Tối ưu hóa
php8.4 artisan optimize
php8.4 artisan storage:link
```

---

## 🔐 Bước 7: Phân quyền thư mục

```bash
cd ~/ecommerce
chmod -R 775 storage bootstrap/cache
chown -R nobody:nobody storage bootstrap/cache   # tuỳ theo user của PHP-FPM
```

---

## ✅ Kiểm tra sau deploy

| URL | Kết quả mong đợi |
|-----|-----------------|
| `https://demo.tanhdev.com/products` | Danh sách sản phẩm |
| `https://demo.tanhdev.com/admin` | Redirect → `/admin/login` |
| `https://demo.tanhdev.com/admin/login` | Form đăng nhập Filament |

**Đăng nhập Admin:**
- Email: `admin@example.com`
- Password: `password`

---

## 🔄 Cập nhật code sau này

```bash
cd ~/ecommerce
git pull origin main
php8.4 artisan migrate --force
php8.4 artisan optimize:clear
php8.4 artisan optimize
```

---

## 🛠️ Troubleshooting

| Lỗi | Nguyên nhân | Cách fix |
|-----|-------------|----------|
| `500 Internal Server Error` | `.env` sai hoặc thiếu `APP_KEY` | Chạy `php8.4 artisan key:generate` |
| `SQLSTATE[HY000]` | DB credentials sai | Kiểm tra lại `.env` DB section |
| Trang trắng (no CSS) | Thiếu `public/build` | Đã có trong repo, `git pull` lại |
| Admin bị vỡ giao diện | Filament assets chưa publish | Chạy `php8.4 artisan filament:assets` |
| `permission denied` trên `storage/` | Quyền sai | `chmod -R 775 storage` |
