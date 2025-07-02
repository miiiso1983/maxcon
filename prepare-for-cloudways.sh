#!/bin/bash

# تحضير المشروع للنشر على Cloudways
echo "☁️ تحضير المشروع للنشر على Cloudways..."

# 1. إنشاء ملف .env.cloudways
echo "📋 إنشاء ملف .env.cloudways..."
cat > .env.cloudways << 'EOF'
APP_NAME="نظام إدارة الصيدلية"
APP_ENV=production
APP_KEY=base64:QKyZoyATcjBxA0qzfcTUPrsxush+g+1ASMVMxxjXcwk=
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_TIMEZONE=Asia/Baghdad

APP_LOCALE=ar
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

# Database Configuration (يجب تحديثها من Cloudways)
DB_CONNECTION=mysql
DB_HOST=your_cloudways_db_host
DB_PORT=3306
DB_DATABASE=pharmacy_erp
DB_USERNAME=your_cloudways_db_user
DB_PASSWORD=your_cloudways_db_password

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.yourdomain.com
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# Cache Configuration
CACHE_STORE=file
CACHE_PREFIX=pharmacy_erp

# Queue Configuration
QUEUE_CONNECTION=database

# Mail Configuration (Cloudways/Mailgun)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_username
MAIL_PASSWORD=your_mailgun_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
MAIL_ADMIN_EMAIL="admin@yourdomain.com"

# Security Settings
SECURITY_ALERT_EMAIL=admin@yourdomain.com
SESSION_SECURE_COOKIE=true
API_REQUIRE_HTTPS=true
ENABLE_TWO_FACTOR=true
PASSWORD_EXPIRY_DAYS=90
MAX_LOGIN_ATTEMPTS=5
LOGIN_LOCKOUT_MINUTES=15

# Performance Settings
CACHE_DEFAULT_TTL=3600
ENABLE_QUERY_CACHE=true
SLOW_QUERY_THRESHOLD=1000
ENABLE_PERFORMANCE_MONITORING=true

# File Upload Settings
MAX_UPLOAD_SIZE=10240
ALLOWED_FILE_EXTENSIONS=jpg,jpeg,png,pdf,doc,docx,xls,xlsx
ENABLE_VIRUS_SCAN=false

# Backup Settings
BACKUP_ENABLED=true
BACKUP_SCHEDULE="0 2 * * *"
BACKUP_RETENTION_DAYS=30
BACKUP_DISK=local

# Notification Settings
NOTIFICATION_CHANNELS=database,mail
ENABLE_EMAIL_NOTIFICATIONS=true
ENABLE_SMS_NOTIFICATIONS=false

# API Settings
API_RATE_LIMIT=60
API_TOKEN_EXPIRY=1440
API_REFRESH_TOKEN_EXPIRY=43200

# Currency Settings
DEFAULT_CURRENCY=IQD
CURRENCY_SYMBOL="د.ع"
CURRENCY_DECIMAL_PLACES=2

# Inventory Settings
INVENTORY_LOW_STOCK_THRESHOLD=10
INVENTORY_EXPIRY_WARNING_DAYS=30
INVENTORY_AUTO_REORDER=false

# Company Information
COMPANY_NAME="شركة الأدوية التجارية"
COMPANY_NAME_EN="Commercial Pharmacy Company"
COMPANY_ADDRESS="بغداد - العراق"
COMPANY_PHONE="+964 770 123 4567"
COMPANY_EMAIL="info@yourdomain.com"
COMPANY_WEBSITE="www.yourdomain.com"
COMPANY_TAX_NUMBER="123456789"
COMPANY_COMMERCIAL_REGISTER="CR-123456"

# WhatsApp Business API (اختياري)
WHATSAPP_API_URL=https://graph.facebook.com/v18.0
WHATSAPP_ACCESS_TOKEN=your_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id_here
WHATSAPP_BUSINESS_ACCOUNT_ID=your_business_account_id_here
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_webhook_verify_token_here
WHATSAPP_AUTO_SEND_COLLECTION=true
WHATSAPP_AUTO_SEND_PAYMENT=true
WHATSAPP_AUTO_SEND_INVOICE=false
WHATSAPP_LOG_MESSAGES=true

# Production Settings
ENABLE_DEBUG_TOOLBAR=false
ENABLE_TELESCOPE=false
LOG_QUERIES=false
LOG_CHANNEL=daily
LOG_LEVEL=error
EOF

echo "✅ تم إنشاء ملف .env.cloudways"

