# دليل نشر النظام على خادم الإنتاج

## 🚀 خطوات النشر على Hostinger

### 1. رفع الملفات
```bash
# ارفع جميع ملفات المشروع إلى public_html
# تأكد من رفع:
- جميع ملفات Laravel
- مجلد vendor (أو شغل composer install)
- ملف .env (منسوخ من .env.hostinger)
```

### 2. إعدادات الصلاحيات
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env
```

### 3. تشغيل الأوامر المطلوبة
```bash
# في terminal الخادم أو عبر SSH
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan db:seed --class=MultiTenantSystemSeeder --force
```

### 4. إعدادات .htaccess
تأكد من وجود ملف .htaccess في public_html:
```apache
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
```

### 5. إعدادات قاعدة البيانات
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_pharmacy_erp
DB_USERNAME=u123456789_pharmacy
DB_PASSWORD=YourStrongPassword123!
```

### 6. إعدادات البريد الإلكتروني
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=YourEmailPassword123!
MAIL_ENCRYPTION=tls
```

### 7. إعدادات الأمان
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true
API_REQUIRE_HTTPS=true
```

## 🔧 حل مشاكل شائعة

### خطأ 500 Internal Server Error
1. تحقق من ملف .env
2. تأكد من صلاحيات المجلدات
3. شغل: php artisan config:clear
4. تحقق من logs: storage/logs/laravel.log

### خطأ قاعدة البيانات
1. تأكد من بيانات الاتصال في .env
2. تأكد من إنشاء قاعدة البيانات في hPanel
3. شغل: php artisan migrate --force

### خطأ الصلاحيات
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

## 📞 بيانات تسجيل الدخول

### Super Admins
- superadmin@pharmacy-erp.com / SuperAdmin@2024
- admin@pharmacy-erp.com / Admin@2024
- support@pharmacy-erp.com / Support@2024

### Tenant Users
- atheer@rama.com / 123456
- info@alshifa-pharmacy.com / 123456
