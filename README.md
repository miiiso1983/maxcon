# 🏥 نظام إدارة الصيدليات - Pharmacy ERP System

نظام شامل لإدارة الصيدليات والمؤسسات الطبية مبني بـ Laravel مع واجهة عربية متكاملة.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-purple.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 🌟 المميزات الرئيسية

### 📊 إدارة المخزون
- **إدارة الأصناف**: أدوية، مستلزمات طبية، مكملات غذائية
- **تتبع المخزون**: كميات متاحة، حد أدنى، تواريخ انتهاء الصلاحية
- **إدارة المخازن**: مخازن متعددة مع نقل البضائع
- **تقارير المخزون**: تقارير مفصلة عن حالة المخزون

### 💰 إدارة المبيعات
- **الفواتير**: إنشاء وطباعة فواتير مع QR Code
- **العملاء**: إدارة شاملة للعملاء مع تتبع المعاملات
- **المدفوعات**: تسجيل وتتبع المدفوعات والمستحقات
- **المرتجعات**: إدارة مرتجعات البضائع

### 📈 التقارير والإحصائيات
- **تقارير المبيعات**: يومية، شهرية، سنوية
- **التقارير المالية**: أرباح، خسائر، تدفق نقدي
- **تقارير العملاء**: أفضل العملاء، المستحقات
- **تقارير مخصصة**: منشئ تقارير متقدم

### 👥 إدارة الموارد البشرية
- **الموظفين**: بيانات شخصية، رواتب، إجازات
- **الحضور والانصراف**: تتبع ساعات العمل
- **كشوف الرواتب**: حساب وطباعة كشوف الرواتب
- **الأقسام**: تنظيم الموظفين حسب الأقسام

## 🛠️ التقنيات المستخدمة

- **Backend**: Laravel 11.x
- **Frontend**: Bootstrap 5, jQuery, Select2
- **Database**: MySQL
- **Charts**: Chart.js
- **PDF**: DomPDF
- **Excel**: PhpSpreadsheet
- **QR Code**: SimpleSoftwareIO/simple-qrcode

## 📋 متطلبات النظام

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js & NPM
- Apache/Nginx

## 🚀 التثبيت

### 1. استنساخ المشروع
```bash
git clone https://github.com/yourusername/pharmacy-erp.git
cd pharmacy-erp
```

### 2. تثبيت التبعيات
```bash
composer install
npm install
```

### 3. إعداد البيئة
```bash
cp .env.example .env
php artisan key:generate
```

### 4. إعداد قاعدة البيانات
```bash
mysql -u root -p -e "CREATE DATABASE pharmacy_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 5. تشغيل المايجريشن
```bash
php artisan migrate
php artisan db:seed
```

### 6. تشغيل الخادم
```bash
php artisan serve
```

## 👤 بيانات الدخول الافتراضية

- **الإيميل**: admin@pharmacy.com
- **كلمة المرور**: 123456

## 📱 الواجهات الرئيسية

- لوحة التحكم مع إحصائيات شاملة
- إدارة الفواتير مع قوائم قابلة للبحث
- إدارة العملاء مع تتبع المعاملات
- تقارير مفصلة ومخصصة

## 🔄 API Endpoints

- `GET /api/search/customers?search=term`
- `GET /api/search/items?search=term`
- `GET /api/search/orders?search=term`

## 📝 الترخيص

هذا المشروع مرخص تحت رخصة MIT.

---

**تم تطويره بـ ❤️ في العراق**
# 🏥 نظام إدارة الصيدلية المتكامل - MaxCon ERP

## 🌟 نظرة عامة

نظام إدارة الصيدلية المتكامل مع دعم متعدد المستأجرين، مصمم خصيصاً للصيدليات والشركات الطبية في العراق والمنطقة العربية.

### 🎯 المميزات الرئيسية

- 🏢 **نظام متعدد المستأجرين** مع عزل كامل للبيانات
- 🔐 **نظام أمان متقدم** مع RBAC وتسجيل العمليات
- 📊 **تقارير شاملة** ولوحات تحكم تفاعلية
- 📱 **واجهة متجاوبة** تعمل على جميع الأجهزة
- 🌍 **دعم متعدد اللغات** (العربية والإنجليزية والكردية)
- 💰 **إدارة مالية متكاملة** مع تتبع المدفوعات والديون
- 📦 **إدارة المخزون** مع تنبيهات المخزون المنخفض
- 👥 **إدارة العملاء** مع تاريخ المعاملات
- 🚚 **نظام مندوبي المبيعات** مع تتبع GPS
- 📱 **تطبيق Flutter** للمندوبين

## 🚀 التقنيات المستخدمة

- **Backend:** Laravel 11 + PHP 8.1+
- **Frontend:** Blade Templates + Bootstrap 5
- **Database:** MySQL 8.0+
- **Mobile:** Flutter (للمندوبين)
- **Deployment:** Cloudways + Hostinger
- **Security:** Multi-layer security with CSRF protection

## 📋 المتطلبات

### متطلبات الخادم
- PHP 8.1 أو أحدث
- MySQL 8.0 أو أحدث
- Composer
- Node.js & NPM (للتطوير)
- Git

### متطلبات الذاكرة والأداء
- RAM: 512MB كحد أدنى (2GB مُوصى به)
- Storage: 10GB كحد أدنى
- CPU: 1 Core كحد أدنى (2 Cores مُوصى به)

## 🛠️ التثبيت والإعداد

### 1. استنساخ المشروع
```bash
git clone https://github.com/miiiso1983/maxcon.git
cd maxcon
```

### 2. تثبيت Dependencies
```bash
composer install
npm install && npm run build
```

### 3. إعداد البيئة
```bash
cp .env.example .env
php artisan key:generate
```

### 4. إعداد قاعدة البيانات
```bash
# تحديث إعدادات قاعدة البيانات في .env
php artisan migrate
php artisan db:seed
```

### 5. إعداد التخزين
```bash
php artisan storage:link
chmod -R 755 storage bootstrap/cache
```

### 6. تشغيل الخادم
```bash
php artisan serve
```

## ☁️ النشر على Cloudways

### التحضير للنشر
```bash
# تشغيل سكريبت التحضير
./prepare-for-cloudways.sh

