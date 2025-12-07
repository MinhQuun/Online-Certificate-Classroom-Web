<div align="center">

# Online Certificate Classroom (Web)

Laravel web platform + REST API for the “Xây dựng hệ thống quản lý lớp học chứng chỉ trực tuyến” project. Shares the API contract with the Flutter client: [Online-Certificate-Classroom-Mobile](https://github.com/MinhQuun/Online-Certificate-Classroom-Mobile).

![Laravel](https://img.shields.io/badge/Laravel-11.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![Sanctum](https://img.shields.io/badge/Auth-Sanctum-darkgreen)
![VNPay](https://img.shields.io/badge/Payments-VNPay-orange)

</div>

---

## Overview
- Full-stack e-learning platform for certificate programs: course/lesson delivery, mini-tests (MCQ, writing, speaking uploads), discussions, grading, and certificates.
- Roles: Student (học viên), Teacher (giảng viên), Admin (quản trị). Each role has its own dashboards and permissions.
- Payments via VNPay; Google OAuth login; OTP password reset; exports via DOMPDF + Maatwebsite Excel.
- Mobile app consumes the same `/api/v1` endpoints with Sanctum token auth for a unified experience.

## Architecture & Stack

| Layer | Technology |
| --- | --- |
| Backend | Laravel 11, PHP 8.2, Sanctum, Socialite, Spatie Permission |
| Frontend | Blade + Vite (modular CSS/JS per page), Axios |
| Database | MySQL 8 — managed by `database/Online_Certificate_Classroom.sql` (single source of truth) |
| Storage | Local / S3 (Flysystem v3) |
| Payments | VNPay (return + IPN) |
| Docs/Exports | DOMPDF, Maatwebsite Excel |

**Database policy:** Do **not** run `php artisan migrate` in production. All schema/index changes live in `database/Online_Certificate_Classroom.sql`.

## Key Features

**Student**
- Browse courses/combos, preview free lessons, reviews; persistent cart synced across devices.
- Lesson progress tracking, attempt history, review exercises/mini-tests with uploads and grading results.
- Discussions with threaded replies, soft delete, and role-based moderation.
- Profile, password change, order history, certificates dashboard.

**Teacher**
- Chapter/lesson CRUD with materials and ordering.
- Mini-test builder, manual grading (writing/speaking), progress insights per learner.
- Discussion moderation: pin/lock/status.

**Admin**
- Catalog management: categories, courses, combos, promotions.
- User/role management (Spatie Permission); contact center replies.
- Orders/invoices, VNPay logs, enrollment and combo operations.

**Shared**
- Sanctum-authenticated API for both web SPA features and Flutter client.
- Google OAuth, OTP reset, S3-ready storage, reporting/export utilities.

## Repository Map

| Path | Description |
| --- | --- |
| `routes/web.php` | Public pages + student/teacher/admin dashboards + VNPay routes |
| `routes/api.php` | `/api/v1/student/...` endpoints shared with mobile |
| `app/Http/Controllers/Student` | Web controllers: cart, checkout, progress, discussions, mini-tests, reviews |
| `app/Http/Controllers/Teacher` | Dashboards, lecture CRUD, grading, moderation |
| `app/Http/Controllers/Admin` | Catalog, users/roles, promotions, invoices, contact replies |
| `app/Http/Controllers/API/Student` | REST controllers for the Flutter client (auth, cart, checkout, progress, etc.) |
| `app/Support/Cart` | Session + DB cart shared across web/API |
| `config/vnpay.php` | VNPay config (driven by `.env`) |
| `database/Online_Certificate_Classroom.sql` | **Authoritative schema + seed data** |
| `resources/views` | Blade views for public + role-specific areas |

## Prerequisites

- PHP 8.2 with `zip`, `fileinfo`, `mbstring`, `openssl`, `intl`, `bcmath`
- Composer 2.6+
- Node.js 18+ (npm)
- MySQL 8.0+
- Git; optional XAMPP/Valet
- Add `php.exe` (e.g., `C:\xampp\php`) to PATH for Composer on Windows

## Setup (Local)

```bash
git clone https://github.com/MinhQuun/Online-Certificate-Classroom-Web.git
cd Online-Certificate-Classroom-Web
composer install
npm install
cp .env.example .env
php artisan key:generate
```

1) Configure `.env`: DB, mail, VNPay, Google OAuth, AWS/S3 (optional), Sanctum stateful domains.  
2) Import database: run `database/Online_Certificate_Classroom.sql` (no migrations).  
3) Link storage & build assets:
```bash
php artisan storage:link
npm run dev   # or npm run build for production
```
4) Serve:
```bash
php artisan serve
```
- Web: http://localhost:8000  
- API base: http://localhost:8000/api/v1

## Environment Checklist

| Feature | Required ENV |
| --- | --- |
| Database | `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` |
| Sanctum | `APP_URL`, `SANCTUM_STATEFUL_DOMAINS`, `SESSION_DOMAIN` (if cross-domain) |
| VNPay | `VNP_TMN_CODE`, `VNP_HASH_SECRET`, `VNP_URL`, `VNP_RETURN_URL`, `VNP_IPN_URL`, `VNP_VERSION`, `VNP_EXPIRE_MINUTES` |
| Google OAuth | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` |
| Mail/OTP | `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, creds |
| AWS/S3 (optional) | `FILESYSTEM_DISK`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` |

## Database Policy

- Keep schema/index changes inside `database/Online_Certificate_Classroom.sql`.
- For production, apply SQL from that file (or the specific ALTERs) before deploying code depending on new tables/columns.

## Useful Commands

| Scenario | Command |
| --- | --- |
| Dev server | `php artisan serve` |
| Build assets | `npm run build` |
| Watch assets | `npm run dev` |
| Clear caches | `php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear` |
| Routes filter | `php artisan route:list | findstr student` |

VNPay sandbox card for QA:
```
Bank: NCB
Card: 9704198526191432198
Holder: NGUYEN VAN A
Expiry: 07/15
OTP: 123456
```

## Testing & QA

- Automated: `php artisan test`
- Code style: `vendor/bin/pint`
- Manual: verify grading flows (writing/speaking), discussions, cart + VNPay return/IPN, certificate issuance. Refresh caches after changing configs/views.

## Mobile Integration

- Flutter client: [Online-Certificate-Classroom-Mobile](https://github.com/MinhQuun/Online-Certificate-Classroom-Mobile)
- Uses `/api/v1/student/...` with Sanctum token auth (cart sync, checkout, discussions, mini-tests, progress).
- Keep responses stable; version breaking changes under `/api/v1` or bump the prefix.

## Deployment Checklist

- Apply SQL updates from `database/Online_Certificate_Classroom.sql` first.
- Set `APP_URL`, `SESSION_DOMAIN`, `SANCTUM_STATEFUL_DOMAINS` correctly for the deployed domain.
- Ensure `/payment/vnpay/ipn` is publicly reachable.
- Build assets: `npm run build` (serve with Vite manifest).
- Start queues for async work (emails/grading if queued): `php artisan queue:work`.
- Warm caches where appropriate: `php artisan config:cache route:cache view:cache`.

## Maintenance & Contributions

- Branch per feature (`feature/...`), follow PSR-12 + Laravel conventions.
- Document API changes so mobile stays aligned; update the SQL file for schema edits.
- Open issues/PRs for questions or improvements. Contact: MinhQuun.
