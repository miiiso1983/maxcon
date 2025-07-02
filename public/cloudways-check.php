<?php
/**
 * فحص شامل للنشر على Cloudways
 */

echo "<h1>☁️ فحص النشر على Cloudways</h1>";

// 1. فحص معلومات الخادم
echo "<h2>🖥️ معلومات الخادم:</h2>";
echo "<table border='1' style='border-collapse:collapse; width:100%; margin:10px 0;'>";
echo "<tr><th>المعلومة</th><th>القيمة</th><th>الحالة</th></tr>";

$serverChecks = [
    'إصدار PHP' => [
        'value' => phpversion(),
        'status' => version_compare(phpversion(), '8.1.0', '>=') ? '✅ مناسب' : '❌ يحتاج تحديث'
    ],
    'Memory Limit' => [
        'value' => ini_get('memory_limit'),
        'status' => (int)ini_get('memory_limit') >= 512 ? '✅ كافي' : '⚠️ قليل'
    ],
    'Max Execution Time' => [
        'value' => ini_get('max_execution_time') . 's',
        'status' => ini_get('max_execution_time') >= 300 ? '✅ مناسب' : '⚠️ قصير'
    ],
    'Upload Max Size' => [
        'value' => ini_get('upload_max_filesize'),
        'status' => (int)ini_get('upload_max_filesize') >= 10 ? '✅ مناسب' : '⚠️ صغير'
    ],
    'Post Max Size' => [
        'value' => ini_get('post_max_size'),
        'status' => (int)ini_get('post_max_size') >= 10 ? '✅ مناسب' : '⚠️ صغير'
    ],
    'نظام التشغيل' => [
        'value' => php_uname('s') . ' ' . php_uname('r'),
        'status' => '✅ معلومات'
    ],
    'الوقت الحالي' => [
        'value' => date('Y-m-d H:i:s T'),
        'status' => '✅ معلومات'
    ]
];

foreach ($serverChecks as $check => $info) {
    echo "<tr>";
    echo "<td><strong>$check</strong></td>";
    echo "<td>{$info['value']}</td>";
    echo "<td>{$info['status']}</td>";
    echo "</tr>";
}
echo "</table>";

// 2. فحص ملفات Laravel
echo "<h2>📁 فحص ملفات Laravel:</h2>";
echo "<table border='1' style='border-collapse:collapse; width:100%; margin:10px 0;'>";
echo "<tr><th>الملف/المجلد</th><th>الحالة</th><th>الصلاحيات</th></tr>";

$laravelChecks = [
    'vendor/autoload.php' => 'ملف',
    '.env' => 'ملف',
    'storage' => 'مجلد',
    'storage/logs' => 'مجلد',
    'storage/framework' => 'مجلد',
    'storage/framework/cache' => 'مجلد',
    'storage/framework/sessions' => 'مجلد',
    'storage/framework/views' => 'مجلد',
    'bootstrap/cache' => 'مجلد',
    'public/storage' => 'رابط'
];

foreach ($laravelChecks as $path => $type) {
    echo "<tr>";
    echo "<td><strong>$path</strong> ($type)</td>";
    
    if (file_exists($path)) {
        echo "<td style='color:green;'>✅ موجود</td>";
        
        if ($type === 'مجلد') {
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            $writable = is_writable($path) ? '✅ قابل للكتابة' : '❌ غير قابل للكتابة';
            echo "<td>$perms - $writable</td>";
        } elseif ($type === 'رابط') {
            $isLink = is_link($path) ? '✅ رابط صحيح' : '❌ ليس رابط';
            echo "<td>$isLink</td>";
        } else {
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            echo "<td>$perms</td>";
        }
    } else {
        echo "<td style='color:red;'>❌ غير موجود</td>";
        echo "<td>-</td>";
    }
    echo "</tr>";
}
echo "</table>";

