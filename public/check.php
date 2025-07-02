<?php
/**
 * فحص سريع لحالة النظام
 * ضع هذا الملف في public/ واحذفه بعد حل المشاكل
 */

echo "<h1>فحص سريع للنظام</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .ok{color:green;} .error{color:red;}</style>";

// 1. فحص PHP
echo "<h2>PHP</h2>";
echo "إصدار PHP: " . PHP_VERSION . "<br>";

// 2. فحص المجلدات
echo "<h2>المجلدات</h2>";
$dirs = ['../storage', '../bootstrap/cache', '../vendor'];
foreach ($dirs as $dir) {
    $status = is_dir($dir) ? '<span class="ok">✓</span>' : '<span class="error">✗</span>';
    echo "$dir: $status<br>";
}

// 3. فحص ملف .env
echo "<h2>ملف .env</h2>";
$envExists = file_exists('../.env');
echo "ملف .env: " . ($envExists ? '<span class="ok">✓ موجود</span>' : '<span class="error">✗ غير موجود</span>') . "<br>";

if ($envExists) {
    $env = file_get_contents('../.env');
    echo "APP_ENV: " . (strpos($env, 'APP_ENV=') !== false ? '<span class="ok">✓</span>' : '<span class="error">✗</span>') . "<br>";
    echo "DB_DATABASE: " . (strpos($env, 'DB_DATABASE=') !== false ? '<span class="ok">✓</span>' : '<span class="error">✗</span>') . "<br>";
}

// 4. فحص Laravel
echo "<h2>Laravel</h2>";
$autoload = file_exists('../vendor/autoload.php');
echo "Autoload: " . ($autoload ? '<span class="ok">✓ موجود</span>' : '<span class="error">✗ غير موجود</span>') . "<br>";

if ($autoload) {
    try {
        require_once '../vendor/autoload.php';
        echo "Laravel: <span class='ok'>✓ يعمل</span><br>";
    } catch (Exception $e) {
        echo "Laravel: <span class='error'>✗ خطأ: " . $e->getMessage() . "</span><br>";
    }
}

echo "<hr>";
echo "<p>تاريخ الفحص: " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>احذف هذا الملف بعد حل المشاكل!</strong></p>";
?>
