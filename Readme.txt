
---

## 🚀 Yêu cầu hệ thống

- PHP >= 8.2
- Composer
- Node.js >= 16
- MySQL hoặc PostgreSQL
- Git (tuỳ chọn)

---

## ⚙️ Cài đặt backend (Laravel)

```bash
cd BE-admin-laravel

# Cài package
composer install

# Sao chép file .env và chỉnh sửa theo thông tin DB
cp .env.example .env

# Tạo key và migrate DB
php artisan key:generate
php artisan migrate

# Cài Laravel Sanctum
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

# Khởi chạy backend
php artisan serve

--------------------------------------------------------------------------------

cd DATN-FE

# Cài package
npm install

# Khởi chạy React
npm run dev  # hoặc npm start (tùy create-react-app hoặc Vite)
