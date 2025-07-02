#!/bin/bash

# سكريبت نشر نظام إدارة الصيدلية على Cloudways
echo "🚀 بدء نشر النظام على Cloudways..."

# متغيرات
APP_PATH="/applications/$(basename $PWD)/public_html"
echo "📁 مسار التطبيق: $APP_PATH"

# التأكد من المسار
if [ ! -f "artisan" ]; then
    echo "❌ خطأ: ملف artisan غير موجود. تأكد من أنك في مجلد Laravel الصحيح"
    exit 1
fi

# 1. تثبيت Dependencies
echo "📦 تثبيت Composer Dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 2. نسخ ملف البيئة
echo "⚙️ إعداد ملف البيئة..."
if [ -f ".env.cloudways" ]; then
    cp .env.cloudways .env
    echo "✅ تم نسخ .env.cloudways إلى .env"
else
    echo "⚠️ ملف .env.cloudways غير موجود، تأكد من إنشاؤه أولاً"
fi

# 3. إنشاء مفتاح التطبيق
echo "🔑 إنشاء مفتاح التطبيق..."
php artisan key:generate --force

# 4. مسح الكاش
echo "🧹 مسح الكاش القديم..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 5. إعداد قاعدة البيانات
echo "🗄️ إعداد قاعدة البيانات..."
php artisan migrate --force

# 6. تشغيل Seeders
echo "🌱 إضافة البيانات الأساسية..."
php artisan db:seed --class=MultiTenantSystemSeeder --force

# 7. إنشاء كاش الإنتاج
echo "⚡ إنشاء كاش الإنتاج..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. إنشاء رابط التخزين
echo "🔗 إنشاء رابط التخزين..."
php artisan storage:link

# 9. تحسين Autoloader
echo "🚀 تحسين Autoloader..."
composer dump-autoload --optimize

# 10. إعداد الصلاحيات
echo "🔐 إعداد الصلاحيات..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env

# 11. إنشاء ملفات مطلوبة
echo "📄 إنشاء ملفات مطلوبة..."
touch storage/logs/laravel.log
chmod 644 storage/logs/laravel.log

echo ""
echo "✅ تم نشر النظام بنجاح على Cloudways!"
echo ""
echo "🔗 الروابط المهمة:"
echo "- الصفحة الرئيسية: https://your-domain.com"
echo "- تسجيل دخول المستخدمين: https://your-domain.com/login"
echo "- تسجيل دخول السوبر أدمن: https://your-domain.com/super-admin/login"
echo ""
echo "🔑 بيانات تسجيل الدخول:"
echo "- Super Admin: superadmin@pharmacy-erp.com / SuperAdmin@2024"
echo "- Admin: admin@pharmacy-erp.com / Admin@2024"
echo "- Support: support@pharmacy-erp.com / Support@2024"
echo ""
echo "📋 الخطوات التالية:"
echo "1. تحديث APP_URL في .env"
echo "2. إعداد SSL Certificate"
echo "3. تفعيل Redis و Varnish"
echo "4. إعداد Cron Jobs"
echo "5. تفعيل النسخ الاحتياطي"
echo ""
echo "📞 للدعم الفني: تواصل مع فريق Cloudways"
