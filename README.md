# Adept Play Tournament App

## Requirements
- PHP 8.1+
- MySQL 5.7+/8.x or MariaDB
- Apache/Nginx with PHP
- Internet connection if using Tailwind CDN / Font Awesome CDN

## Install
1. Create/upload this folder to your PHP hosting or XAMPP `htdocs`.
2. If necessary, edit `common/config.php` DB values, or set DB_HOST/DB_NAME/DB_USER/DB_PASS environment variables.
3. Open `install.php` once.
4. Default admin: `admin` / `admin123`.
5. Immediately change the admin password in the database or extend the admin settings flow.
6. Delete `install.php` after successful installation.
7. If updating an existing installation, use `update_database.php` once, then delete it.

## Important
- Payment verification is manual by design. Admin must verify the UPI transaction ID in the bank/UPI app before approving.
- Uploaded QR images are stored under `uploads/`.
- For production, force HTTPS, use a strong DB password, change the default admin credential, and keep PHP/MySQL patched.