# 2. تحديث composer.json للإنتاج
echo "📦 تحديث composer.json للإنتاج..."
cat > composer.json << 'EOF'
{
    "name": "pharmacy/erp-system",
    "type": "project",
    "description": "نظام إدارة الصيدلية المتكامل",
    "keywords": ["laravel", "pharmacy", "erp", "management"],
    "license": "MIT",
    "require": {
        "php": "^8.1",
        "laravel/framework": "^11.0",
        "laravel/tinker": "^2.9",
        "spatie/laravel-permission": "^6.0",
        "barryvdh/laravel-dompdf": "^2.0",
        "maatwebsite/excel": "^3.1",
        "intervention/image": "^3.0"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "laravel/pint": "^1.13",
        "laravel/sail": "^1.26",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.0",
        "phpunit/phpunit": "^11.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi",
            "@php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\"",
            "@php artisan migrate --graceful --ansi"
        ],
        "post-install-cmd": [
            "php artisan clear-compiled",
            "php artisan optimize"
        ],
        "cloudways-deploy": [
            "composer install --no-dev --optimize-autoloader",
            "php artisan config:cache",
            "php artisan route:cache",
            "php artisan view:cache"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
EOF

echo "✅ تم تحديث composer.json"

# 3. إنشاء ملف .htaccess محسن لـ Cloudways
echo "🔧 إنشاء ملف .htaccess محسن..."
cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https:; connect-src 'self';"
</IfModule>

# Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/json
</IfModule>

# Browser Caching
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType text/javascript "access plus 1 year"
    ExpiresByType application/x-shockwave-flash "access plus 1 year"
    ExpiresByType image/x-icon "access plus 1 year"
    ExpiresByType text/html "access plus 1 hour"
</IfModule>

# Protect sensitive files
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.lock">
    Order allow,deny
    Deny from all
</Files>

<Files "*.log">
    Order allow,deny
    Deny from all
</Files>
EOF

echo "✅ تم إنشاء ملف .htaccess محسن"

# 4. إنشاء مجلدات مطلوبة
echo "📁 إنشاء المجلدات المطلوبة..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache
mkdir -p public/storage

echo "✅ تم إنشاء المجلدات المطلوبة"

# 5. ضبط الصلاحيات
echo "🔐 ضبط الصلاحيات..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env.cloudways

echo "✅ تم ضبط الصلاحيات"

# 6. تنظيف وتحسين
echo "🧹 تنظيف وتحسين..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "✅ تم التنظيف"

# 7. إنشاء ملف معلومات النشر
echo "📄 إنشاء ملف معلومات النشر..."
cat > cloudways-deployment-info.txt << EOF
=== معلومات النشر على Cloudways ===
تاريخ التحضير: $(date)
إصدار PHP: $(php -v | head -n 1)
إصدار Laravel: $(php artisan --version)

=== متطلبات الخادم ===
- PHP 8.1+
- MySQL 8.0+
- Memory Limit: 512MB+
- Max Execution Time: 300s
- Upload Max Size: 100MB

=== الملفات المحضرة ===
✅ .env.cloudways - إعدادات الإنتاج
✅ composer.json - محسن للإنتاج
✅ public/.htaccess - أمان وأداء محسن
✅ المجلدات المطلوبة
✅ الصلاحيات مضبوطة

=== خطوات النشر على Cloudways ===
1. إنشاء خادم جديد في Cloudways
2. إعداد التطبيق (PHP 8.1+)
3. رفع الملفات عبر Git أو SFTP
4. تحديث .env بمعلومات قاعدة البيانات
5. تشغيل الأوامر المطلوبة
6. إعداد SSL Certificate
7. تفعيل النسخ الاحتياطية

=== الأوامر المطلوبة على الخادم ===
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
chmod -R 755 storage bootstrap/cache

=== إعدادات قاعدة البيانات ===
يجب تحديث هذه القيم في .env:
DB_HOST=من_cloudways_panel
DB_DATABASE=pharmacy_erp
DB_USERNAME=من_cloudways_panel
DB_PASSWORD=من_cloudways_panel

=== روابط مهمة بعد النشر ===
- الموقع الرئيسي: https://yourdomain.com
- تسجيل الدخول: https://yourdomain.com/login
- لوحة السوبر أدمن: https://yourdomain.com/super-admin/login
- فحص النشر: https://yourdomain.com/cloudways-check.php

=== بيانات تسجيل الدخول الافتراضية ===
السوبر أدمن:
- البريد: superadmin@pharmacy-erp.com
- كلمة المرور: SuperAdmin@2024

المستخدم العادي:
- البريد: atheer@rama.com
- كلمة المرور: Manager@2024

=== نصائح مهمة ===
- استخدم Git Deployment للتحديثات التلقائية
- فعل النسخ الاحتياطية التلقائية
- راقب الأداء باستمرار
- استخدم CDN لتحسين السرعة
- فعل SSL Certificate
EOF

echo "✅ تم إنشاء ملف cloudways-deployment-info.txt"

# 8. عرض ملخص التحضير
echo ""
echo "🎉 تم الانتهاء من تحضير المشروع لـ Cloudways!"
echo ""
echo "📋 الملفات المحضرة:"
echo "✅ .env.cloudways - إعدادات الإنتاج"
echo "✅ composer.json - محسن للأداء"
echo "✅ public/.htaccess - أمان محسن"
echo "✅ cloudways-deployment-info.txt - دليل النشر"
echo ""
echo "🚀 الخطوات التالية:"
echo "1. إنشاء خادم في Cloudways"
echo "2. رفع المشروع عبر Git أو SFTP"
echo "3. تحديث إعدادات قاعدة البيانات"
echo "4. تشغيل أوامر Laravel"
echo "5. إعداد SSL وCDN"
echo ""
echo "📖 راجع ملف CLOUDWAYS-DEPLOYMENT-GUIDE.md للتفاصيل الكاملة"
echo ""
