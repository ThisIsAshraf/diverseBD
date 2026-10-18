# ThreadWear — Core PHP + MySQL clothing store

A modular starter e-commerce application with storefront, product/category browsing, cart, cash-on-delivery checkout, generated order numbers, inventory decrement, coupon discounts, contact form, email notifications via Gmail SMTP (PHPMailer), and admin management for products, orders, coupons, contacts, and basic sales reporting.

## Requirements
- PHP 8.1+ (PDO MySQL, mbstring, openssl)
- MySQL 8+ or MariaDB 10.5+
- Composer
- Apache/Nginx (XAMPP works locally)

## Install
1. Copy the `threadwear` folder into your web root (for XAMPP: `htdocs/threadwear`).
2. Create a MySQL database, e.g. `threadwear`.
3. Import `sql/schema.sql` using phpMyAdmin or the MySQL CLI.
4. Copy `.env.example` to `.env` and update DB settings, `APP_URL`, and Gmail SMTP settings.
5. In the project folder run `composer install`.
6. Generate an admin password hash: `php -r "echo password_hash('ChangeThisPassword!', PASSWORD_DEFAULT), PHP_EOL;"`.
7. Insert an admin account (replace email/hash):
   `INSERT INTO admins (name,email,password_hash) VALUES ('Store Admin','admin@example.com','PASTE_HASH_HERE');`
8. Open `http://localhost/threadwear/` for the store and `http://localhost/threadwear/admin/login.php` for admin.

## Gmail SMTP
Use your Gmail address as `MAIL_USERNAME`. Enable 2-Step Verification on that Google account and create an **App Password**. Put that App Password in `MAIL_PASSWORD` (not your normal Gmail password). Gmail SMTP uses `smtp.gmail.com`, port `587`, encryption `tls`.

## Important setup notes
- For production, point the web server document root to `public/` and configure `/admin` routing appropriately; this starter keeps admin at `/admin` for ease of local setup.
- Set `APP_DEBUG=false`, use HTTPS, use a strong unique admin password, and keep `.env` out of version control.
- Configure backups, transactional email monitoring, delivery/return policy, privacy policy, and legal/tax settings before launch.
- This is a functional starter/MVP, not a fully audited production commerce platform. Have it security-reviewed before taking live orders. Add rate limiting, audit logs, admin roles, image upload validation, shipping integrations, and automated tests before scaling.
- Product image fields accept URLs for this first version; upload management is a recommended next enhancement.


## Store branding

After importing the schema and logging into `/admin`, open **Store branding** to change the store name, upload or remove the logo, and edit the announcement bar. The `site_settings` table is used for these values. For an existing database, the settings page creates the required values automatically when you save. Uploads are stored under `storage/uploads`; make sure PHP can write to that folder on your host.
