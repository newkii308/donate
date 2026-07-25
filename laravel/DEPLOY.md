# คู่มือขึ้นโฮส DirectAdmin (Shared Hosting) — DONATE LIVE

โปรเจกต์นี้เป็น **Laravel 12** ออกแบบให้รันบน Shared Hosting (DirectAdmin) ได้
ไม่ต้องใช้ Docker / VPS / Redis / WebSocket — แจ้งเตือนเรียลไทม์ใช้ **Polling**

> ความต้องการขั้นต่ำของโฮส: PHP **8.2+**, MySQL/MariaDB, รองรับ `mod_rewrite` (.htaccess),
> ต่ออินเทอร์เน็ตออกได้ (สำหรับเสียงไทย TTS), ตั้ง Cron Job ได้

---

## ภาพรวมสำคัญ (อ่านก่อน)

Laravel มีโฟลเดอร์ `public/` เป็น "หน้าบ้าน" (web root) ส่วนโค้ดที่เหลือ (`app`, `vendor`, `.env`)
**ต้องไม่อยู่ในที่ที่เข้าถึงผ่านเว็บได้** เพื่อความปลอดภัย แต่ Shared Hosting จะเสิร์ฟจาก
`public_html/` เสมอ จึงมี 2 วิธีจัดการ:

| วิธี | เหมาะกับ | ความปลอดภัย |
|---|---|---|
| **วิธีที่ 1** ตั้ง Document Root → ชี้ไปที่ `.../laravel/public` | โฮสที่ตั้ง docroot เองได้ | ดีที่สุด ✅ |
| **วิธีที่ 2** วางแอปนอก `public_html` + ก๊อป `public` เข้า `public_html` + แก้ `index.php` | ใช้ได้ทุกโฮส | ดี ✅ |

ถ้าแผง DirectAdmin ตั้ง Document Root ได้ → ใช้ **วิธีที่ 1** (ง่ายสุด)
ถ้าไม่ได้ → ใช้ **วิธีที่ 2**

---

## ขั้นที่ 0 — เตรียมไฟล์ในเครื่อง (ก่อนอัป)

```powershell
cd "C:\Users\Administrator\Music\donate x2\newlab"

# 1) build CSS/JS (โฮสไม่มี Node) — จะได้โฟลเดอร์ public/build
npm run build

# 2) (ถ้าโฮสไม่มี Composer/SSH) ติดตั้ง vendor แบบ production ในเครื่องก่อน
composer install --no-dev --optimize-autoloader

# 3) ล้าง cache เก่าที่ผูกกับเครื่อง dev (สำคัญ! กันค่าเก่าติดไป)
php artisan optimize:clear
```

**ห้ามอัปขึ้นโฮส** (อัปแล้วจะช้า/พัง): `node_modules/`, `.git/`,
`database/database.sqlite`, `storage/logs/*.log`, `.env` (ตัว dev),
`bootstrap/cache/*.php`, `storage/framework/{cache,sessions,views}/*`

> เคล็ดลับ: zip ทั้งโฟลเดอร์ (ยกเว้นที่ห้ามด้านบน) แล้วอัปไฟล์ zip ผ่าน File Manager
> ของ DirectAdmin → คลิกขวา Extract จะเร็วกว่าอัปทีละไฟล์มาก

---

## ขั้นที่ 1 — เลือกเวอร์ชัน PHP

DirectAdmin → **Select PHP Version** (หรือ PHP Selector)
- เลือก **PHP 8.2 หรือ 8.3** (8.4 ถ้ามี)
- เปิด extension ที่ต้องใช้ให้ครบ: `pdo_mysql`, `mbstring`, `openssl`, `curl`,
  `fileinfo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- ปรับค่า (PHP Settings / php.ini) สำหรับอัปโหลดสื่อ:
  - `upload_max_filesize = 16M`
  - `post_max_size = 16M`
  - `memory_limit = 256M`

---

## ขั้นที่ 2 — สร้างฐานข้อมูล MySQL

DirectAdmin → **MySQL Management** → Create new Database
- จดไว้: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- (DirectAdmin มักเติม prefix ชื่อยูสไว้ เช่น `myuser_donate`)

---

## ขั้นที่ 3 — อัปโหลดไฟล์

### วิธีที่ 1 (ตั้ง Document Root ได้)
1. อัปทั้งโปรเจกต์ไปไว้ที่ `/home/USERNAME/laravel/` (นอก `public_html`)
2. DirectAdmin → Domain Setup → เลือกโดเมน → ตั้ง **Document Root** เป็น
   `/home/USERNAME/laravel/public`
3. จบ — `index.php` และ `.htaccess` ใช้ของเดิมใน `public/` ได้เลย

### วิธีที่ 2 (ใช้ได้ทุกโฮส)
1. อัปทั้งโปรเจกต์ไปไว้ที่ `/home/USERNAME/laravel/` (นอก `public_html`)
2. **ย้ายไฟล์ทุกอย่างในโฟลเดอร์ `laravel/public/`** ไปไว้ใน `public_html/`
   (รวมไฟล์ `.htaccess`, `index.php`, โฟลเดอร์ `build/`)
3. แก้ `public_html/index.php` ให้ชี้กลับไปที่โค้ดแอป — เปลี่ยน 2 บรรทัดนี้:

```php
// เดิม: require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../laravel/vendor/autoload.php';

