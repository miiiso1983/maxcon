# ☁️ نظام إدارة الصيدلية - النشر على Cloudways

## 🌟 نظرة عامة

نظام إدارة الصيدلية المتكامل مع دعم متعدد المستأجرين، مصمم خصيصاً للنشر على منصة Cloudways السحابية.

### المميزات الرئيسية:
- 🏢 **نظام متعدد المستأجرين** مع عزل كامل للبيانات
- 🔐 **نظام أمان متقدم** مع RBAC
- 📊 **تقارير شاملة** ولوحات تحكم تفاعلية
- 📱 **واجهة متجاوبة** تعمل على جميع الأجهزة
- 🌍 **دعم متعدد اللغات** (العربية والإنجليزية)
- ☁️ **محسن لـ Cloudways** مع أفضل الممارسات

## 🚀 النشر السريع على Cloudways

### الخطوة 1: تحضير المشروع
```bash
# تشغيل سكريبت التحضير
./prepare-for-cloudways.sh

# أو تحضير يدوي
cp .env.cloudways .env
composer install --no-dev --optimize-autoloader
php artisan optimize
```

### الخطوة 2: إنشاء خادم Cloudways
1. سجل دخول إلى [Cloudways](https://www.cloudways.com)
2. انقر "Launch Server"
3. اختر:
   - **Cloud Provider:** DigitalOcean (مُوصى به)
   - **Server Size:** 2GB RAM (الحد الأدنى)
   - **Location:** الأقرب لجمهورك
   - **Application:** PHP 8.1+

### الخطوة 3: رفع المشروع

#### الطريقة الأولى: Git Deployment (مُوصى بها)
```bash
# إعداد Git
./cloudways-git-deploy.sh

# في Cloudways Panel:
# 1. اذهب إلى Git Deployment
# 2. أدخل رابط Repository
# 3. اختر الفرع main
# 4. فعل Auto Deploy
```

#### الطريقة الثانية: SFTP
```bash
# استخدام rsync
rsync -avz --exclude 'node_modules' --exclude '.git' ./ user@server:/applications/app_name/public_html/
```

### الخطوة 4: إعداد قاعدة البيانات
1. احصل على بيانات قاعدة البيانات من Cloudways Panel
2. حدث ملف `.env`:
```env
DB_HOST=your_cloudways_db_host
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### الخطوة 5: تشغيل الأوامر
```bash
# SSH إلى الخادم
ssh user@your-server-ip
cd applications/app_name/public_html

# تثبيت Dependencies
composer install --no-dev --optimize-autoloader

# إعداد Laravel
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link

# تحسين للإنتاج
php artisan optimize
chmod -R 755 storage bootstrap/cache
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
opcache.enable = 1
opcache.memory_consumption = 256
```

### 2. إعداد SSL
- اذهب إلى "SSL Certificate"
- اختر "Let's Encrypt"
- أدخل اسم النطاق
- انقر "Install Certificate"

### 3. تفعيل CDN
- اذهب إلى "CloudwaysCDN"
- فعل CDN لتسريع الموقع
- اختر المناطق المطلوبة

### 4. إعداد النسخ الاحتياطية
- اذهب إلى "Backups"
- فعل النسخ الاحتياطية التلقائية
- اختر التوقيت المناسب

### 5. مراقبة الأداء
- استخدم "Monitoring" لمراقبة الموارد
- فعل التنبيهات للمشاكل
- راجع التقارير بانتظام

## 🔧 أدوات الصيانة

### فحص النشر
```
https://yourdomain.com/cloudways-check.php
```

### تحسين الأداء
```
https://yourdomain.com/cloudways-optimize.php
```

### أوامر الصيانة
```bash
# تنظيف شامل
php artisan optimize:clear

# تحسين للإنتاج
php artisan optimize

# إعادة تشغيل الطوابير
php artisan queue:restart

# فحص الصحة
php artisan about
```

## 🔐 بيانات تسجيل الدخول الافتراضية

### السوبر أدمن
- **الرابط:** `https://yourdomain.com/super-admin/login`
- **البريد:** `superadmin@pharmacy-erp.com`
- **كلمة المرور:** `SuperAdmin@2024`

### المستخدم العادي
- **الرابط:** `https://yourdomain.com/login`
- **البريد:** `atheer@rama.com`
- **كلمة المرور:** `Manager@2024`

## 📊 مراقبة النظام

### مؤشرات الأداء المهمة
- **استخدام CPU:** يجب أن يكون أقل من 80%
- **استخدام الذاكرة:** يجب أن يكون أقل من 85%
- **مساحة القرص:** يجب أن تكون أكثر من 20% متاحة
- **وقت الاستجابة:** يجب أن يكون أقل من 2 ثانية

### تنبيهات مهمة
- مراقبة استخدام قاعدة البيانات
- مراقبة حجم ملفات السجلات
- مراقبة النسخ الاحتياطية
- مراقبة انتهاء صلاحية SSL

## 🛠️ استكشاف الأخطاء

### مشاكل شائعة وحلولها

#### خطأ 500 - Internal Server Error
```bash
# فحص السجلات
tail -f storage/logs/laravel.log

# تنظيف الكاش
php artisan optimize:clear
```

#### مشاكل قاعدة البيانات
```bash
# اختبار الاتصال
php artisan tinker
DB::connection()->getPdo();

# إعادة تشغيل MySQL
# من Cloudways Panel > Services
```

#### مشاكل الصلاحيات
```bash
# إصلاح الصلاحيات
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### مشاكل Composer
```bash
# إعادة تثبيت
rm -rf vendor composer.lock
composer install --no-dev
```

## 📈 تحسين الأداء

### 1. استخدام Redis
```env
# في .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 2. تحسين قاعدة البيانات
```sql
-- فهرسة الجداول المهمة
CREATE INDEX idx_tenant_id ON users(tenant_id);
CREATE INDEX idx_created_at ON orders(created_at);
```

### 3. ضغط الاستجابات
```apache
# في .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript
</IfModule>
```

## 🔄 التحديثات والصيانة

### تحديث النظام
```bash
# سحب آخر التحديثات
git pull origin main

# تحديث Dependencies
composer update --no-dev

# تشغيل المايجريشن
php artisan migrate --force

# تحسين
php artisan optimize
```

### صيانة دورية
- **يومياً:** مراجعة السجلات والأداء
- **أسبوعياً:** تحديث النظام والمكتبات
- **شهرياً:** مراجعة النسخ الاحتياطية والأمان
- **ربع سنوياً:** تحديث PHP وMySQL

## 📞 الدعم والمساعدة

### الوثائق
- [دليل النشر الشامل](CLOUDWAYS-DEPLOYMENT-GUIDE.md)
- [إعداد Git](CLOUDWAYS-GIT-SETUP.md)
- [معلومات النشر](cloudways-deployment-info.txt)

### أدوات المساعدة
- `cloudways-check.php` - فحص شامل للنشر
- `cloudways-optimize.php` - تحسين الأداء
- `deploy-hook.php` - webhook للنشر التلقائي

### روابط مفيدة
- [دعم Cloudways](https://support.cloudways.com)
- [مجتمع Cloudways](https://community.cloudways.com)
- [وثائق Laravel](https://laravel.com/docs)

---

## 📝 ملاحظات مهمة

1. **تأكد من تحديث إعدادات قاعدة البيانات** في ملف `.env`
2. **فعل SSL Certificate** فور النشر
3. **إعداد النسخ الاحتياطية التلقائية**
4. **مراقبة الأداء باستمرار**
5. **تحديث النظام بانتظام**

**تم تحضير هذا المشروع خصيصاً للنشر على Cloudways مع أفضل الممارسات والتحسينات.**
