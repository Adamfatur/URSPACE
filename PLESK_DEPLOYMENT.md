# Plesk Deployment Guide - Forum UR

## Prerequisites
Pastikan di Plesk sudah terinstall:
- PHP 8.2+
- Node.js 18+ (install via Node.js extension di Plesk)
- npm/yarn
- Composer

## Cara Deploy:

### Option 1: Via Plesk Git Integration (Recommended)
1. Di Plesk Panel → Subscriptions → Your Domain → Git
2. Connect ke GitHub repository `https://github.com/Adamfatur/URSPACE.git`
3. Select branch `main`
4. Plesk akan auto-pull dan run post-install scripts

### Option 2: Manual SSH Deployment
```bash
ssh user@your-domain.com
cd /var/www/vhosts/your-domain.com/httpdocs

# Clone atau pull latest
git clone https://github.com/Adamfatur/URSPACE.git .
# atau jika sudah ada:
git pull origin main

# Install dependencies (ini akan auto-run npm build via composer post-install)
composer install --no-dev --optimize-autoloader

# Manual setup jika diperlukan:
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan cache:clear
php artisan config:clear
```

### Option 3: Deployment Script (Recommended untuk CI/CD)
Buat file `deploy.sh` di root project:

```bash
#!/bin/bash
set -e

echo "🚀 Starting deployment..."

# Update code
git pull origin main

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Setup environment
cp .env.example .env 2>/dev/null || true

# Generate key if not exists
if ! grep -q "APP_KEY=base64" .env; then
    php artisan key:generate
fi

# Install node dependencies and build assets
npm install
npm run build

# Run migrations
php artisan migrate --force

# Setup storage
php artisan storage:link 2>/dev/null || true

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo "✅ Deployment complete!"
```

## Troubleshooting:

### Error: "vite: not found"
**Solusi:** npm dependencies belum diinstall. Jalankan:
```bash
npm install
npm run build
```

### Error: "Node.js not found"
**Solusi:** Install Node.js di Plesk:
- Plesk Panel → Tools & Settings → Extensions → Marketplace
- Cari "Node.js" dan install
- Atau via SSH (CentOS):
  ```bash
  curl -sL https://rpm.nodesource.com/setup_20.x | bash -
  yum install nodejs -y
  ```

### Permission denied di public/storage
**Solusi:** Jalankan:
```bash
chmod -R 755 public/storage
chown -R nobody:nobody public/storage
php artisan storage:link
```

### Assets tidak muncul (CSS/JS)
**Solusi:** Pastikan sudah run:
```bash
npm run build
php artisan config:clear
php artisan cache:clear
```

### Database error saat migrate
**Solusi:** Pastikan `.env` memiliki DB settings yang benar untuk production

## Environment Variables yang Penting:
```
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql (atau sqlite untuk testing)
MAIL_DRIVER=smtp
CACHE_DRIVER=file
SESSION_DRIVER=file
```

## Performance Tips:
- Set `APP_DEBUG=false`
- Run `php artisan config:cache`
- Setup queue worker (optional untuk email)
- Enable gzip compression di Plesk (Performance → Compression)
