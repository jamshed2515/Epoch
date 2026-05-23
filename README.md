# Epoch

**Find the right professional. Pick a real slot. Confirm the appointment.**

Epoch is a Laravel-powered appointment booking platform for connecting people with trusted professionals across doctors, tutors, consultants, lawyers, therapists, and fitness coaches. It brings discovery, scheduling, payments, dashboards, admin controls, and API access into one clean product experience.

The app is built for a marketplace-style workflow: clients browse professionals, professionals manage availability and appointment states, and admins keep the platform organized.

## What Epoch Does

- Discover professionals by category, search, rating, location, and profile details.
- Book appointments against real availability windows and avoid already-booked slots.
- Let clients track upcoming and past appointments from a user dashboard.
- Let professionals confirm, reject, complete, and manage appointments from their own dashboard.
- Let professionals create and update public profiles, consultation fees, experience, specialization, and working hours.
- Let admins manage users, professionals, categories, and platform-wide appointments.
- Support Razorpay live payments when keys are configured, with a built-in demo payment mode for local development.
- Send appointment confirmation and cancellation emails through queued mailables.
- Offer public and authenticated API endpoints backed by Laravel Sanctum.
- Support English and Hindi interface strings.

## Product Story

Epoch is about turning appointment booking from a back-and-forth conversation into a crisp, visible timeline.

A user can land on the homepage, search for a professional, inspect experience and consultation fees, choose a date and slot, pay, and receive an appointment record. A professional can review the booking, move it through the appointment lifecycle, and keep availability current. An admin gets the control room: categories, professionals, users, and appointments in one place.

## Core Roles

| Role | Capabilities |
| --- | --- |
| Client | Register/login, browse professionals, book and pay for appointments, view appointment history, cancel eligible future appointments |
| Professional | Maintain a professional profile, set availability, confirm/reject/complete appointments, view dashboard metrics |
| Admin | Manage users, professionals, categories, and appointments |

## Main Modules

- **Marketplace discovery:** public category browsing and professional profile pages.
- **Scheduling engine:** availability records generate date-specific time slots, excluding pending and confirmed bookings.
- **Appointment lifecycle:** pending, confirmed, cancelled, and completed states.
- **Payments:** Razorpay integration with signature verification, plus local demo checkout when live keys are absent.
- **Dashboards:** separate views for users, professionals, and admins.
- **Localization:** English and Hindi language files.
- **API:** public professional listing/detail endpoints, authenticated appointment resources, and a health check.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Blade, Tailwind CSS, Alpine.js, Lucide icons, Vite
- **Database:** SQLite by default for local development, MySQL-ready configuration for production
- **Auth/API:** Laravel authentication plus Sanctum
- **Payments:** Razorpay
- **Mail:** Laravel mailables and queues
- **Testing:** PHPUnit

## Quick Start

Clone the project and install dependencies:

```bash
composer install
npm install
```

Create your environment file:

```bash
cp .env.example .env
php artisan key:generate
```

For SQLite development, create the database file and set `DB_DATABASE` in `.env` to its absolute path:

```bash
touch database/database.sqlite
```

Run migrations and seed the demo marketplace:

```bash
php artisan migrate --seed
```

Build frontend assets:

```bash
npm run build
```

Start the development workflow:

```bash
composer run dev
```

That launches the Laravel server, queue listener, logs, and Vite together through `concurrently`.

## Demo Data

The seeders create a working local marketplace with:

- Professional categories such as Doctors, Tutors, Consultants, Lawyers, Therapists, and Fitness Coaches.
- Demo professionals across Indian cities with fees, ratings, specializations, and availability.
- Demo users and sample appointment records.

Seeded accounts use `password` as the default password. The admin account is:

```text
admin@epoch.com
```

## Payments

Epoch supports two payment paths:

- **Demo mode:** used automatically when Razorpay keys are missing or placeholder values. This lets you simulate a successful booking locally.
- **Live mode:** enabled when valid `RAZORPAY_KEY` and `RAZORPAY_SECRET` values are configured.

Add the Razorpay keys to `.env` when you are ready to test real checkout flows.

## API Surface

Public endpoints:

```text
GET /api/ping
GET /api/professionals
GET /api/professionals/{professional}
```

Authenticated with Sanctum:

```text
GET /api/user
apiResource /api/appointments
```

## Useful Commands

```bash
php artisan migrate --seed
php artisan test
npm run build
composer run dev
```

On Windows PowerShell, if script execution blocks `npm`, use:

```bash
npm.cmd install
npm.cmd run build
```

## Project Notes

- `.env`, `vendor`, `node_modules`, local SQLite data, logs, and built assets are intentionally ignored.
- The repository identity is Epoch.
- The default feature test may need database refresh setup before it can hit `/` successfully with an in-memory SQLite database.

## License

Epoch is built on Laravel and follows the license terms included with this project.
