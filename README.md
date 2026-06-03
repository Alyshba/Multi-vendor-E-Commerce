# Simple Multi-Vendor Admin Panel

This is a simple but complete Laravel Blade admin panel for a multi-vendor e-commerce system. It is made for a XAMPP setup and keeps the project easy to understand while still covering the important requirements: admin login, CRUD, product approvals, orders, reports, filters, pagination, JWT, and API documentation.

## Main Features

- Admin login.
- Dashboard with totals.
- Vendor CRUD.
- Product CRUD and approval/rejection.
- Order records and order items.
- PDF report.
- JWT protected API routes.
- Postman collection included in `postman/MultiVendorAdmin.postman_collection.json`.

## XAMPP Setup

1. Open XAMPP Control Panel.
2. Start `Apache` and `MySQL`.
3. Open `http://localhost/phpmyadmin`.
4. Create a database named `multi_vendor_admin`.
5. Open this project folder in terminal.
6. Install dependencies:

```bash
composer install
```

7. Create the `.env` file:

```bash
copy .env.example .env
```

For PowerShell:

```powershell
Copy-Item .env.example .env
```

8. Generate Laravel keys:

```bash
php artisan key:generate
php artisan jwt:secret
```

9. Create tables and sample data:

```bash
php artisan migrate --seed
```

10. Start the project:

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000/login
```

## XAMPP Database Settings

The `.env.example` file already uses normal XAMPP settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multi_vendor_admin
DB_USERNAME=root
DB_PASSWORD=
```

Laravel still uses `DB_CONNECTION=mysql` because XAMPP's database works through the MySQL-compatible driver.

## Default Admin Login

```text
Email: admin@example.com
Password: password
```

## If PHP Is Not Recognized

Use XAMPP's PHP directly:

```bash
C:\xampp\php\php.exe artisan serve
```
