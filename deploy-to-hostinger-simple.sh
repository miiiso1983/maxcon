#!/bin/bash

# نشر مبسط لـ Hostinger
echo "🚀 بدء نشر المشروع على Hostinger..."

# 1. نسخ ملف الإعدادات
echo "📋 نسخ إعدادات Hostinger..."
if [ -f ".env.hostinger" ]; then
    cp .env.hostinger .env
    echo "✅ تم نسخ .env.hostinger إلى .env"
else
    echo "❌ ملف .env.hostinger غير موجود!"
    exit 1
fi

# 2. تنظيف الكاش
echo "🧹 تنظيف الكاش..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 3. تحسين للإنتاج
echo "⚡ تحسين للإنتاج..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. إنشاء مجلدات مطلوبة
echo "📁 إنشاء المجلدات المطلوبة..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# 5. ضبط الصلاحيات
echo "🔐 ضبط الصلاحيات..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env

# 6. إنشاء ملف معلومات النشر
echo "📄 إنشاء ملف معلومات النشر..."
cat > deployment-info.txt << EOF
=== معلومات النشر ===
تاريخ النشر: $(date)
إصدار PHP: $(php -v | head -n 1)
إصدار Laravel: $(php artisan --version)

=== الملفات المطلوبة للرفع ===
- جميع ملفات المشروع
- مجلد vendor (أو تشغيل composer install)
- ملف .env (محدث بإعدادات Hostinger)

=== الأوامر المطلوبة على السيرفر ===
1. php artisan key:generate --force
2. php artisan migrate --force
3. php artisan db:seed --force
4. chmod -R 755 storage bootstrap/cache

=== إعدادات قاعدة البيانات المطلوبة ===
يجب تحديث هذه القيم في ملف .env:
- DB_HOST=localhost
- DB_DATABASE=اسم_قاعدة_البيانات_من_hostinger
- DB_USERNAME=اسم_المستخدم_من_hostinger
- DB_PASSWORD=كلمة_المرور_من_hostinger

=== روابط مهمة بعد النشر ===
- الموقع الرئيسي: https://yourdomain.com
- تسجيل الدخول: https://yourdomain.com/login
- لوحة السوبر أدمن: https://yourdomain.com/super-admin/login
- اختبار قاعدة البيانات: https://yourdomain.com/test-hostinger-connection.php

=== بيانات تسجيل الدخول الافتراضية ===
السوبر أدمن:
- البريد: superadmin@pharmacy-erp.com
- كلمة المرور: SuperAdmin@2024

المستخدم العادي:
- البريد: atheer@rama.com
- كلمة المرور: Manager@2024
EOF

echo "✅ تم إنشاء ملف deployment-info.txt"

# 7. إنشاء ملف تحقق سريع
echo "🔍 إنشاء ملف تحقق سريع..."
cat > quick-check.php << 'EOF'
<?php
echo "<h1>فحص سريع للنشر</h1>";

$checks = [
    'PHP Version' => phpversion(),
    'Laravel Installed' => file_exists('vendor/autoload.php') ? '✅ نعم' : '❌ لا',
    '.env File' => file_exists('.env') ? '✅ موجود' : '❌ غير موجود',
    'Storage Writable' => is_writable('storage') ? '✅ قابل للكتابة' : '❌ غير قابل للكتابة',
    'Bootstrap Cache Writable' => is_writable('bootstrap/cache') ? '✅ قابل للكتابة' : '❌ غير قابل للكتابة',
];

echo "<table border='1' style='border-collapse:collapse; width:100%;'>";
echo "<tr><th>الفحص</th><th>النتيجة</th></tr>";
foreach ($checks as $check => $result) {
    echo "<tr><td>$check</td><td>$result</td></tr>";
}
echo "</table>";

if (file_exists('.env')) {
    echo "<h2>إعدادات قاعدة البيانات:</h2>";
    $env = file_get_contents('.env');
    preg_match('/DB_HOST=(.*)/', $env, $host);
    preg_match('/DB_DATABASE=(.*)/', $env, $database);
    preg_match('/DB_USERNAME=(.*)/', $env, $username);
    
    echo "<ul>";
    echo "<li>DB_HOST: " . ($host[1] ?? 'غير محدد') . "</li>";
    echo "<li>DB_DATABASE: " . ($database[1] ?? 'غير محدد') . "</li>";
    echo "<li>DB_USERNAME: " . ($username[1] ?? 'غير محدد') . "</li>";
    echo "</ul>";
}

echo "<p><a href='test-hostinger-connection.php'>اختبار الاتصال بقاعدة البيانات</a></p>";
?>
EOF

echo "✅ تم إنشاء ملف quick-check.php"

# 8. عرض ملخص النشر
echo ""
echo "🎉 تم الانتهاء من تحضير المشروع للنشر!"
echo ""
echo "📋 الخطوات التالية:"
echo "1. ارفع جميع ملفات المشروع إلى public_html في Hostinger"
echo "2. أنشئ قاعدة بيانات جديدة في لوحة تحكم Hostinger"
echo "3. حدث إعدادات قاعدة البيانات في ملف .env"
echo "4. شغل الأوامر المذكورة في deployment-info.txt"
echo "5. اختبر الموقع باستخدام quick-check.php"
echo ""
echo "📁 الملفات المهمة:"
echo "- deployment-info.txt (معلومات النشر)"
echo "- quick-check.php (فحص سريع)"
echo "- test-hostinger-connection.php (اختبار قاعدة البيانات)"
echo ""
echo "🔗 بعد النشر، زر:"
echo "- https://yourdomain.com/quick-check.php"
echo "- https://yourdomain.com/test-hostinger-connection.php"
echo ""