// เดิม: $app = require_once __DIR__.'/../bootstrap/app.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
```
และบรรทัด maintenance (ถ้ามี) เปลี่ยนเป็น `__DIR__.'/../laravel/storage/framework/maintenance.php'`

> `../laravel` = path สัมพัทธ์จาก `public_html` ไปยังโฟลเดอร์แอป ปรับให้ตรงโครงสร้างจริง

---

## ขั้นที่ 4 — ติดตั้ง Composer dependencies

- **มี SSH/Terminal:**
  ```bash
  cd ~/laravel
  composer install --no-dev --optimize-autoloader
  ```
- **ไม่มี SSH:** อัปโฟลเดอร์ `vendor/` (ที่ build ไว้ในขั้นที่ 0) ขึ้นไปด้วยเลย

---

## ขั้นที่ 5 — ตั้งค่า `.env` (production)

สร้างไฟล์ `.env` ในโฟลเดอร์แอป (`~/laravel/.env`) — ดูตัวอย่างจาก `.env.production.example`
ค่าที่สำคัญ:

```dotenv
APP_NAME="DONATE LIVE"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_LOCALE=th

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myuser_donate
DB_USERNAME=myuser_donate
DB_PASSWORD=รหัสผ่าน

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

> ⚠️ `APP_DEBUG=false` เสมอบน production · `APP_ENV=production`

---

## ขั้นที่ 6 — Generate APP_KEY + Migrate

**มี SSH/Terminal** (รันในโฟลเดอร์แอป):
```bash
php artisan key:generate --force
php artisan migrate --force          # สร้างตาราง (ยังไม่ใส่ข้อมูลตัวอย่าง)
php artisan db:seed --force          # (ไม่บังคับ) ใส่ข้อมูลเดโม + แอดมินทดสอบ
```

**ไม่มี SSH:**
- `APP_KEY`: รัน `php artisan key:generate --show` ในเครื่อง แล้วเอาค่ามาวางใน `.env` ของโฮส
- Migrate: รันในเครื่องแบบชี้ไป DB ของโฮส (ถ้าเปิด remote MySQL) — หรือ export โครงสร้าง
  เป็นไฟล์ `.sql` แล้ว import ผ่าน **phpMyAdmin** ของ DirectAdmin

**สร้างแอดมินจริง (แทนการ seed เดโม)** — ผ่าน Terminal:
```bash
php artisan tinker --execute="\App\Models\User::create(['name'=>'Admin','email'=>'you@mail.com','password'=>bcrypt('รหัสที่ตั้ง'),'role'=>'admin','is_active'=>true]);"
```

---

## ขั้นที่ 7 — Storage Link (รูป/QR/ไฟล์เสียงอัปโหลด)

- **วิธีที่ 1:** รัน `php artisan storage:link` ได้ตามปกติ
- **วิธีที่ 2:** ต้องชี้ symlink เข้าหา `public_html` เอง (เพราะ public ถูกย้ายแล้ว):
  ```bash
  ln -s ~/laravel/storage/app/public ~/domains/your-domain.com/public_html/storage
  ```
  (ถ้าทำ symlink ไม่ได้ ให้คัดลอกโฟลเดอร์ `storage/app/public` ไปวางเป็น `public_html/storage` แทน)

โฟลเดอร์ `storage/app/tts` (แคชเสียงไทย) จะถูกสร้างเองอัตโนมัติ — แค่ให้ `storage/` เขียนได้

---

## ขั้นที่ 8 — สิทธิ์ไฟล์ (Permissions)

