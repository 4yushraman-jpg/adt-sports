# ADT Sports — Laravel Full-Stack Platform

India's #1 Kabaddi Media Platform built with **Laravel 11**, SQLite (upgradeable to MySQL), Blade templates, and a complete Admin Panel.

---

## 🚀 Quick Start (5 Minutes)

### Requirements
- PHP 8.2+ with extensions: `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- Composer
- Node.js (optional, for asset compilation if needed)

### Step 1 — Install dependencies
```bash
cd adt-sports-laravel
composer install
```

### Step 2 — Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### Step 3 — Database setup
```bash
# SQLite (default — no extra setup needed)
touch database/adt_sports.sqlite

# Run migrations and seed sample data
php artisan migrate:fresh --seed
```

### Step 4 — Storage link (for uploaded images)
```bash
php artisan storage:link
```

### Step 5 — Start the server
```bash
php artisan serve
```

Open: **http://localhost:8000**  
Admin: **http://localhost:8000/admin**

---

## 🔐 Default Login

| Field    | Value                     |
|----------|---------------------------|
| Email    | `admin@adtsports.com`     |
| Password | `ADT@admin2025`           |

> **⚠️ Change your password immediately** after first login via Settings → My Profile.

---

## 📁 Project Structure

```
adt-sports-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          ← Dashboard, Articles, Categories, Media, Settings, Users
│   │   │   ├── Auth/           ← Login/Logout
│   │   │   └── Frontend/       ← Home, Article, Category, Search
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   ├── Models/
│   │   ├── Article.php         ← Full article model with scopes & helpers
│   │   ├── Category.php
│   │   ├── Media.php
│   │   ├── Setting.php
│   │   └── User.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/app.php
├── config/                     ← app, auth, cache, database, filesystems, session
├── database/
│   ├── migrations/             ← Single migration file for all tables
│   └── seeders/
│       └── DatabaseSeeder.php  ← Admin + categories + 6 sample articles
├── public/
│   ├── index.php
│   └── uploads/               ← Place logo.png here
├── resources/views/
│   ├── layouts/
│   │   ├── admin.blade.php     ← Full admin layout
│   │   └── frontend.blade.php  ← Full frontend layout
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   ├── articles/
│   │   │   ├── index.blade.php
│   │   │   └── editor.blade.php  ← Rich text editor
│   │   ├── categories/index.blade.php
│   │   ├── media/index.blade.php
│   │   ├── settings/index.blade.php
│   │   └── users/index.blade.php
│   ├── auth/login.blade.php
│   └── frontend/
│       ├── home.blade.php
│       ├── article.blade.php
│       ├── category.blade.php
│       └── search.blade.php
├── routes/
│   ├── web.php
│   └── console.php
├── storage/                    ← Auto-created on first run
├── artisan
├── composer.json
└── .env.example
```

---

## 🛠 Admin Panel Features

### ✍️ Article Editor
- Rich text editor (Bold, Italic, H2, H3, Blockquote, Lists, Callout boxes, Links)
- HTML source mode toggle
- Auto-generated SEO slugs
- Cover emoji picker (18 options) + background theme picker (8 gradients)
- Cover image upload
- Category assignment
- Featured / Breaking News toggles
- Tag management (Enter or comma to add)
- SEO title & meta description fields
- Save as Draft or Publish instantly
- Word count + estimated read time

### 📰 Article Management
- Table with search, filter by status and category
- Quick publish / unpublish from the list
- View count per article
- Edit, delete from list

### 🏷️ Categories
- Create/edit/delete categories
- Custom hex color per category
- Live article count

### 🖼️ Media Library
- Drag & drop image upload
- Copy image URL to clipboard
- Delete images (removes from storage)

### 👥 Team Members (Admin only)
- Add editors and admins
- Role-based access control (Editor: own articles; Admin: everything)
- Remove members

### ⚙️ Settings
- Site name, tagline, description
- Breaking news ticker text
- Contact email & phone
- Social media links (Facebook, Instagram, YouTube, Twitter)
- Articles per page
- Footer tagline
- Personal profile + password change

---

## 🌐 Public Website Features

- **Home** — Hero story + sidebar stack + category tabs + article feed + feature strip + sidebar
- **Article** — Full article with reading progress bar, font size toggle, share button, related articles
- **Category** — Filtered article list with header
- **Search** — Full-text search across title and excerpt
- **Dark/Light mode** — Persisted in localStorage
- **Reading progress bar** — Top of page while reading
- **Live breaking ticker** — Auto-scrolling, configurable from admin

---

## 🗄 Switching to MySQL

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=adt_sports
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Then run:
```bash
php artisan migrate:fresh --seed
```

---

## 🚀 Deploy to Production

### VPS (Ubuntu + Nginx)
```bash
# Install PHP 8.2 + extensions
sudo apt install php8.2-fpm php8.2-sqlite3 php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd

# Upload files to /var/www/adt-sports
cd /var/www/adt-sports
composer install --optimize-autoloader --no-dev
cp .env.example .env
php artisan key:generate
php artisan migrate --force --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Nginx config:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/adt-sports/public;
    index index.php;
    location / { try_files $uri $uri/ /index.php?$query_string; }
    location ~ \.php$ { fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; include fastcgi_params; }
}
```

### Railway / Render / Fly.io
1. Push to GitHub
2. Set `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`
3. Set build command: `composer install && php artisan migrate --force --seed`
4. Deploy

---

## 📸 Adding Your Logo

Place your logo file at:
```
public/uploads/logo.png
```

It will automatically appear in the navbar, footer, and sidebar.

---

## 🔒 Production Checklist

- [ ] `APP_DEBUG=false` in `.env`
- [ ] `APP_ENV=production` in `.env`
- [ ] Change admin password from Settings panel
- [ ] Enable HTTPS (Nginx + Let's Encrypt)
- [ ] Set correct `APP_URL`
- [ ] Back up `database/adt_sports.sqlite` regularly (or use MySQL)
- [ ] Set correct file permissions: `chmod -R 775 storage bootstrap/cache`

---

Built with ❤️ for **ADT Sports** — India's #1 Kabaddi Media Platform
