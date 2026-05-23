# SETUP.md — Panduan Deployment Production FoundationOS

> 📌 **Dokumen ini adalah panduan lengkap untuk deploy FoundationOS di environment production.**
> Untuk onboarding system & konfigurasi penggunaan, lihat [ONBOARDING.md](./ONBOARDING.md).

---

## 📋 Daftar Isi

1. [Prerequisites](#-prerequisites)
2. [Server Setup](#-server-setup)
3. [Database Setup](#-database-setup)
4. [Application Deployment](#-application-deployment)
5. [Web Server Configuration](#-web-server-configuration)
6. [Background Services](#-background-services)
7. [Security Hardening](#-security-hardening)
8. [Monitoring & Logging](#-monitoring--logging)
9. [Performance Optimization](#-performance-optimization)
10. [Backup & Disaster Recovery](#-backup--disaster-recovery)
11. [Post-Deployment Checklist](#-post-deployment-checklist)

---

## 🔧 Prerequisites

### Spesifikasi Server Minimal

**Hardware**:
```
CPU:           2 cores minimum (4 cores recommended)
RAM:           4GB minimum (8GB recommended)
Storage:       20GB minimum SSD (100GB+ recommended)
Network:       1Gbps uplink
```

**Software Requirements**:
```
PHP:           8.4+
Node.js:       18.x LTS atau lebih baru
Composer:      2.4+
npm:           9.x+
Database:      MySQL 8.0+ atau PostgreSQL 12+
Web Server:    Nginx atau Apache 2.4+
SSL/TLS:       Let's Encrypt atau certificate provider lainnya
```

### Domain & SSL

- Domain yang sudah terdaftar (misal: `app.institution.edu`)
- SSL/TLS certificate (gunakan Let's Encrypt untuk free)
- DNS records sudah dikonfigurasi ke server IP

### System Access

- Root atau sudo access ke server
- SSH key-based authentication (recommended)
- Firewall access untuk ports: 80, 443, 22

---

## 🖥️ Server Setup

### 1. Operating System Preparation

```bash
# Update system packages (Ubuntu/Debian)
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl wget git unzip

# Set timezone
sudo timedatectl set-timezone Asia/Jakarta

# Enable firewall
sudo ufw enable
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

### 2. PHP 8.4 Installation

```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php
sudo apt update

# Install PHP 8.4 dan extensions
sudo apt install -y \
  php8.4 \
  php8.4-cli \
  php8.4-fpm \
  php8.4-mysql \
  php8.4-pgsql \
  php8.4-sqlite3 \
  php8.4-mbstring \
  php8.4-xml \
  php8.4-bcmath \
  php8.4-json \
  php8.4-curl \
  php8.4-zip \
  php8.4-gd \
  php8.4-intl \
  php8.4-redis

# Verify PHP installation
php -v
```

### 3. Web Server Installation

#### **Nginx** (Recommended)

```bash
# Install Nginx
sudo apt install -y nginx

# Start and enable
sudo systemctl start nginx
sudo systemctl enable nginx

# Verify
sudo systemctl status nginx
```

#### **Apache 2.4** (Alternative)

```bash
# Install Apache
sudo apt install -y apache2 apache2-utils libapache2-mod-php8.4

# Enable modules
sudo a2enmod rewrite
sudo a2enmod proxy_fcgi
sudo a2enmod setenvif
sudo a2enmod headers

# Start and enable
sudo systemctl start apache2
sudo systemctl enable apache2
```

### 4. Node.js & npm Installation

```bash
# Using NodeSource repository
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Verify
node -v
npm -v
```

### 5. Composer Installation

```bash
# Download Composer installer
curl -sS https://getcomposer.org/installer | php

# Move to global location
sudo mv composer.phar /usr/local/bin/composer

# Verify
composer --version
```

### 6. Create Application User

```bash
# Create non-root user for application
sudo useradd -m -s /bin/bash foundationos

# Create app directory
sudo mkdir -p /var/www/foundationos
sudo chown -R foundationos:foundationos /var/www/foundationos

# Switch to application user
sudo su - foundationos
```

---

## 💾 Database Setup

### MySQL 8.0+

```bash
# Install MySQL
sudo apt install -y mysql-server

# Secure installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p <<EOF
CREATE DATABASE foundationos_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'foundationos'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON foundationos_prod.* TO 'foundationos'@'localhost';
FLUSH PRIVILEGES;
EXIT;
EOF

# Test connection
mysql -u foundationos -p foundationos_prod
```

### PostgreSQL 12+

```bash
# Install PostgreSQL
sudo apt install -y postgresql postgresql-contrib

# Create database and user
sudo -u postgres psql <<EOF
CREATE DATABASE foundationos_prod;
CREATE USER foundationos WITH ENCRYPTED PASSWORD 'strong_password_here';
GRANT ALL PRIVILEGES ON DATABASE foundationos_prod TO foundationos;
\q
EOF

# Test connection
psql -h localhost -U foundationos -d foundationos_prod
```

### Connection Pooling (Optional but Recommended)

```bash
# Install PgBouncer (untuk PostgreSQL)
sudo apt install -y pgbouncer

# Edit /etc/pgbouncer/pgbouncer.ini
[databases]
foundationos_prod = host=localhost dbname=foundationos_prod user=foundationos password=strong_password_here

[pgbouncer]
pool_mode = transaction
max_client_conn = 1000
default_pool_size = 25
min_pool_size = 10
```

---

## 📦 Application Deployment

### 1. Clone Repository

```bash
cd /var/www/foundationos

# Clone dari repository
git clone https://github.com/yusufbayuw/foundationOS.git .

# Checkout production branch (jika ada)
git checkout main
```

### 2. Environment Configuration

```bash
# Copy .env template
cp .env.example .env

# Edit .env dengan production settings
nano .env
```

**Key .env configurations untuk production**:

```env
APP_NAME="FoundationOS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.institution.edu

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=foundationos_prod
DB_USERNAME=foundationos
DB_PASSWORD=strong_password_here

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis (untuk cache, session, queue)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail configuration
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@institution.edu
MAIL_FROM_NAME="FoundationOS"

# Moodle Integration (opsional)
MOODLE_ENABLED=false
MOODLE_URL=https://moodle.institution.edu
MOODLE_TOKEN=your_moodle_token

# Security
APP_KEY=base64:your_app_key_will_be_generated
```

### 3. Generate App Key & Install Dependencies

```bash
# Generate Laravel app key
php artisan key:generate

# Install Composer dependencies
composer install --optimize-autoloader --no-dev

# Install npm dependencies
npm install

# Build frontend assets
npm run build
```

### 4. Database Setup

```bash
# Run migrations
php artisan migrate --force

# Run seeders (optional, untuk demo data)
php artisan db:seed --class=DatabaseSeeder

# Create storage link
php artisan storage:link

# Generate Filament Shield permissions
php artisan shield:generate --all --panel=admin --option=permissions --no-interaction
```

### 5. Permission Setup

```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/foundationos
chmod -R 755 /var/www/foundationos
chmod -R 775 /var/www/foundationos/storage
chmod -R 775 /var/www/foundationos/bootstrap/cache
chmod -R 775 /var/www/foundationos/public
```

---

## 🌐 Web Server Configuration

### Nginx Configuration

**File: `/etc/nginx/sites-available/foundationos`**

```nginx
server {
    listen 80;
    server_name app.institution.edu;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name app.institution.edu;

    # SSL Certificates
    ssl_certificate /etc/letsencrypt/live/app.institution.edu/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/app.institution.edu/privkey.pem;

    # SSL Security
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    root /var/www/foundationos/public;
    index index.php;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css text/js text/xml text/javascript application/javascript application/xml+rss;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;

    # PHP-FPM configuration
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PHP_VALUE "upload_max_filesize=100M \n post_max_size=100M";
    }

    # Block access to hidden files
    location ~ /\. {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

**Enable site**:

```bash
sudo ln -s /etc/nginx/sites-available/foundationos /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### SSL Certificate dengan Let's Encrypt

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain certificate
sudo certbot certonly --nginx -d app.institution.edu

# Auto-renewal (already set with systemd timer)
sudo systemctl status certbot.timer
```

---

## 🔄 Background Services

### Queue Worker Setup

```bash
# Install Supervisor (untuk manage queue worker)
sudo apt install -y supervisor

# Create supervisor config
sudo nano /etc/supervisor/conf.d/foundationos-queue.conf
```

**Config content**:

```ini
[program:foundationos-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/foundationos/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/foundationos-queue.log
stopwaitsecs=3600
```

**Start supervisor**:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start foundationos-queue:*
```

### Scheduled Tasks

Laravel scheduler otomatis dijalankan jika queue worker aktif. Tapi untuk extra assurance:

```bash
# Add to crontab
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/foundationos && php artisan schedule:run >> /dev/null 2>&1") | crontab -
```

### Redis Installation (untuk Queue & Caching)

```bash
# Install Redis
sudo apt install -y redis-server

# Start and enable
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Verify
redis-cli ping
# Output: PONG
```

---

## 🔒 Security Hardening

### 1. Firewall Rules

```bash
# Allow SSH (but limit access)
sudo ufw limit 22/tcp

# Allow HTTP & HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Block all other incoming
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Verify rules
sudo ufw status
```

### 2. SSH Hardening

**File: `/etc/ssh/sshd_config`**

```bash
# Edit SSH config
sudo nano /etc/ssh/sshd_config
```

**Changes**:

```
Port 22
PermitRootLogin no
PasswordAuthentication no
PubkeyAuthentication yes
AllowUsers foundationos
```

**Apply changes**:

```bash
sudo systemctl restart sshd
```

### 3. File Permissions

```bash
# Storage directory (readable by web server)
sudo chown -R www-data:www-data /var/www/foundationos/storage
sudo chmod -R 775 /var/www/foundationos/storage

# Bootstrap cache
sudo chmod -R 775 /var/www/foundationos/bootstrap/cache

# .env file (read-only by web server)
sudo chown www-data:www-data /var/www/foundationos/.env
sudo chmod 600 /var/www/foundationos/.env

# Public directory
sudo chmod -R 755 /var/www/foundationos/public
```

### 4. Database Backup User

```bash
# Create backup user dengan minimal privileges
sudo mysql -u root -p <<EOF
CREATE USER 'backup'@'localhost' IDENTIFIED BY 'backup_password';
GRANT SELECT, LOCK TABLES ON *.* TO 'backup'@'localhost';
FLUSH PRIVILEGES;
EOF
```

### 5. Environment File Security

```bash
# Ensure .env is not publicly accessible
sudo nano /etc/nginx/conf.d/security.conf
```

```nginx
location ~ /\.env {
    deny all;
}
```

---

## 📊 Monitoring & Logging

### 1. Application Logs

Logs tersimpan di `/var/www/foundationos/storage/logs/`:

```bash
# Monitor logs in real-time
tail -f /var/www/foundationos/storage/logs/laravel.log

# Or use Laravel Pail
php artisan pail
```

### 2. System Logging

```bash
# Check system logs
sudo journalctl -xe

# Check PHP-FPM logs
sudo tail -f /var/log/php8.4-fpm.log

# Check Nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

### 3. Monitoring Tools (Optional)

**Install htop**:

```bash
sudo apt install -y htop

# Monitor system resources
htop
```

**Setup error tracking with Sentry** (optional):

```bash
# Add to .env
SENTRY_LARAVEL_ENABLED=true
SENTRY_DSN=your_sentry_dsn_here

# Install package
composer require sentry/sentry-laravel
```

### 4. Health Check Endpoint

```bash
# Access health check
curl https://app.institution.edu/health

# Should return 200 OK with system status
```

---

## ⚡ Performance Optimization

### 1. Cache Configuration

Gunakan Redis untuk optimal performance:

```bash
# Install Redis (jika belum)
sudo apt install -y redis-server

# Test connection
redis-cli ping
```

Update `.env`:

```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 2. Database Optimization

```bash
# Create indexes untuk frequently queried columns
php artisan tinker

# Check slow queries
mysql -u root -p <<EOF
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;
SHOW VARIABLES LIKE '%slow%';
EOF
```

### 3. Asset Compilation & CDN

```bash
# Build assets for production (minified)
npm run build

# Serve from CDN (optional)
# Update APP_URL dalam .env untuk point ke CDN
```

### 4. PHP-FPM Tuning

**File: `/etc/php/8.4/fpm/pool.d/www.conf`**

```ini
; Increase children processes
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 35

; Opcache (built-in PHP caching)
opcache.enable = 1
opcache.memory_consumption = 256
opcache.validate_timestamps = 0
opcache.revalidate_freq = 0
```

**Restart PHP-FPM**:

```bash
sudo systemctl restart php8.4-fpm
```

---

## 💾 Backup & Disaster Recovery

### 1. Database Backup Strategy

**Manual backup**:

```bash
# MySQL
mysqldump -u foundationos -p foundationos_prod > /backups/db_$(date +%Y%m%d_%H%M%S).sql

# PostgreSQL
pg_dump -U foundationos foundationos_prod > /backups/db_$(date +%Y%m%d_%H%M%S).sql
```

**Automated backup dengan cron**:

```bash
# Add to crontab (daily at 2 AM)
0 2 * * * mysqldump -u backup -p'backup_password' foundationos_prod > /backups/db_$(date +\%Y\%m\%d).sql && find /backups -name "db_*.sql" -mtime +7 -delete
```

### 2. File Backup

```bash
# Backup storage directory
tar -czf /backups/storage_$(date +%Y%m%d).tar.gz /var/www/foundationos/storage/

# Setup automatic cleanup (keep 7 days)
find /backups -name "storage_*.tar.gz" -mtime +7 -delete
```

### 3. Backup Storage

```bash
# Create dedicated backup partition or use cloud storage
sudo mkdir -p /backups
sudo chmod 700 /backups

# Or use S3/Cloud Storage
# Install AWS CLI for S3 backup
aws s3 sync /backups s3://your-bucket/foundationos-backups --delete
```

### 4. Recovery Procedure

**Database recovery**:

```bash
# MySQL
mysql -u foundationos -p foundationos_prod < /backups/db_backup.sql

# PostgreSQL
psql -U foundationos foundationos_prod < /backups/db_backup.sql
```

**File recovery**:

```bash
# Stop application
sudo systemctl stop php8.4-fpm

# Restore storage
tar -xzf /backups/storage_backup.tar.gz -C /

# Restart
sudo systemctl start php8.4-fpm
```

---

## ✅ Post-Deployment Checklist

### Verifikasi Aplikasi

- [ ] Access admin panel: `https://app.institution.edu/admin`
- [ ] Login dengan super admin credentials
- [ ] Check system health: `php artisan tinker` → `App\Models\User::count()`
- [ ] Queue worker running: `sudo supervisorctl status`
- [ ] Redis accessible: `redis-cli ping` → PONG
- [ ] Database migrations applied: `php artisan migrate:status`
- [ ] File uploads working: Upload file di panel admin
- [ ] Email working: Test send email

### Security Verification

- [ ] SSL certificate valid: `openssl s_client -connect app.institution.edu:443`
- [ ] Firewall rules applied: `sudo ufw status`
- [ ] .env file not publicly accessible
- [ ] Database backups scheduled
- [ ] Error logs monitored
- [ ] SSH hardened (no root login)
- [ ] File permissions correct

### Performance Verification

- [ ] Page load time < 2 seconds
- [ ] Cache working: `redis-cli --stat`
- [ ] Queue processing: Check supervisor status
- [ ] Database queries optimized: Check slow query log
- [ ] Assets minified: Check inspector network tab
- [ ] Gzip enabled: Response headers show `Content-Encoding: gzip`

### Monitoring Setup

- [ ] Application logs configured
- [ ] System monitoring tools installed
- [ ] Uptime monitoring enabled
- [ ] Error tracking (Sentry) configured
- [ ] Log rotation setup
- [ ] Backup verification passed

### Documentation

- [ ] Production .env backed up securely
- [ ] Database credentials stored securely
- [ ] Backup procedures documented
- [ ] Team members trained on deployment
- [ ] Runbooks created for common tasks
- [ ] Incident response plan in place

---

## 🆘 Troubleshooting

### Error: "Disk space full"

```bash
# Check disk usage
df -h

# Find large files
du -sh /var/www/foundationos/*

# Clean up old logs
rm -f /var/www/foundationos/storage/logs/*.log
rm -f /var/log/nginx/*.log.*
```

### Error: "Queue jobs not processing"

```bash
# Check supervisor status
sudo supervisorctl status foundationos-queue:*

# Restart queue workers
sudo supervisorctl restart foundationos-queue:*

# Check Redis connection
redis-cli ping
```

### Error: "Database connection refused"

```bash
# Check MySQL service
sudo systemctl status mysql

# Verify database credentials in .env
mysql -u foundationos -p -h localhost foundationos_prod -e "SELECT 1"

# Check firewall
sudo ufw status | grep 3306
```

### Error: "PHP-FPM connection timeout"

```bash
# Check PHP-FPM status
sudo systemctl status php8.4-fpm

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm

# Increase timeout in nginx config
fastcgi_connect_timeout 60s;
fastcgi_send_timeout 60s;
fastcgi_read_timeout 60s;
```

---

## 📞 Support & Further Help

- 📖 [README.md](./README.md) - Platform overview
- 📖 [ONBOARDING.md](./ONBOARDING.md) - System configuration
- 📖 [ROADMAP.md](./ROADMAP.md) - Development roadmap
- 📖 [WORKFLOW.md](./WORKFLOW.md) - Workflow engine
- 📖 [MOODLE.md](./MOODLE.md) - Moodle integration

---

**Last Updated**: 2026-05-23
**Version**: Production v1.0
**Status**: ✅ Ready for deployment