ให้โฟลเดอร์เหล่านี้ "เขียนได้" (ผ่าน File Manager → Permissions หรือ SSH):
```bash
chmod -R 775 storage bootstrap/cache
```

---

## ขั้นที่ 9 — Optimize (cache config/route/view)

มี SSH → รันเพื่อให้เร็วขึ้น (รันใหม่ทุกครั้งที่แก้ `.env`/route/view):
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
> ถ้าแก้ `.env` แล้วค่าไม่อัปเดต ให้รัน `php artisan optimize:clear` ก่อน

---

## ขั้นที่ 10 — ตั้ง Cron Job (สำคัญ — ล้างแคชเสียง/คิวเก่าอัตโนมัติ)

DirectAdmin → **Cron Jobs** → เพิ่มงานใหม่ ทุก 1 นาที:
```
* * * * * /usr/local/bin/php /home/USERNAME/laravel/artisan schedule:run >/dev/null 2>&1
```
> ปรับ path ของ `php` ให้ตรงเวอร์ชันที่เลือก (ดูได้จาก Select PHP Version — มักเป็น
> `/usr/local/php82/bin/php` หรือ `/usr/local/bin/php`) และ path ของ `artisan` ให้ตรงจริง

ระบบจะรัน `newlab:cleanup` ให้อัตโนมัติทุกชั่วโมง (ลบแคชเสียง TTS/คิวแจ้งเตือนที่หมดอายุ)

---

## ขั้นที่ 11 — เปิด SSL (HTTPS)

DirectAdmin → **SSL Certificates** → ใช้ **Let's Encrypt** (ฟรี) → ออกใบรับรอง
แล้วตั้ง `APP_URL=https://...` ใน `.env` + เปิด "Force HTTPS redirect"

> จำเป็น: OBS Browser Source และ TTS เสียงไทยทำงานได้ดีกว่าบน HTTPS

---

## ขั้นที่ 12 — ทดสอบ

1. เปิด `https://your-domain.com` → เห็นหน้าแรก
2. สมัคร/ล็อกอิน → เข้าแดชบอร์ด
3. หน้า **ตั้งค่า Overlay** → กด "เล่นตัวอย่าง" → มีเสียง + อ่านไทย
4. เปิดลิงก์ `/overlay/...` ใน OBS (Browser Source) → กดทดสอบ → แจ้งเตือนเด้ง
5. เปิด `/donate/ชื่อผู้ใช้` ทดลองโดเนท

---

## ปัญหาที่เจอบ่อย (Troubleshooting)

| อาการ | วิธีแก้ |
|---|---|
| 500 Error หน้าขาว | เปิด `APP_DEBUG=true` ชั่วคราวดู error → ดู `storage/logs/laravel.log` → แก้แล้วปิดกลับ |
| CSS/JS ไม่ขึ้น (หน้าเปล่าๆ) | ลืมอัป `public/build` หรือ `APP_URL` ผิด → รัน `npm run build` แล้วอัปใหม่ |
| รูป/QR ไม่ขึ้น | ยังไม่ได้ทำ `storage:link` (ขั้นที่ 7) |
| เสียงไทยไม่ออกบนโฮส | โฮสบล็อกการต่อเน็ตออก หรือ `storage/app/tts` เขียนไม่ได้ → เช็คสิทธิ์ + นโยบาย outbound |
| `419 Page Expired` ตอนล็อกอิน | ค่า session ผิด → ตั้ง `SESSION_DRIVER=database` + `php artisan migrate` (ตาราง sessions) แล้ว `php artisan optimize:clear` |
| migrate แล้ว error เรื่อง APP_KEY | ยังไม่ได้ generate → ทำขั้นที่ 6 |

---

## สรุปลำดับสั้นๆ (Checklist)
- [ ] `npm run build` + `composer install --no-dev` ในเครื่อง
- [ ] เลือก PHP 8.2+ + เปิด extensions
- [ ] สร้าง MySQL DB
- [ ] อัปไฟล์ (วิธี 1 หรือ 2)
- [ ] ตั้ง `.env` (production + MySQL)
- [ ] `key:generate` + `migrate --force`
- [ ] `storage:link`
- [ ] สิทธิ์ `storage/`, `bootstrap/cache/` เขียนได้
- [ ] `config:cache route:cache view:cache`
- [ ] Cron `schedule:run` ทุก 1 นาที
- [ ] เปิด SSL + `APP_URL` https
- [ ] ทดสอบครบทุกหน้า
