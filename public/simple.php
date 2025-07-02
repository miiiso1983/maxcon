<?php
// ملف اختبار Laravel مبسط
echo "<h1>اختبار Laravel</h1>";

try {
    // تحميل autoload
    if (!file_exists('../vendor/autoload.php')) {
        throw new Exception('ملف vendor/autoload.php غير موجود');
    }
    require_once '../vendor/autoload.php';
    echo "✓ تم تحميل autoload<br>";

    // تحميل .env
    if (!file_exists('../.env')) {
        throw new Exception('ملف .env غير موجود');
    }
    echo "✓ ملف .env موجود<br>";

    // تحميل bootstrap
    if (!file_exists('../bootstrap/app.php')) {
        throw new Exception('ملف bootstrap/app.php غير موجود');
    }
    
    $app = require_once '../bootstrap/app.php';
    echo "✓ تم تحميل Laravel bootstrap<br>";

    // اختبار قاعدة البيانات
    try {
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        
        $pdo = DB::connection()->getPdo();
        echo "✓ الاتصال بقاعدة البيانات نجح<br>";
        echo "قاعدة البيانات: " . DB::connection()->getDatabaseName() . "<br>";
    } catch (Exception $e) {
        echo "✗ خطأ في قاعدة البيانات: " . $e->getMessage() . "<br>";
    }

    echo "<h2>النظام جاهز!</h2>";
    echo "<a href='/login'>صفحة تسجيل الدخول</a><br>";
    echo "<a href='/super-admin/login'>صفحة السوبر أدمن</a>";

} catch (Exception $e) {
    echo "✗ خطأ: " . $e->getMessage() . "<br>";
    echo "تفاصيل الخطأ:<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
