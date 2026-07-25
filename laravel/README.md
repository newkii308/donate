# NEWLAB — Donation Platform for Streamers

A complete, production-ready, **multi-tenant donation platform** built with **Laravel 12**.
Designed to be simple, fast, stable, and shared-hosting friendly (DirectAdmin) — no Docker,
Kubernetes, VPS, Redis or WebSockets required. Real-time alerts use lightweight **polling**.

## Features

- **Public donation page** — `/donate/{username}` — avatar, QR payment, copy account number,
  donation form (name, amount, message, anonymous), mobile-first. No registration for donors.
- **OBS overlay** — `/overlay/{overlay_key}` — transparent browser source, polls every 3 s,
  plays animations / images / GIF / WEBM, notification sound, and TTS. **Queue never overlaps.**
- **Overlay customization** — font, size, weight, color, background, radius, shadow, position,
  width/height, animation + durations, sound (volume/delay), and full TTS control.
- **Text to Speech** — fully customizable template (`{streamer_name} {donor_name} {amount}
  {currency} {message}`) with server-side caching. Nothing is hardcoded.
- **Test notification** — fire a fake alert to the overlay without creating a real donation.
- **Dashboard** — today / month / all-time totals, 7-day chart, recent donations, statistics.
- **Donation history** — search, date filtering, **CSV export**.
- **Media manager** — upload images (PNG/JPG/WEBP), animation (GIF/WEBM), audio (MP3/WAV/OGG).
- **Admin** — manage users & streamers, suspend/activate, view all donations, media, activity
  logs, and global settings.
- **Security** — CSRF, validated & sanitized input (XSS/SQLi safe via Eloquent + Blade),
  rate limiting, file validation, role-based authorization, suspended-account guard.

## Tech stack

Laravel 12 · PHP 8.2+ · MySQL/MariaDB (SQLite for local/testing) · Blade · Tailwind CSS v4 ·
Alpine.js · Vanilla JS · Vite.

## Local setup

```bash
composer install
npm install && npm run build
cp .env.example .env        # then set APP_KEY, DB_* (SQLite works out of the box)
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

### Demo accounts (after seeding)

| Role      | Email                  | Password   |
|-----------|------------------------|------------|
| Admin     | `admin@newlab.test`    | `password` |
| Streamer  | `streamer@newlab.test` | `password` |

The demo streamer's donation page is at `/donate/demo`.

## Database tables

`users`, `streamers`, `donation_pages`, `donations`, `overlay_settings`, `media`,
`notification_queue`, `tts_cache`, `activity_logs` — with foreign keys, indexes and soft
deletes where appropriate.

## Architecture

- **Service layer** (`app/Services`) holds the business logic: `DonationService`,
  `NotificationQueueService`, `TtsService`, `MediaService`, `StatisticsService`,
  `StreamerService`, `ActivityLogService`, `GlobalSettings`.
- **Form Requests** validate every write. **Enums** model roles/statuses/types.
- Controllers stay thin; Blade components keep the UI DRY.

## Polling API

| Method | Endpoint                                   | Purpose                          |
|--------|--------------------------------------------|----------------------------------|
| GET    | `/api/overlay/{overlay_key}/check`         | Next pending alert (one-at-once) |
| POST   | `/api/overlay/{overlay_key}/complete`      | Acknowledge an alert finished    |

The server marks an alert `playing` when delivered and only releases the next one after the
overlay reports completion, guaranteeing sounds/TTS/animations never overlap and stay in
chronological order.

## Shared hosting (DirectAdmin) deployment

1. Upload the project; point the web root to `public/`.
2. Create a MySQL database and set `DB_*` in `.env` (`APP_ENV=production`, `APP_DEBUG=false`).
3. Run `php artisan migrate --force` and `php artisan storage:link`.
4. Build assets locally with `npm run build` and upload `public/build`.
5. Add **one** Cron entry (every minute) so scheduled cleanup runs:

   ```
   * * * * * php /home/USER/domains/your-domain/artisan schedule:run >> /dev/null 2>&1
   ```

   This prunes expired TTS cache and old completed notifications automatically.

## Tests

```bash
php artisan test
```

Feature + unit tests cover the donation flow, overlay polling/queue, TTS templating,
authentication & role authorization, and the media/dashboard areas.
