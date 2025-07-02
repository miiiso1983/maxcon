# ☁️ دليل النشر على Cloudways - نظام إدارة الصيدلية

## 🌟 مميزات Cloudways
- **خوادم سحابية عالية الأداء** (AWS, Google Cloud, DigitalOcean)
- **SSL مجاني** مع Let's Encrypt
- **نسخ احتياطية تلقائية**
- **CDN مدمج**
- **مراقبة الأداء**
- **Git Deployment** المدمج

## 📋 المتطلبات الأساسية

### 1. حساب Cloudways
- خطة استضافة مناسبة (يُنصح بـ DigitalOcean أو AWS)
- خادم بـ PHP 8.1+ و MySQL 8.0+
- مساحة تخزين كافية (10GB على الأقل)

### 2. إعدادات الخادم المطلوبة
```
PHP Version: 8.1+
MySQL Version: 8.0+
Memory Limit: 512MB+
Max Execution Time: 300s
Upload Max Size: 100MB
```

## 🚀 خطوات النشر

### الخطوة 1: إعداد الخادم في Cloudways

1. **إنشاء خادم جديد**
   - اختر مزود الخدمة (DigitalOcean مُوصى به)
   - اختر حجم الخادم (2GB RAM على الأقل)
   - اختر الموقع الجغرافي الأقرب

2. **إعداد التطبيق**
   - اختر "PHP" كنوع التطبيق
   - اختر إصدار PHP 8.1+
   - حدد اسم التطبيق: "pharmacy-erp"

3. **إعداد قاعدة البيانات**
   - اسم قاعدة البيانات: `pharmacy_erp`
   - اسم المستخدم: سيتم إنشاؤه تلقائياً
   - كلمة المرور: سيتم إنشاؤها تلقائياً

### الخطوة 2: تحضير المشروع محلياً

1. **تشغيل سكريبت التحضير**
   ```bash
   ./prepare-for-cloudways.sh
   ```

2. **التحقق من الملفات**
   - ملف `.env.cloudways` محدث
   - ملف `composer.json` محسن
   - مجلد `public` جاهز

### الخطوة 3: رفع المشروع

#### الطريقة 1: Git Deployment (مُوصى بها)

1. **إعداد Git Repository**
   ```bash
   git init
   git add .
   git commit -m "Initial commit for Cloudways deployment"
   git remote add origin YOUR_REPO_URL
   git push -u origin main
   ```

2. **ربط Git في Cloudways**
   - اذهب إلى تبويب "Git Deployment"
   - أدخل رابط المستودع
   - اختر الفرع `main`
   - فعل "Auto Deploy"

#### الطريقة 2: رفع مباشر عبر SFTP

1. **الحصول على بيانات SFTP**
   - من لوحة تحكم Cloudways
   - تبويب "Server Management" > "Master Credentials"

2. **رفع الملفات**
   ```bash
   # استخدام rsync
   rsync -avz --exclude 'node_modules' --exclude '.git' ./ user@server:/applications/app_name/public_html/
   ```

### الخطوة 4: إعداد قاعدة البيانات

1. **الحصول على بيانات قاعدة البيانات**
   - من تبويب "Application Management"
   - انسخ: Host, Database Name, Username, Password

2. **تحديث ملف .env**
   ```env
   DB_HOST=your_db_host
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```

### الخطوة 5: تشغيل الأوامر

1. **الوصول إلى SSH Terminal**
   ```bash
   ssh user@your-server-ip
   cd applications/app_name/public_html
   ```

2. **تثبيت Dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **تشغيل أوامر Laravel**
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan storage:link
   ```

4. **ضبط الصلاحيات**
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

## ⚙️ إعدادات Cloudways المتقدمة

### 1. تحسين PHP
```ini
# في PHP Settings
memory_limit = 512M
max_execution_time = 300
upload_max_filesize = 100M
post_max_size = 100M
max_input_vars = 3000
```

### 2. إعداد Cron Jobs
```bash
# إضافة في Cron Job Management
* * * * * cd /applications/app_name/public_html && php artisan schedule:run >> /dev/null 2>&1
```

### 3. تفعيل Redis (اختياري)
```env
# في .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 4. إعداد SSL
- اذهب إلى "SSL Certificate"
- اختر "Let's Encrypt"
- أدخل اسم النطاق
- انقر "Install Certificate"

## 🔧 ملفات التكوين المخصصة

### 1. ملف .env.cloudways
```env
APP_NAME="نظام إدارة الصيدلية"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database (سيتم تحديثها من Cloudways)
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Cache & Sessions
CACHE_DRIVER=file
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Mail (Cloudways SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_username
MAIL_PASSWORD=your_mailgun_password
MAIL_ENCRYPTION=tls

# Security
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
```

### 2. ملف composer.json محسن
```json
{
    "require": {
        "php": "^8.1",
        "laravel/framework": "^11.0"
    },
    "scripts": {
        "post-install-cmd": [
            "php artisan clear-compiled",
            "php artisan optimize"
        ],
        "post-update-cmd": [
            "php artisan clear-compiled",
            "php artisan optimize"
        ]
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true
    }
}
```

## 🔍 استكشاف الأخطاء

### مشاكل شائعة وحلولها

#### 1. خطأ 500 - Internal Server Error
```bash
# تحقق من logs
tail -f storage/logs/laravel.log

# تنظيف الكاش
php artisan config:clear
php artisan cache:clear
```

#### 2. مشاكل قاعدة البيانات
```bash
# اختبار الاتصال
php artisan tinker
DB::connection()->getPdo();
```

#### 3. مشاكل الصلاحيات
```bash
# إصلاح الصلاحيات
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
```

#### 4. مشاكل Composer
```bash
# إعادة تثبيت
rm -rf vendor
composer install --no-dev
```

## 📊 مراقبة الأداء

### 1. استخدام Cloudways Monitoring
- CPU Usage
- Memory Usage
- Disk Usage
- Database Performance

### 2. Laravel Telescope (للتطوير)
```bash
composer require laravel/telescope --dev
php artisan telescope:install
```

### 3. New Relic Integration
- متوفر في Cloudways
- مراقبة شاملة للأداء

## 🔐 الأمان

### 1. إعدادات الأمان الأساسية
```env
APP_DEBUG=false
APP_ENV=production
SESSION_SECURE_COOKIE=true
```

### 2. Firewall Rules
- السماح فقط للمنافذ المطلوبة
- حظر IP المشبوهة

### 3. نسخ احتياطية منتظمة
- تفعيل النسخ الاحتياطية التلقائية
- جدولة النسخ الاحتياطية يومياً

## 📞 الدعم والمساعدة

### روابط مفيدة
- [وثائق Cloudways](https://support.cloudways.com)
- [مجتمع Cloudways](https://community.cloudways.com)
- [دعم Laravel](https://laravel.com/docs)

### أدوات المساعدة
- `cloudways-check.php` - فحص شامل للنشر
- `cloudways-optimize.php` - تحسين الأداء
- `cloudways-backup.php` - إدارة النسخ الاحتياطية

---

**ملاحظة:** Cloudways يوفر بيئة استضافة متقدمة مع أدوات إدارة قوية. تأكد من استغلال جميع المميزات المتاحة لتحسين أداء وأمان موقعك.