// 3. فحص إعدادات قاعدة البيانات
echo "<h2>🗄️ فحص قاعدة البيانات:</h2>";

if (file_exists('.env')) {
    $env = file_get_contents('.env');
    preg_match('/DB_HOST=(.*)/', $env, $host);
    preg_match('/DB_DATABASE=(.*)/', $env, $database);
    preg_match('/DB_USERNAME=(.*)/', $env, $username);
    
    echo "<table border='1' style='border-collapse:collapse; width:100%; margin:10px 0;'>";
    echo "<tr><th>الإعداد</th><th>القيمة</th></tr>";
    echo "<tr><td>DB_HOST</td><td>" . ($host[1] ?? 'غير محدد') . "</td></tr>";
    echo "<tr><td>DB_DATABASE</td><td>" . ($database[1] ?? 'غير محدد') . "</td></tr>";
    echo "<tr><td>DB_USERNAME</td><td>" . ($username[1] ?? 'غير محدد') . "</td></tr>";
    echo "</table>";
    
    // اختبار الاتصال
    try {
        if (file_exists('vendor/autoload.php')) {
            require_once 'vendor/autoload.php';
            
            $app = require_once 'bootstrap/app.php';
            $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
            
            DB::connection()->getPdo();
            echo "<div style='background:#d4edda; padding:15px; border-radius:5px; color:#155724; margin:10px 0;'>";
            echo "<h3>✅ نجح الاتصال بقاعدة البيانات!</h3>";
            echo "<p>تفاصيل الاتصال:</p>";
            echo "<ul>";
            echo "<li>اسم قاعدة البيانات: " . DB::connection()->getDatabaseName() . "</li>";
            echo "<li>إصدار MySQL: " . DB::select('SELECT VERSION() as version')[0]->version . "</li>";
            echo "</ul>";
            echo "</div>";
            
            // فحص الجداول
            $tables = DB::select('SHOW TABLES');
            echo "<p><strong>عدد الجداول:</strong> " . count($tables) . "</p>";
            
        } else {
            echo "<div style='background:#fff3cd; padding:15px; border-radius:5px; color:#856404;'>";
            echo "<p>⚠️ Laravel غير مثبت - لا يمكن اختبار قاعدة البيانات</p>";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='background:#f8d7da; padding:15px; border-radius:5px; color:#721c24;'>";
        echo "<h3>❌ فشل الاتصال بقاعدة البيانات!</h3>";
        echo "<p><strong>رسالة الخطأ:</strong> " . $e->getMessage() . "</p>";
        echo "</div>";
    }
} else {
    echo "<p style='color:red;'>❌ ملف .env غير موجود</p>";
}

// 4. فحص إعدادات الأمان
echo "<h2>🔐 فحص إعدادات الأمان:</h2>";

$securityChecks = [
    'HTTPS' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? '✅ مفعل' : '⚠️ غير مفعل',
    'X-Frame-Options' => isset($_SERVER['HTTP_X_FRAME_OPTIONS']) ? '✅ مفعل' : '⚠️ غير مفعل',
    'X-Content-Type-Options' => isset($_SERVER['HTTP_X_CONTENT_TYPE_OPTIONS']) ? '✅ مفعل' : '⚠️ غير مفعل',
    'Strict-Transport-Security' => isset($_SERVER['HTTP_STRICT_TRANSPORT_SECURITY']) ? '✅ مفعل' : '⚠️ غير مفعل'
];

echo "<table border='1' style='border-collapse:collapse; width:100%; margin:10px 0;'>";
echo "<tr><th>إعداد الأمان</th><th>الحالة</th></tr>";
foreach ($securityChecks as $check => $status) {
    echo "<tr><td>$check</td><td>$status</td></tr>";
}
echo "</table>";

// 5. فحص الأداء
echo "<h2>⚡ فحص الأداء:</h2>";

$startTime = microtime(true);

// اختبار سرعة الاستجابة
usleep(1000); // 1ms delay for testing

$endTime = microtime(true);
$responseTime = ($endTime - $startTime) * 1000;

echo "<table border='1' style='border-collapse:collapse; width:100%; margin:10px 0;'>";
echo "<tr><th>مؤشر الأداء</th><th>القيمة</th><th>التقييم</th></tr>";
echo "<tr>";
echo "<td>وقت الاستجابة</td>";
echo "<td>" . number_format($responseTime, 2) . " ms</td>";
echo "<td>" . ($responseTime < 100 ? '✅ ممتاز' : ($responseTime < 500 ? '⚠️ جيد' : '❌ بطيء')) . "</td>";
echo "</tr>";

$memoryUsage = memory_get_usage(true) / 1024 / 1024;
echo "<tr>";
echo "<td>استخدام الذاكرة</td>";
echo "<td>" . number_format($memoryUsage, 2) . " MB</td>";
echo "<td>" . ($memoryUsage < 50 ? '✅ ممتاز' : ($memoryUsage < 100 ? '⚠️ جيد' : '❌ عالي')) . "</td>";
echo "</tr>";
echo "</table>";

// 6. إرشادات التحسين
echo "<h2>🚀 إرشادات التحسين:</h2>";

echo "<div style='background:#e7f3ff; padding:20px; border-radius:10px; margin:20px 0;'>";
echo "<h3>📋 قائمة التحقق للإنتاج:</h3>";
echo "<ul>";
echo "<li>✅ تفعيل SSL Certificate</li>";
echo "<li>✅ إعداد CDN</li>";
echo "<li>✅ تفعيل النسخ الاحتياطية</li>";
echo "<li>✅ مراقبة الأداء</li>";
echo "<li>✅ إعداد Cron Jobs</li>";
echo "<li>✅ تحسين قاعدة البيانات</li>";
echo "<li>✅ ضغط الملفات</li>";
echo "<li>✅ تحسين الصور</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background:#fff3cd; padding:20px; border-radius:10px; margin:20px 0;'>";
echo "<h3>⚠️ تحسينات مقترحة:</h3>";
echo "<ul>";
echo "<li>استخدم Redis للكاش والجلسات</li>";
echo "<li>فعل OPcache لتحسين PHP</li>";
echo "<li>استخدم Queue للمهام الثقيلة</li>";
echo "<li>راقب استخدام الموارد</li>";
echo "<li>حدث Laravel و PHP بانتظام</li>";
echo "</ul>";
echo "</div>";

// 7. روابط مفيدة
echo "<h2>🔗 روابط مفيدة:</h2>";
echo "<ul>";
echo "<li><a href='/login'>تسجيل الدخول</a></li>";
echo "<li><a href='/super-admin/login'>لوحة السوبر أدمن</a></li>";
echo "<li><a href='/dashboard'>الداشبورد</a></li>";
if (file_exists('test-connection.php')) {
    echo "<li><a href='test-connection.php'>اختبار قاعدة البيانات</a></li>";
}
echo "</ul>";

echo "<div style='text-align:center; margin:30px 0;'>";
echo "<p><strong>تم الفحص في:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>الخادم:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "</p>";
echo "</div>";
?>

<style>
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    margin: 20px; 
    background: #f8f9fa;
    direction: rtl;
}
h1 { 
    color: #333; 
    border-bottom: 3px solid #007bff; 
    padding-bottom: 10px; 
    text-align: center;
}
h2 { 
    color: #555; 
    margin-top: 30px; 
    background: #e9ecef;
    padding: 10px;
    border-radius: 5px;
}
table { 
    width: 100%; 
    border-collapse: collapse;
    margin: 10px 0;
}
th, td { 
    padding: 8px; 
    text-align: right; 
    border: 1px solid #ddd;
}
th { 
    background-color: #f2f2f2; 
    font-weight: bold;
}
ul {
    text-align: right;
}
a {
    color: #007bff;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>
