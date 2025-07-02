<?php
/**
 * ملف تشخيص مشاكل الإنتاج
 * احذف هذا الملف بعد حل المشاكل لأسباب أمنية
 */

echo "<h1>تشخيص نظام إدارة الصيدلية</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;}</style>";

// 1. فحص PHP
echo "<h2>1. معلومات PHP</h2>";
echo "إصدار PHP: " . PHP_VERSION . "<br>";
echo "نظام التشغيل: " . PHP_OS . "<br>";
echo "الذاكرة المتاحة: " . ini_get('memory_limit') . "<br>";
echo "حد رفع الملفات: " . ini_get('upload_max_filesize') . "<br>";
echo "حد POST: " . ini_get('post_max_size') . "<br>";

// 2. فحص المجلدات والصلاحيات
echo "<h2>2. فحص المجلدات والصلاحيات</h2>";

$directories = [
    '../storage/logs' => 'مجلد السجلات',
    '../storage/framework/cache' => 'مجلد الكاش',
    '../storage/framework/sessions' => 'مجلد الجلسات',
    '../storage/framework/views' => 'مجلد العروض المترجمة',
    '../bootstrap/cache' => 'مجلد Bootstrap Cache',
];

foreach ($directories as $dir => $name) {
    if (is_dir($dir)) {
        $writable = is_writable($dir) ? '<span class="success">✓ قابل للكتابة</span>' : '<span class="error">✗ غير قابل للكتابة</span>';
        echo "$name: $writable<br>";
    } else {
        echo "$name: <span class='error'>✗ غير موجود</span><br>";
    }
}

// 3. فحص ملف .env
echo "<h2>3. فحص ملف .env</h2>";
if (file_exists('../.env')) {
    echo "<span class='success'>✓ ملف .env موجود</span><br>";
    
    // قراءة بعض الإعدادات المهمة (بدون كشف كلمات المرور)
    $envContent = file_get_contents('../.env');
    $lines = explode("\n", $envContent);
    
    $importantKeys = ['APP_NAME', 'APP_ENV', 'APP_DEBUG', 'APP_URL', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE'];
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        
        foreach ($importantKeys as $key) {
            if (strpos($line, $key . '=') === 0) {
                echo "$line<br>";
                break;
            }
        }
    }
} else {
    echo "<span class='error'>✗ ملف .env غير موجود</span><br>";
}

// 4. فحص قاعدة البيانات
echo "<h2>4. فحص قاعدة البيانات</h2>";
try {
    // تحميل متغيرات البيئة
    if (file_exists('../.env')) {
        $envContent = file_get_contents('../.env');
        $lines = explode("\n", $envContent);
        $env = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $env[trim($parts[0])] = trim($parts[1], '"\'');
            }
        }
        
        $host = $env['DB_HOST'] ?? 'localhost';
        $database = $env['DB_DATABASE'] ?? '';
        $username = $env['DB_USERNAME'] ?? '';
        $password = $env['DB_PASSWORD'] ?? '';
        
        if (!empty($database) && !empty($username)) {
            $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            echo "<span class='success'>✓ الاتصال بقاعدة البيانات نجح</span><br>";
            
            // فحص الجداول
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "عدد الجداول: " . count($tables) . "<br>";
            
            // فحص جداول مهمة
            $importantTables = ['users', 'tenants', 'super_admins', 'sales_representatives'];
            foreach ($importantTables as $table) {
                if (in_array($table, $tables)) {
                    echo "<span class='success'>✓ جدول $table موجود</span><br>";
                } else {
                    echo "<span class='error'>✗ جدول $table غير موجود</span><br>";
                }
            }
        } else {
            echo "<span class='error'>✗ بيانات قاعدة البيانات غير مكتملة في .env</span><br>";
        }
    }
} catch (Exception $e) {
    echo "<span class='error'>✗ خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage() . "</span><br>";
}

// 5. فحص Laravel
echo "<h2>5. فحص Laravel</h2>";
if (file_exists('../vendor/autoload.php')) {
    echo "<span class='success'>✓ Composer autoload موجود</span><br>";
    
    try {
        require_once '../vendor/autoload.php';
        echo "<span class='success'>✓ تم تحميل Laravel بنجاح</span><br>";
    } catch (Exception $e) {
        echo "<span class='error'>✗ خطأ في تحميل Laravel: " . $e->getMessage() . "</span><br>";
    }
} else {
    echo "<span class='error'>✗ ملف vendor/autoload.php غير موجود - شغل composer install</span><br>";
}

// 6. فحص الملفات المهمة
echo "<h2>6. فحص الملفات المهمة</h2>";
$importantFiles = [
    '../app/Http/Kernel.php' => 'Kernel',
    '../routes/web.php' => 'Web Routes',
    '../routes/admin.php' => 'Admin Routes',
    '../bootstrap/app.php' => 'Bootstrap App',
];

foreach ($importantFiles as $file => $name) {
    if (file_exists($file)) {
        echo "<span class='success'>✓ $name موجود</span><br>";
    } else {
        echo "<span class='error'>✗ $name غير موجود</span><br>";
    }
}

echo "<hr>";
echo "<p><strong>ملاحظة:</strong> احذف هذا الملف (debug.php) بعد حل المشاكل لأسباب أمنية.</p>";
echo "<p>تاريخ الفحص: " . date('Y-m-d H:i:s') . "</p>";
?>