# أو تحضير يدوي
cp .env.cloudways .env
composer install --no-dev --optimize-autoloader
php artisan optimize
```

### خطوات النشر
1. إنشاء خادم في [Cloudways](https://www.cloudways.com)
2. رفع المشروع عبر Git Deployment
3. تحديث إعدادات قاعدة البيانات
4. تشغيل أوامر Laravel
5. إعداد SSL وCDN

📖 **للتفاصيل الكاملة:** راجع [دليل النشر على Cloudways](CLOUDWAYS-DEPLOYMENT-GUIDE.md)

## 🏠 النشر على Hostinger

### التحضير للنشر
```bash
./deploy-to-hostinger-simple.sh
```

📖 **للتفاصيل الكاملة:** راجع [دليل النشر على Hostinger](HOSTINGER-DEPLOYMENT-GUIDE.md)

## 🔐 بيانات تسجيل الدخول الافتراضية

### السوبر أدمن
- **الرابط:** `/super-admin/login`
- **البريد:** `superadmin@pharmacy-erp.com`
- **كلمة المرور:** `SuperAdmin@2024`

### المستخدم العادي
- **الرابط:** `/login`
- **البريد:** `atheer@rama.com`
- **كلمة المرور:** `Manager@2024`

## 📱 تطبيق Flutter للمندوبين

### المميزات
- 📍 تتبع GPS للزيارات
- 📝 تقارير يومية
- 💰 تحصيل المدفوعات
- 📦 إدارة الطلبات
- 🔔 تذكيرات ذكية

### التثبيت
```bash
cd flutter_sales_app
flutter pub get
flutter run
```

## 🔧 أدوات التطوير والصيانة

### فحص النظام
- `cloudways-check.php` - فحص شامل للنشر على Cloudways
- `cloudways-ready.php` - فحص جاهزية المشروع
- `quick-check.php` - فحص سريع للنظام

### تحسين الأداء
- `cloudways-optimize.php` - تحسين الأداء على Cloudways
- `php artisan optimize` - تحسين Laravel

### النشر التلقائي
- `deploy-hook.php` - webhook للنشر التلقائي
- `cloudways-git-deploy.sh` - إعداد Git Deployment

## 📞 الدعم والمساعدة

### الوثائق
- [دليل النشر على Cloudways](CLOUDWAYS-DEPLOYMENT-GUIDE.md)
- [دليل النشر على Hostinger](HOSTINGER-DEPLOYMENT-GUIDE.md)
- [دليل النظام متعدد المستأجرين](MULTI_TENANT_SYSTEM.md)
- [إرشادات الإعداد](SETUP_INSTRUCTIONS.md)

---

<div align="center">
  <strong>🚀 MaxCon ERP - نظام إدارة الصيدلية المتكامل</strong><br>
  <em>مطور بـ ❤️ للصيدليات العربية</em>
</div>
