<?php
/**
 * سكريبت تشغيل الخادم البديل
 * شغل هذا الملف: php start-server.php
 */

echo "🚀 بدء تشغيل خادم Laravel...\n\n";

// فحص المتطلبات الأساسية
$checks = [
    'PHP Version' => version_compare(PHP_VERSION, '8.1.0', '>='),
    'Vendor Directory' => is_dir('vendor'),
    'Bootstrap File' => file_exists('bootstrap/app.php'),
    'Artisan File' => file_exists('artisan'),
    'Env File' => file_exists('.env')
];

echo "📋 فحص المتطلبات:\n";
foreach ($checks as $check => $status) {
    $icon = $status ? "✅" : "❌";
    echo "$icon $check\n";
}

// إذا كانت هناك مشاكل، اعرض الحلول
$failed_checks = array_filter($checks, function($status) { return !$status; });

if (!empty($failed_checks)) {
    echo "\n⚠️ مشاكل تحتاج إصلاح:\n";
    
    if (!$checks['Vendor Directory']) {
        echo "- شغل: composer install\n";
    }
    
    if (!$checks['Env File']) {
        echo "- شغل: cp .env.example .env\n";
        echo "- شغل: php artisan key:generate\n";
    }
    
    echo "\nأصلح هذه المشاكل أولاً ثم أعد تشغيل السكريبت.\n";
    exit(1);
}

// البحث عن منفذ متاح
echo "\n🔍 البحث عن منفذ متاح...\n";
$ports = [8000, 8080, 8001, 3000, 9000];
$available_port = null;

foreach ($ports as $port) {
    $connection = @fsockopen('127.0.0.1', $port, $errno, $errstr, 1);
    if (!$connection) {
        $available_port = $port;
        echo "✅ المنفذ $port متاح\n";
        break;
    } else {
        echo "⚠️ المنفذ $port مستخدم\n";
        fclose($connection);
    }
}

if (!$available_port) {
    echo "❌ لم يتم العثور على منفذ متاح. جرب إغلاق التطبيقات الأخرى.\n";
    exit(1);
}

// مسح الكاش قبل التشغيل
echo "\n🧹 مسح الكاش...\n";
$cache_commands = [
    'config:clear',
    'cache:clear',
    'route:clear',
    'view:clear'
];

foreach ($cache_commands as $command) {
    $output = shell_exec("php artisan $command 2>&1");
    if (strpos($output, 'successfully') !== false || strpos($output, 'cleared') !== false) {
        echo "✅ $command\n";
    } else {
        echo "⚠️ $command (قد يكون غير ضروري)\n";
    }
}

// تشغيل الخادم
echo "\n🚀 تشغيل الخادم على المنفذ $available_port...\n";
echo "الرابط: http://localhost:$available_port\n";
echo "للإيقاف: اضغط Ctrl+C\n\n";

// تشغيل الخادم
$command = "php artisan serve --host=127.0.0.1 --port=$available_port";
echo "تشغيل الأمر: $command\n\n";

// تشغيل الأمر
passthru($command);
?>
