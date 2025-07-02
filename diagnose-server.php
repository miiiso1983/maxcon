<?php
/**
 * أداة تشخيص مشاكل الخادم المحلي
 * شغل هذا الملف: php diagnose-server.php
 */

echo "🔍 تشخيص مشاكل الخادم المحلي\n";
echo "================================\n\n";

// فحص PHP
echo "📋 فحص PHP:\n";
echo "إصدار PHP: " . PHP_VERSION . "\n";
echo "مسار PHP: " . PHP_BINARY . "\n";

// فحص الإضافات المطلوبة
$required_extensions = ['openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
echo "\n🔧 فحص الإضافات المطلوبة:\n";
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? "✅" : "❌";
    echo "$status $ext\n";
}

// فحص الملفات المطلوبة
echo "\n📁 فحص الملفات المطلوبة:\n";
$required_files = [
    'vendor/autoload.php' => 'Composer Autoload',
    'bootstrap/app.php' => 'Laravel Bootstrap',
    '.env' => 'Environment File',
    'artisan' => 'Artisan Command',
    'public/index.php' => 'Public Index'
];

foreach ($required_files as $file => $desc) {
    $status = file_exists($file) ? "✅" : "❌";
    echo "$status $desc ($file)\n";
}

// فحص ملف .env
echo "\n📄 فحص ملف .env:\n";
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    
    // فحص APP_KEY
    if (preg_match('/APP_KEY=(.+)/', $env_content, $matches)) {
        $app_key = trim($matches[1]);
        if (empty($app_key)) {
            echo "❌ APP_KEY فارغ\n";
        } elseif (strpos($app_key, 'base64:') === 0) {
            echo "✅ APP_KEY محدد بشكل صحيح\n";
        } else {
            echo "⚠️ APP_KEY محدد لكن قد يكون بصيغة قديمة\n";
        }
    } else {
        echo "❌ APP_KEY غير موجود\n";
    }
    
    // فحص إعدادات قاعدة البيانات
    $db_settings = ['DB_CONNECTION', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME'];
    foreach ($db_settings as $setting) {
        if (preg_match("/$setting=(.+)/", $env_content, $matches)) {
            $value = trim($matches[1]);
            if (!empty($value)) {
                echo "✅ $setting محدد\n";
            } else {
                echo "⚠️ $setting فارغ\n";
            }
        } else {
            echo "❌ $setting غير موجود\n";
        }
    }
} else {
    echo "❌ ملف .env غير موجود\n";
}

// فحص صلاحيات المجلدات
echo "\n🔒 فحص صلاحيات المجلدات:\n";
$directories = ['storage', 'bootstrap/cache'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "✅ $dir قابل للكتابة\n";
        } else {
            echo "❌ $dir غير قابل للكتابة\n";
        }
    } else {
        echo "❌ $dir غير موجود\n";
    }
}

// اختبار Composer
echo "\n📦 فحص Composer:\n";
if (file_exists('vendor/autoload.php')) {
    try {
        require_once 'vendor/autoload.php';
        echo "✅ Composer autoload يعمل\n";
    } catch (Exception $e) {
        echo "❌ خطأ في Composer autoload: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Composer autoload غير موجود\n";
}

// اختبار Laravel Bootstrap
echo "\n🚀 فحص Laravel Bootstrap:\n";
if (file_exists('bootstrap/app.php')) {
    try {
        $app = require_once 'bootstrap/app.php';
        echo "✅ Laravel Bootstrap يعمل\n";
        
        // اختبار إنشاء Application
        if ($app instanceof Illuminate\Foundation\Application) {
            echo "✅ Laravel Application تم إنشاؤه بنجاح\n";
        } else {
            echo "❌ Laravel Application لم يتم إنشاؤه بشكل صحيح\n";
        }
    } catch (Exception $e) {
        echo "❌ خطأ في Laravel Bootstrap: " . $e->getMessage() . "\n";
    } catch (Error $e) {
        echo "❌ خطأ PHP في Laravel Bootstrap: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Laravel Bootstrap غير موجود\n";
}

// اختبار قاعدة البيانات
echo "\n🗄️ اختبار قاعدة البيانات:\n";
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    
    // استخراج إعدادات قاعدة البيانات
    preg_match('/DB_CONNECTION=(.+)/', $env_content, $connection_match);
    preg_match('/DB_HOST=(.+)/', $env_content, $host_match);
    preg_match('/DB_DATABASE=(.+)/', $env_content, $database_match);
    preg_match('/DB_USERNAME=(.+)/', $env_content, $username_match);
    preg_match('/DB_PASSWORD=(.*)/', $env_content, $password_match);
    
    $connection = trim($connection_match[1] ?? '');
    $host = trim($host_match[1] ?? '');
    $database = trim($database_match[1] ?? '');
    $username = trim($username_match[1] ?? '');
    $password = trim($password_match[1] ?? '');
    
    if ($connection === 'mysql' && !empty($host) && !empty($database)) {
        try {
            $dsn = "mysql:host=$host;dbname=$database";
            $pdo = new PDO($dsn, $username, $password);
            echo "✅ اتصال قاعدة البيانات ناجح\n";
        } catch (PDOException $e) {
            echo "❌ فشل اتصال قاعدة البيانات: " . $e->getMessage() . "\n";
        }
    } elseif ($connection === 'sqlite') {
        $db_path = $database;
        if (file_exists($db_path)) {
            echo "✅ قاعدة بيانات SQLite موجودة\n";
        } else {
            echo "❌ قاعدة بيانات SQLite غير موجودة: $db_path\n";
        }
    } else {
        echo "⚠️ إعدادات قاعدة البيانات غير مكتملة\n";
    }
}

// فحص المنافذ المستخدمة
echo "\n🌐 فحص المنافذ:\n";
$ports_to_check = [8000, 8080, 3000, 80];
foreach ($ports_to_check as $port) {
    $connection = @fsockopen('127.0.0.1', $port, $errno, $errstr, 1);
    if ($connection) {
        echo "⚠️ المنفذ $port مستخدم\n";
        fclose($connection);
    } else {
        echo "✅ المنفذ $port متاح\n";
    }
}

// اقتراحات الحلول
echo "\n💡 اقتراحات الحلول:\n";
echo "================================\n";

if (!file_exists('.env')) {
    echo "1. أنشئ ملف .env:\n";
    echo "   cp .env.example .env\n";
    echo "   php artisan key:generate\n\n";
}

if (!file_exists('vendor/autoload.php')) {
    echo "2. ثبت مكتبات Composer:\n";
    echo "   composer install\n\n";
}

if (!is_writable('storage') || !is_writable('bootstrap/cache')) {
    echo "3. أصلح صلاحيات المجلدات:\n";
    echo "   chmod -R 755 storage bootstrap/cache\n\n";
}

echo "4. شغل الخادم:\n";
echo "   php artisan serve\n\n";

echo "5. إذا استمرت المشكلة، جرب:\n";
echo "   php artisan config:clear\n";
echo "   php artisan cache:clear\n";
echo "   php artisan serve --port=8080\n\n";

echo "🎯 للحصول على مساعدة إضافية، شارك نتائج هذا التشخيص.\n";
?>
