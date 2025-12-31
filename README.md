<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# LARAVEL APPLICATION DEPLOYMENT GUIDE

Dokumen ini berisi panduan lengkap untuk melakukan setup dan deployment
aplikasi Laravel pada lingkungan local maupun server (VPS/Production).

Panduan ini ditujukan untuk engineer/developer yang memiliki pemahaman dasar
tentang PHP, Composer, database, dan web server.

---

1. REQUIREMENT SISTEM

---

Software yang dibutuhkan:

-   PHP versi 8.2 atau lebih baru
-   Composer
-   MySQL / MariaDB
-   Web Server (Apache atau Nginx)
-   Git

PHP Extension yang harus aktif:

-   OpenSSL
-   PDO
-   Mbstring
-   Tokenizer
-   XML
-   Ctype
-   Fileinfo
-   Curl

---

2. CLONE SOURCE CODE

---

Clone repository ke server atau local machine:

git clone <repository-url>
cd <project-folder>

---

3. INSTALL DEPENDENCY

---

Install dependency menggunakan Composer:

composer install

Untuk production environment:

composer install --no-dev --optimize-autoloader

---

4. KONFIGURASI ENVIRONMENT

---

Salin file environment example:

cp .env.example .env

Edit file .env sesuai konfigurasi server:

APP_NAME=LaravelApp
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=root
DB_PASSWORD=

---

5. GENERATE APPLICATION KEY

---

Generate application key Laravel:

php artisan key:generate

---

6. SETUP DATABASE

---

Pastikan database sudah dibuat terlebih dahulu.

Jalankan migration:

php artisan migrate

Jika menggunakan seeder:

php artisan db:seed

Atau sekaligus:

php artisan migrate --seed

---

7. PERMISSION FOLDER (LINUX)

---

Pastikan folder storage dan cache dapat ditulis:

chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

(Sesuaikan user web server jika bukan www-data)

---

8. MENJALANKAN APLIKASI (LOCAL)

---

Untuk menjalankan aplikasi secara lokal:

php artisan serve

Akses aplikasi melalui browser:

http://127.0.0.1:8000

---

9. KONFIGURASI WEB SERVER

---

APACHE:
Pastikan DocumentRoot mengarah ke folder public:

DocumentRoot /var/www/laravel-app/public

Aktifkan mod_rewrite:

a2enmod rewrite

NGINX (CONTOH):

root /var/www/laravel-app/public;
index index.php index.html;

location / {
try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
fastcgi_index index.php;
include fastcgi_params;
fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}

---

10. CACHE CONFIGURATION (PRODUCTION)

---

Untuk production, jalankan perintah berikut:

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

---

11. STORAGE LINK (JIKA DIGUNAKAN)

---

Jika aplikasi menggunakan storage file:

php artisan storage:link

---

12. TROUBLESHOOTING

---

ERROR 500 / HALAMAN KOSONG:

-   Periksa file storage/logs/laravel.log
-   Pastikan APP_KEY sudah di-generate
-   Periksa permission folder

DATABASE ERROR:

-   Periksa konfigurasi .env
-   Pastikan service database berjalan

---

13. STRUKTUR DIREKTORI PENTING

---

app/
routes/
resources/
database/
public/
storage/

---

14. CATATAN PENTING

---

-   Jangan commit file .env ke repository
-   Gunakan APP_DEBUG=false di production
-   Lakukan backup database sebelum deployment ulang

---

15. LISENSI

---

Sesuaikan lisensi dengan kebutuhan proyek.

END OF FILE
