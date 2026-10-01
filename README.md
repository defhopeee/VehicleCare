# VehicleCare

A nationwide vehicle breakdown and repair connector. Customers find a partner garage, browse specialist mechanics, buy spare parts, check service pricing, and book an appointment. The system connects the customer to the right garage; the repair and payment happen in person, this is a booking/connector platform, not a payment gateway.

## Features

**Public site**
- Browse garages by county, with contact details, opening hours, and a map
- Browse spare parts with search, filtering, and pagination
- Browse specialist mechanics, filterable by fault type
- View services and transparent pricing
- Book an appointment (repair, spare part purchase, or inspection), the form adjusts to what's relevant
- Live chat, plus call and WhatsApp as backup contact options
- Send feedback, complaints, or inquiries

**Admin panel**
- Dashboard with live counts (pending bookings, new feedback, open chats)
- Full booking management: filter by status, update status, add notes
- CRUD for garages, mechanics, brands, spare parts, services, and fault categories
- Live chat with reply, close, and reopen
- Feedback management (mark read/resolved, delete)
- Sidebar notification badges that update as you act on items
- Admin profile management: name, contact info, photo, username, password

## Tech Stack

- PHP (vanilla, no framework) with PDO + prepared statements throughout
- MySQL / MariaDB
- Bootstrap 5.3 + Bootstrap Icons (self-hosted under `assets/vendor/`, no CDN dependency)
- Vanilla JavaScript for live chat polling, dynamic booking form, pagination

## Setup

1. Requires PHP 7.4+ and MySQL/MariaDB (e.g. via XAMPP).
2. Place this folder under your web server's document root.
3. Create a database and import `breakdown_system.sql`, it creates every table and seeds realistic starting data (garages, mechanics, brands, parts, services, demo bookings/feedback/chat, and a default admin account).
4. Open `config/db.php` and set your database host, name, username, and password.
5. Visit the site root for the public pages, and `/admin/login.php` for the admin panel.

A default admin account is included in the seed data. **Change its password immediately after first login** via the profile menu (top right of the admin dashboard) → My Profile → Change Password.

## Project Structure

```
├── admin/              Admin panel (dashboard, CRUD for every module, profile)
│   └── includes/       Shared admin header/footer
├── assets/
│   ├── css/             Site stylesheet
│   ├── img/              Hero photos, spare part category illustrations
│   ├── uploads/          User-uploaded images (garages, mechanics, parts, brands, admin photos)
│   └── vendor/            Self-hosted Bootstrap + icons
├── config/db.php        Database connection + site constants
├── includes/            Shared public header/footer, auth helpers
├── breakdown_system.sql Full database schema + seed data
└── *.php                 Public-facing pages (index, booking, garages, parts, etc.)
```

## Security Notes

- All database queries use PDO prepared statements.
- Admin login has rate limiting (lockout after repeated failed attempts).
- No payment processing happens in this system by design.
- Change the default admin password immediately in any real deployment.
