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
# maxcon
