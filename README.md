# Basic Course Website (HTML/CSS/JS + PHP + MySQL)

A minimal example site that lists courses and lets you add new ones. Built for local use with XAMPP (Apache + MySQL + PHP).

## Structure

```
index.html
assets/
  styles.css
  script.js
php/
  config.php
  courses.php      # GET list of courses (JSON)
  add_course.php   # POST add a course (JSON)
schema.sql         # Database and table
```

## Prerequisites
- XAMPP installed (Apache and MySQL running)
- PHP enabled in Apache

## Setup
1. Import database and table
   - Open `http://localhost/phpmyadmin`
   - Create a database `courses_db` (or use any name you like)
   - Import `schema.sql`
2. Configure credentials
   - Edit `php/config.php` if your MySQL user/password/host/port differ
   - Default user is `root` with an empty password on XAMPP
3. Place the project in your XAMPP web root
   - On Windows: copy this folder to `C:\xampp\htdocs\course-site` (for example)
   - Access it at `http://localhost/course-site/`

## Endpoints
- `GET php/courses.php` → returns courses as JSON
- `POST php/add_course.php` with JSON body `{ "title": "...", "description": "..." }`
- `POST php/signup.php` → email+password signup
- `POST php/login.php` → email+password login
- `POST php/logout.php` → logout (clears session)
- `GET php/me.php` → current session user
- `GET php/health.php` → DB health check
- `POST php/otp_request.php` → request OTP `{ email, purpose }`
- `POST php/otp_verify.php` → verify OTP `{ email, code, purpose }`
- `GET php/test_mail.php?to=you@example.com` → test mail/log delivery

## Troubleshooting
- If you see an error like "Database error":
  - Ensure MySQL is running
  - Confirm the database and table exist (run `schema.sql`)
  - Check `php/config.php` DSN, user, and password
- If PHP returns a 500 error, check `xampp\apache\logs\error.log` for details

## Notes
- Uses PDO with prepared statements
- CORS headers are enabled for local usage; remove in production
- This is intentionally simple and not meant for production use

## Email/OTP (XAMPP friendly)
- By default, OTP emails are logged to `mail.log` in the project root. Check this file to see the code.
- To actually send emails via PHP `mail()` (if configured), set environment variable `MAIL_MODE=mail` and configure sendmail/SMTP in `C:\xampp\sendmail\sendmail.ini` and `php.ini`.
- Request an OTP by POSTing to `php/otp_request.php` with body `{ "email": "you@example.com", "purpose": "signup" }`, then verify via `php/otp_verify.php` with `{ "email": "you@example.com", "code": "123456", "purpose": "signup" }`.

## PHPMailer (SMTP)
- Install dependencies with Composer (from project root):
  - `composer install`
- Set environment variables (or adapt in code):
  - `MAIL_MODE=smtp`
  - `SMTP_HOST=your-smtp-host`
  - `SMTP_PORT=587` (or 465 for SSL)
  - `SMTP_USER=your-smtp-username`
  - `SMTP_PASS=your-smtp-password`
  - `SMTP_FROM=no-reply@yourdomain.com`
  - `SMTP_FROM_NAME=LearnLite`
  - `SMTP_SECURE=tls` (or `ssl`)
- Test delivery:
  - Open `http://localhost/course-site/php/test_mail.php?to=you@example.com`
  - Response `{"success":true}` indicates mail accepted by the SMTP client.

## Signup/Login emails
- After a successful signup (`php/signup.php`), the app sends a welcome email using `send_mail_or_log(...)`.
- After a successful login (`php/login.php`), the app sends a login notification.
- Delivery modes:
  - `MAIL_MODE=log` (default): writes to `mail.log` for quick testing on XAMPP.
  - `MAIL_MODE=mail`: uses PHP `mail()` (requires sendmail/SMTP configured in XAMPP).
  - `MAIL_MODE=smtp`: uses PHPMailer with your SMTP settings.
