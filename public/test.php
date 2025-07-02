<?php
echo "<h1>اختبار PHP</h1>";
echo "PHP يعمل بشكل صحيح!<br>";
echo "إصدار PHP: " . PHP_VERSION . "<br>";
echo "التاريخ: " . date('Y-m-d H:i:s') . "<br>";

// فحص المجلدات
echo "<h2>فحص المجلدات:</h2>";
$dirs = ['../storage', '../bootstrap', '../vendor', '../app'];
foreach ($dirs as $dir) {
    echo $dir . ": " . (is_dir($dir) ? "موجود" : "غير موجود") . "<br>";
}

// فحص ملف .env
echo "<h2>فحص .env:</h2>";
echo "ملف .env: " . (file_exists('../.env') ? "موجود" : "غير موجود") . "<br>";

if (file_exists('../.env')) {
    $env = file_get_contents('../.env');
    echo "حجم الملف: " . strlen($env) . " حرف<br>";
    echo "يحتوي على APP_KEY: " . (strpos($env, 'APP_KEY=') !== false ? "نعم" : "لا") . "<br>";
    echo "يحتوي على DB_DATABASE: " . (strpos($env, 'DB_DATABASE=') !== false ? "نعم" : "لا") . "<br>";
}

// فحص autoload
echo "<h2>فحص Laravel:</h2>";
echo "vendor/autoload.php: " . (file_exists('../vendor/autoload.php') ? "موجود" : "غير موجود") . "<br>";

if (file_exists('../vendor/autoload.php')) {
    try {
        require_once '../vendor/autoload.php';
        echo "تحميل Laravel: نجح<br>";
        
        // فحص bootstrap
        if (file_exists('../bootstrap/app.php')) {
            echo "bootstrap/app.php: موجود<br>";
        }
    } catch (Exception $e) {
        echo "خطأ في تحميل Laravel: " . $e->getMessage() . "<br>";
    }
}
?>
