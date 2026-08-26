# 🏠 Boarding House Management System
 
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-3.3-FDAE4B?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat&logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)
 
A boarding house (*kos/kontrakan*) management system built with **Laravel 11** and the **Filament 3** admin panel. It helps a boarding house owner manage rooms, tenants, and payments in one dashboard — with **online payments via Midtrans** and **automatic WhatsApp notifications via Twilio**.
 
> 📸 *Add a screenshot or short screen-recording GIF of the dashboard here — it's the first thing recruiters and visitors will look at.*
> ```md
> ! COMMING SOON
> ```
 
## ✨ Features
 
- 🛏️ Room / unit management (availability, room type, pricing)
- 👤 Tenant (penyewa) management
- 💳 Online payment powered by [Midtrans](https://midtrans.com/)
- 📲 Automated WhatsApp notifications (payment reminders / confirmations) via [Twilio](https://www.twilio.com/)
- 🔐 Admin dashboard built with Filament 3 (auth, resources, tables, forms out of the box)
- 🔑 API-ready authentication via Laravel Sanctum
> Double-check this list against what's actually implemented, and add/remove items so it matches the real app.

## 🧰 Tech Stack
 
| Layer | Technology |
|---|---|
| Backend | Laravel 11 (PHP 8.2) |
| Admin Panel | Filament 3.3 |
| Payment Gateway | Midtrans PHP SDK |
| Notifications | Twilio SDK (WhatsApp) |
| Auth | Laravel Sanctum |
| Database | MySQL / SQLite |
| Frontend build | Vite |

## ✅ Requirements
 
- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL (or SQLite for local development)
- A [Midtrans](https://midtrans.com/) sandbox account (for payment testing)
- A [Twilio](https://www.twilio.com/) account with WhatsApp sandbox enabled (for notification testing)
## 🚀 Installation
 
```bash
# 1. Clone the repository
git clone https://github.com/rayyan1510/boarding-house.git
cd boarding-house
 
# 2. Install PHP dependencies
composer install
 
# 3. Install JS dependencies
npm install
 
# 4. Copy the environment file and generate an app key
cp .env.example .env
php artisan key:generate
 
# 5. Configure your .env
# - Set DB_* variables (or leave DB_CONNECTION=sqlite for a quick start)
# - Add Midtrans and Twilio credentials (see below)
 
# 6. Run migrations (add --seed if you have seeders for demo data)
php artisan migrate --seed
 
# 7. Create a Filament admin user
php artisan make:filament-user
 
# 8. Build frontend assets
npm run build
# or, for local development with hot reload:
npm run dev
 
# 9. Start the app
php artisan serve
```
### ⚙️ Configuration
 
`.env.example` in the repo currently only has Laravel's default variables — it's missing the Midtrans and Twilio keys the project actually depends on. Copy the block below into your `.env` (and add the same empty keys to `.env.example` so other developers/reviewers know what's required) and fill in your own values:
 
```env
APP_NAME="Boarding House"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
 
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=boarding_house
DB_USERNAME=root
DB_PASSWORD=
 
# Midtrans — get these from https://dashboard.midtrans.com (Sandbox settings)
MIDTRANS_MERCHANT_ID=
MIDTRANS_CLIENT_KEY=
MIDTRANS_SERVER_KEY=
MIDTRANS_IS_PRODUCTION=false
 
# Twilio — get these from https://console.twilio.com
TWILIO_SID=
TWILIO_AUTH_TOKEN=
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
```

## 📁 Project Structure
 
Standard Laravel 11 structure, with Filament resources typically under `app/Filament/Resources`:
 
```
app/            → models, Filament resources, services (Midtrans/Twilio integrations)
database/       → migrations, factories, seeders
resources/      → views, frontend assets
routes/         → web.php, api.php
tests/          → feature & unit tests
```

## 📄 License
 
This project is open-sourced under the [MIT license].

 ## 🤝 Contributing
 
This is currently a personal/portfolio project, but suggestions and issues are welcome — feel free to open an issue or a pull request.

