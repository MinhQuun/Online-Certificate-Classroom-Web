<div align="center">

# Online Certificate Classroom

Laravel web platform + REST API (shared with the Flutter app [Online-Certificate-Classroom-Mobile](https://github.com/MinhQuun/Online-Certificate-Classroom-Mobile)) for managing certificate-oriented classes.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![Sanctum](https://img.shields.io/badge/Auth-Sanctum-darkgreen)
![VNPay](https://img.shields.io/badge/Payments-VNPay-orange)

</div>

---

## Table of Contents

1. [Highlights](#highlights)
2. [Architecture & Tech Stack](#architecture--tech-stack)
3. [Repository Guide](#repository-guide)
4. [Prerequisites](#prerequisites)
5. [Setup](#setup)
6. [Environment Checklist](#environment-checklist)
7. [Database Policy](#database-policy)
8. [Daily Commands](#daily-commands)
9. [Testing & QA](#testing--qa)
10. [Mobile Integration](#mobile-integration)
11. [Deployment Notes](#deployment-notes)
12. [Roadmap & Contributions](#roadmap--contributions)

---

## Highlights

### Student
- Browse courses/combos, preview free lessons, favorite, and read reviews.
- Persistent cart (courses + combos) synced across devices, VNPay checkout, activation-code redemption.
- Lesson progress tracking, mini-tests (multiple choice, writing, speaking uploads), attempt history, result review.
- Lesson discussions with threaded replies, soft-delete, permissions per role.
- Profile management, password update, order history, my courses dashboard.

### Teacher
- Chapter/lesson CRUD with material uploads, ordering, and visibility controls.
- Mini-test builder, manual grading tools, result dashboards.
- Lecture progress tracker, per-student insights, discussion moderation (pin/lock/status).

### Admin
- Catalog management: categories, courses, combos, promotions.
- User management with role assignment (Spatie Permission), contact center replies.
- Order/invoice overview, VNPay transaction logs, activation code handling.

### Shared Services
- Laravel Sanctum API for both web SPA features and Flutter app.
- Google OAuth (Socialite), OTP password reset, file storage via S3-compatible disks.
- Reports/export via DOMPDF + Maatwebsite Excel.

---

## Architecture & Tech Stack

| Layer | Technology |
| --- | --- |
| Backend | Laravel 11, PHP 8.2, Sanctum, Socialite, Spatie Permission |
| Frontend | Blade + Laravel UI, Vite, Axios |
| Database | MySQL 8 (managed through `database/Online_Certificate_Classroom.sql`) |
| Storage & Media | Local / S3 (Flysystem v3) |
| Payments | VNPay (return + IPN flow) |
| Documents | DOMPDF, Maatwebsite Excel |

**Important:** Production never runs `php artisan migrate`. All schema changes must go through `database/Online_Certificate_Classroom.sql`.

---

## Repository Guide

| Path | Description |
| --- | --- |
| `routes/web.php` | Public pages, student/teacher/admin dashboards, VNPay routes |
| `routes/api.php` | `/api/v1/student/...` endpoints reused by Flutter |
| `app/Http/Controllers/Student` | Student web controllers (cart, checkout, progress, discussions, mini-tests, etc.) |
| `app/Http/Controllers/Teacher` | Teacher dashboards, lecture CRUD, grading, discussions |
| `app/Http/Controllers/Admin` | Admin panels for catalog, users, promotions, invoices |
| `app/Http/Controllers/API/Student` | REST controllers (auth, cart, checkout, profile, progress, mini-tests, etc.) |
| `app/Support/Cart` | Session + DB backed cart storage shared across web/API |
| `config/vnpay.php` | Gateway configuration driven by `.env` |
| `database/Online_Certificate_Classroom.sql` | **Single source of truth for schema + seed data** |
| `resources/views` | Blade views for public pages and role-specific dashboards |

---

## Prerequisites

- PHP 8.2 with extensions: `zip`, `fileinfo`, `mbstring`, `openssl`, `intl`, `bcmath`
- Composer 2.6+
- Node.js 18+ (npm)
- MySQL 8.0+
- Git, plus optional XAMPP/Valet
- Add `php.exe` (e.g. `C:\xampp\php`) to PATH for Composer

---

## Setup

```bash
git clone https://github.com/MinhQuun/Online-Certificate-Classroom-Web.git
cd Online-Certificate-Classroom-Web
composer install
npm install
cp .env.example .env   # or create manually
php artisan key:generate
```

1. **Configure `.env`:** database credentials, mail, VNPay, Google OAuth, AWS/S3, Sanctum domain if needed.
2. **Import database:** run the SQL script in `database/Online_Certificate_Classroom.sql`. Do **not** run migrations.
3. **Storage & assets:**
   ```bash
   php artisan storage:link
   npm run dev    # npm run build for production
   ```
4. **Serve app:**
   ```bash
   php artisan serve
   ```
   - Web UI: `http://localhost:8000`
   - API base: `http://localhost:8000/api/v1`

---

## Environment Checklist

| Feature | Required ENV keys |
| --- | --- |
| Database | `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` |
| Sanctum SPA/API | `APP_URL`, `SANCTUM_STATEFUL_DOMAINS`, `SESSION_DOMAIN` (if cross-domain) |
| VNPay | `VNP_TMN_CODE`, `VNP_HASH_SECRET`, `VNP_URL`, `VNP_RETURN_URL`, `VNP_IPN_URL`, `VNP_VERSION`, `VNP_EXPIRE_MINUTES` |
| Google OAuth | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` |
| Mail / OTP | `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, credentials |
| AWS/S3 (optional) | `FILESYSTEM_DISK`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` |

---

## Database Policy

- All schema/table/index changes live in `database/Online_Certificate_Classroom.sql`.
- When new features require tables (e.g., student cart, discussions), add their DDL inside the SQL file and re-import.
- Production servers should run the SQL script or apply the specific `CREATE/ALTER` statements manually.

---

## Daily Commands

| Scenario | Command |
| --- | --- |
| Run dev server | `php artisan serve` |
| Build assets | `npm run build` |
| Watch assets | `npm run dev` |
| Cache reset | `php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear` |
| Route check | `php artisan route:list | findstr student` |

VNPay sandbox card for QA:
```
Bank: NCB
Card: 9704198526191432198
Holder: NGUYEN VAN A
Expiry: 07/15
OTP: 123456
```

---

## Testing & QA

- PHPUnit: `php artisan test`
- Code style: `vendor/bin/pint`
- Manual grading & discussion flows should be verified with seeded demo accounts.
- Remember to refresh caches after editing configs/views.

---

## Mobile Integration

- Flutter project: [Online-Certificate-Classroom-Mobile](https://github.com/MinhQuun/Online-Certificate-Classroom-Mobile)
- Consumes `/api/v1/student/...` endpoints (Sanctum token auth, VNPay checkout, lesson discussions, mini-tests, cart sync).
- Keep API responses stable; version new changes under `/api/v1` or bump the prefix when making breaking changes.

---

## Deployment Notes

- Deploy SQL updates before shipping code that depends on new tables.
- Ensure `/payment/vnpay/ipn` is publicly reachable for VNPay callbacks.
- Configure queue workers (`php artisan queue:work`) when grading/emails are async.
- Serve built assets (`npm run build`) behind Vite manifest; remember to set correct `APP_URL`.

---

## Roadmap & Contributions

1. Fork and branch (`feature/...`), follow PSR-12 + Laravel conventions.
2. Apply schema edits directly in `database/Online_Certificate_Classroom.sql`.
3. Update README/API notes so the mobile team stays aligned.

Ideas in progress:
- Finish Flutter screens for discussions, mini-tests, and cart checkout.
- Add notifications (email or websocket) for grading and discussion updates.
- Expand admin analytics and export features.

---

Happy building! For questions about setup or the web/mobile contract, open an issue or contact MinhQuun.
