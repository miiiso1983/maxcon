# 🚀 دليل النشر على Hostinger - نظام إدارة الصيدلية

## 📋 المتطلبات الأساسية

### 1. حساب Hostinger
- خطة استضافة تدعم PHP 8.1+
- قاعدة بيانات MySQL
- SSL Certificate (مجاني مع Hostinger)

### 2. الملفات المطلوبة
- جميع ملفات المشروع
- ملف `.env.hostinger` (محدث بالإعدادات الصحيحة)
- مجلد `vendor` (أو إمكانية تشغيل composer)

## 🔧 خطوات النشر

### الخطوة 1: إعداد قاعدة البيانات

1. **دخول لوحة تحكم Hostinger**
   - اذهب إلى قسم "Databases"
   - انقر على "Create Database"

2. **إنشاء قاعدة البيانات**
   ```
   Database Name: u123456789_pharmacy_erp
   Username: u123456789_pharmacy
   Password: [كلمة مرور قوية]
   ```

3. **حفظ المعلومات**
   - احفظ اسم قاعدة البيانات
   - احفظ اسم المستخدم
   - احفظ كلمة المرور

### الخطوة 2: تحديث ملف .env

1. **نسخ الملف**
   ```bash
   cp .env.hostinger .env
   ```

2. **تحديث إعدادات قاعدة البيانات**
   ```env
   DB_HOST=localhost
   DB_DATABASE=u123456789_pharmacy_erp
   DB_USERNAME=u123456789_pharmacy
   DB_PASSWORD=YourActualPassword
   ```

3. **تحديث عنوان الموقع**
   ```env
   APP_URL=https://yourdomain.com
   ```

4. **تحديث إعدادات البريد**
   ```env
   MAIL_HOST=smtp.hostinger.com
   MAIL_USERNAME=noreply@yourdomain.com
   MAIL_PASSWORD=YourEmailPassword
   ```

### الخطوة 3: رفع الملفات

1. **استخدام File Manager أو FTP**
   - ارفع جميع الملفات إلى `public_html`
   - تأكد من رفع الملفات المخفية (مثل .env)

2. **الملفات المهمة**
   ```
   ✅ index.php
   ✅ .env
   ✅ .htaccess
   ✅ مجلد app/
   ✅ مجلد config/
   ✅ مجلد database/
   ✅ مجلد resources/
   ✅ مجلد routes/
   ✅ مجلد storage/
   ✅ مجلد vendor/ (أو composer.json)
   ```

### الخطوة 4: تشغيل الأوامر

1. **الوصول إلى Terminal (إذا متوفر)**
   ```bash
   cd public_html
   ```

2. **تثبيت Dependencies (إذا لم ترفع vendor)**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **تشغيل أوامر Laravel**
   ```bash
   php artisan key:generate --force
   php artisan migrate --force
   php artisan db:seed --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **ضبط الصلاحيات**
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   chmod 644 .env
   ```

### الخطوة 5: اختبار النشر

1. **زيارة الروابط التالية:**
   - `https://yourdomain.com/quick-check.php`
   - `https://yourdomain.com/test-hostinger-connection.php`
   - `https://yourdomain.com/login`

2. **تسجيل الدخول**
   ```
   السوبر أدمن:
   البريد: superadmin@pharmacy-erp.com
   كلمة المرور: SuperAdmin@2024

   المستخدم العادي:
   البريد: atheer@rama.com
   كلمة المرور: Manager@2024
   ```

## 🔍 استكشاف الأخطاء

### خطأ 500 - Internal Server Error
```bash
# تحقق من logs
tail -f storage/logs/laravel.log

# تنظيف الكاش
php artisan config:clear
php artisan cache:clear
```

### خطأ قاعدة البيانات
```bash
# اختبار الاتصال
php test-hostinger-connection.php

# تحقق من إعدادات .env
cat .env | grep DB_
```

### خطأ 419 - CSRF Token
```bash
# تنظيف الجلسات
php artisan session:table
php artisan migrate

# تحديث CSRF
php fix-csrf-final.php
```

### خطأ الصلاحيات
```bash
# إصلاح الصلاحيات
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage
```

## 📁 هيكل الملفات على Hostinger

```
public_html/
├── index.php                 # نقطة الدخول
├── .env                      # إعدادات التطبيق
├── .htaccess                 # إعدادات Apache
├── app/                      # كود التطبيق
├── bootstrap/                # ملفات البدء
├── config/                   # ملفات الإعدادات
├── database/                 # قاعدة البيانات والمايجريشن
├── public/                   # الملفات العامة
├── resources/                # الموارد (views, assets)
├── routes/                   # ملفات التوجيه
├── storage/                  # ملفات التخزين
├── vendor/                   # مكتبات PHP
├── quick-check.php           # فحص سريع
└── test-hostinger-connection.php  # اختبار قاعدة البيانات
```

## 🔐 الأمان

### 1. حماية الملفات الحساسة
```apache
# في .htaccess
<Files ".env">
    Order allow,deny
    Deny from all
</Files>
```

### 2. تفعيل HTTPS
```env
# في .env
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true
```

### 3. إخفاء معلومات الخادم
```env
APP_DEBUG=false
APP_ENV=production
```

## 📞 الدعم

### روابط مفيدة
- [وثائق Hostinger](https://support.hostinger.com)
- [وثائق Laravel](https://laravel.com/docs)
- [دليل استكشاف الأخطاء](./troubleshooting.md)

### ملفات المساعدة
- `quick-check.php` - فحص سريع للنشر
- `test-hostinger-connection.php` - اختبار قاعدة البيانات
- `fix-csrf-final.php` - حل مشاكل CSRF
- `deployment-info.txt` - معلومات النشر

## ✅ قائمة التحقق النهائية

- [ ] تم إنشاء قاعدة البيانات في Hostinger
- [ ] تم تحديث ملف .env بالإعدادات الصحيحة
- [ ] تم رفع جميع الملفات إلى public_html
- [ ] تم تشغيل أوامر Laravel
- [ ] تم ضبط الصلاحيات
- [ ] تم اختبار الموقع
- [ ] تم تسجيل الدخول بنجاح
- [ ] تم تفعيل HTTPS
- [ ] تم اختبار جميع الوظائف الأساسية

---

**ملاحظة:** هذا الدليل مخصص لنشر نظام إدارة الصيدلية على استضافة Hostinger. تأكد من اتباع جميع الخطوات بعناية.
