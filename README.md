# Aplikasi UMKM - Sistem Manajemen UMKM

Aplikasi web untuk mengelola Usaha Mikro, Kecil, dan Menengah (UMKM) yang dibangun dengan Laravel 12.

## 🚀 Fitur Utama

### 📊 Dashboard

-   Statistik overview (total pengguna, produk, pesanan, pendapatan)
-   Pesanan terbaru
-   Produk unggulan
-   Alert stok menipis
-   Statistik cepat

### 🏷️ Manajemen Kategori

-   CRUD kategori produk
-   Upload gambar kategori
-   Status aktif/nonaktif
-   Relasi dengan produk

### 📦 Manajemen Produk

-   CRUD produk lengkap
-   Upload gambar produk
-   Manajemen stok
-   Produk unggulan
-   Kategori produk
-   Riwayat pesanan per produk

### 🛒 Manajemen Pesanan

-   Buat pesanan dengan multiple produk
-   Status pesanan (pending, processing, shipped, delivered, cancelled)
-   Informasi pelanggan lengkap
-   Kalkulasi otomatis total pesanan
-   Update stok otomatis

### 👤 Sistem Autentikasi

-   Login dan register
-   Middleware authentication
-   Session management

## 🛠️ Teknologi yang Digunakan

-   **Backend**: Laravel 12
-   **Database**: SQLite (default), MySQL/PostgreSQL
-   **Frontend**: Bootstrap 5, Font Awesome
-   **CSS**: Custom CSS dengan gradient design
-   **JavaScript**: Vanilla JS untuk interaksi dinamis

## 📋 Persyaratan Sistem

-   PHP >= 8.2
-   Composer
-   Web Server (Apache/Nginx) atau PHP Built-in Server
-   Database (SQLite/MySQL/PostgreSQL)

## 🚀 Deploy to Cloudpanel

### 1. Download source code

Download source code ini dan upload file `.zip` nya ke site Cloudpanel:

### 2. SSH ke server

Buka cmd atau powershell dan jalankan perintah berikut:

```bash
ssh user-site@ip_server
```

### 3. Install Dependencies

```bash
composer install
```

### 4. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Jalankan Migration dan Seeder

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 6. Install Node js

```bash
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.2/install.sh | bash
source ~/.bashrc
nvm install 20
nvm use 20
node -v
```

### 7. Install packages dan build

```bash
npm install
npm run build
```

## 🚀 Running on localhost

### 1. Clone Repository

```bash
git clone <repository-url>
cd umkm-app
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<nama_database>
DB_USERNAME=<user_database>
DB_PASSWORD=<password_database>
```

### 5. Jalankan Migration dan Seeder

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 6. Jalankan Migration dan Seeder

buka terminal di vscode dan jalankan perintah:

```bash
npm install
npm run dev
```

### 7. Jalankan Aplikasi

buka terminal yang berbeda dan jalankan:

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 👤 Akun Default

Setelah menjalankan seeder, Anda dapat login dengan akun default:

-   **Email**: admin@umkm.com
-   **Password**: password

## 📁 Struktur Database

### Tabel Users

-   id, name, email, password, email_verified_at, remember_token, timestamps

### Tabel Categories

-   id, name, slug, description, image, is_active, timestamps

### Tabel Products

-   id, category_id, name, slug, description, price, stock, image, is_active, is_featured, timestamps

### Tabel Orders

-   id, user_id, order_number, total_amount, status, customer_name, customer_email, customer_phone, shipping_address, notes, timestamps

### Tabel Order Items

-   id, order_id, product_id, quantity, price, subtotal, timestamps

## 🎨 Fitur UI/UX

### Design System

-   Gradient color scheme (purple-blue)
-   Modern card-based layout
-   Responsive design
-   Interactive hover effects
-   Clean typography

### Komponen UI

-   Sidebar navigation
-   Statistics cards
-   Data tables
-   Form components
-   Modal dialogs
-   Alert notifications

## 🔧 Konfigurasi Tambahan

### Upload Gambar

Aplikasi mendukung upload gambar untuk kategori dan produk. Pastikan direktori berikut memiliki permission write:

```bash
public/images/categories/
public/images/products/
```

### File Storage

Untuk production, disarankan menggunakan cloud storage seperti AWS S3 atau Google Cloud Storage.

## 📱 Responsive Design

Aplikasi responsive dan dapat diakses dari berbagai device:

-   Desktop (>= 1200px)
-   Tablet (768px - 1199px)
-   Mobile (< 768px)

## 🔒 Keamanan

-   CSRF protection
-   SQL injection prevention
-   XSS protection
-   Input validation
-   File upload validation
-   Authentication middleware

## 🧪 Testing

Untuk menjalankan test:

```bash
php artisan test
```

## 📈 Monitoring

Aplikasi menyediakan dashboard dengan berbagai metrik:

-   Total pendapatan
-   Jumlah pesanan
-   Produk terlaris
-   Stok monitoring
-   Status pesanan

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

## 📞 Support

Jika ada pertanyaan atau masalah, silakan buat issue di repository ini.

## 🔄 Changelog

### v1.0.0

-   Initial release
-   CRUD untuk kategori, produk, dan pesanan
-   Dashboard dengan statistik
-   Sistem autentikasi
-   Upload gambar
-   Responsive design

---

**Dibuat dengan ❤️ menggunakan Laravel**
