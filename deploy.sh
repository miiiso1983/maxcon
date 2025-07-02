#!/bin/bash

# سكريبت نشر نظام إدارة الصيدلية على خادم الإنتاج
# استخدم هذا السكريبت بعد رفع الملفات إلى الخادم

echo "🚀 بدء عملية نشر نظام إدارة الصيدلية..."

# 1. تنظيف الكاش
echo "📝 تنظيف الكاش..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 2. إعداد الصلاحيات
echo "🔐 إعداد الصلاحيات..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env

# 3. تحديث Composer (إذا لزم الأمر)
if [ ! -d "vendor" ]; then
    echo "📦 تثبيت Composer dependencies..."
    composer install --no-dev --optimize-autoloader
fi

# 4. إنشاء مفتاح التطبيق
echo "🔑 إنشاء مفتاح التطبيق..."
php artisan key:generate --force

# 5. تشغيل Migrations
echo "🗄️ تشغيل قاعدة البيانات..."
php artisan migrate --force

# 6. تشغيل Seeders
echo "🌱 إضافة البيانات الأساسية..."
php artisan db:seed --class=MultiTenantSystemSeeder --force

# 7. إنشاء الكاش للإنتاج
echo "⚡ إنشاء كاش الإنتاج..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. إنشاء رابط التخزين
echo "🔗 إنشاء رابط التخزين..."
php artisan storage:link

# 9. تحسين Autoloader
echo "🚀 تحسين الأداء..."
composer dump-autoload --optimize

echo "✅ تم نشر النظام بنجاح!"
echo ""
echo "🔗 الروابط المهمة:"
echo "- الصفحة الرئيسية: https://yourdomain.com"
echo "- تسجيل دخول المستخدمين: https://yourdomain.com/login"
echo "- تسجيل دخول السوبر أدمن: https://yourdomain.com/super-admin/login"
echo ""
echo "🔑 بيانات تسجيل الدخول:"
echo "- Super Admin: superadmin@pharmacy-erp.com / SuperAdmin@2024"
echo "- Admin: admin@pharmacy-erp.com / Admin@2024"
echo "- Support: support@pharmacy-erp.com / Support@2024"
echo ""
echo "⚠️ لا تنس:"
echo "1. حذف ملف debug.php من public/"
echo "2. تحديث APP_URL في .env"
echo "3. إعداد SSL Certificate"
echo "4. إعداد Cron Jobs للمهام المجدولة"
echo ""
echo "📞 للدعم الفني: تواصل مع المطور"
