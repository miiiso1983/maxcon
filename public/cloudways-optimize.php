<?php
/**
 * تحسين الأداء على Cloudways
 */

echo "<h1>⚡ تحسين الأداء على Cloudways</h1>";

// التحقق من الصلاحيات
if (!function_exists('shell_exec')) {
    echo "<div style='background:#f8d7da; padding:15px; border-radius:5px; color:#721c24;'>";
    echo "<h2>❌ خطأ في الصلاحيات</h2>";
    echo "<p>لا يمكن تشغيل الأوامر. تحتاج صلاحيات shell_exec.</p>";
    echo "</div>";
    exit;
}

// 1. تنظيف الكاش
echo "<h2>🧹 تنظيف الكاش:</h2>";

$cacheCommands = [
    'php artisan config:clear' => 'مسح كاش الإعدادات',
    'php artisan cache:clear' => 'مسح كاش التطبيق',
    'php artisan route:clear' => 'مسح كاش المسارات',
    'php artisan view:clear' => 'مسح كاش العروض',
    'php artisan event:clear' => 'مسح كاش الأحداث'
];

foreach ($cacheCommands as $command => $description) {
    echo "<p>🔄 $description...</p>";
    $output = shell_exec($command . ' 2>&1');
    if ($output) {
        echo "<pre style='background:#f0f0f0; padding:10px; border-radius:5px; font-size:12px;'>" . htmlspecialchars($output) . "</pre>";
    } else {
        echo "<p style='color:green;'>✅ تم بنجاح</p>";
    }
}

// 2. تحسين للإنتاج
echo "<h2>🚀 تحسين للإنتاج:</h2>";

$optimizeCommands = [
    'php artisan config:cache' => 'كاش الإعدادات',
    'php artisan route:cache' => 'كاش المسارات',
    'php artisan view:cache' => 'كاش العروض',
    'php artisan event:cache' => 'كاش الأحداث',
    'composer dump-autoload --optimize' => 'تحسين Autoloader'
];

foreach ($optimizeCommands as $command => $description) {
    echo "<p>⚡ $description...</p>";
    $output = shell_exec($command . ' 2>&1');
    if ($output) {
        echo "<pre style='background:#f0f0f0; padding:10px; border-radius:5px; font-size:12px;'>" . htmlspecialchars($output) . "</pre>";
    } else {
        echo "<p style='color:green;'>✅ تم بنجاح</p>";
    }
}

// 3. فحص وإصلاح الصلاحيات
echo "<h2>🔐 فحص وإصلاح الصلاحيات:</h2>";

$directories = [
    'storage',
    'storage/logs',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'bootstrap/cache'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $writable = is_writable($dir);
        
        echo "<p>📁 $dir - صلاحيات: $perms - ";
        echo $writable ? "<span style='color:green;'>✅ قابل للكتابة</span>" : "<span style='color:red;'>❌ غير قابل للكتابة</span>";
        echo "</p>";
        
        if (!$writable) {
            echo "<p>🔧 محاولة إصلاح الصلاحيات...</p>";
            $chmodResult = shell_exec("chmod -R 755 $dir 2>&1");
            if ($chmodResult) {
                echo "<pre style='background:#fff3cd; padding:10px; border-radius:5px;'>" . htmlspecialchars($chmodResult) . "</pre>";
            } else {
                echo "<p style='color:green;'>✅ تم إصلاح الصلاحيات</p>";
            }
        }
    } else {
        echo "<p>📁 $dir - <span style='color:red;'>❌ غير موجود</span></p>";
        echo "<p>🔧 إنشاء المجلد...</p>";
        if (mkdir($dir, 0755, true)) {
            echo "<p style='color:green;'>✅ تم إنشاء المجلد</p>";
        } else {
            echo "<p style='color:red;'>❌ فشل في إنشاء المجلد</p>";
        }
    }
}

// 4. إنشاء رابط التخزين
echo "<h2>🔗 إنشاء رابط التخزين:</h2>";

if (!file_exists('public/storage')) {
    echo "<p>🔧 إنشاء رابط storage...</p>";
    $linkResult = shell_exec('php artisan storage:link 2>&1');
    if ($linkResult) {
        echo "<pre style='background:#f0f0f0; padding:10px; border-radius:5px;'>" . htmlspecialchars($linkResult) . "</pre>";
    } else {
        echo "<p style='color:green;'>✅ تم إنشاء الرابط</p>";
    }
} else {
    echo "<p style='color:green;'>✅ رابط storage موجود</p>";
}

// 5. تحسين قاعدة البيانات
echo "<h2>🗄️ تحسين قاعدة البيانات:</h2>";

try {
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        $app = require_once 'bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        
        // فحص الجداول
        $tables = DB::select('SHOW TABLES');
        echo "<p>📊 عدد الجداول: " . count($tables) . "</p>";
        
        // تحسين الجداول
        echo "<p>🔧 تحسين جداول قاعدة البيانات...</p>";
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            DB::statement("OPTIMIZE TABLE `$tableName`");
        }
        echo "<p style='color:green;'>✅ تم تحسين جميع الجداول</p>";
        
        // إحصائيات قاعدة البيانات
        $dbSize = DB::select("
            SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB' 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
        ")[0];
        
        echo "<p>💾 حجم قاعدة البيانات: " . $dbSize->{'DB Size in MB'} . " MB</p>";
        
    } else {
        echo "<p style='color:orange;'>⚠️ Laravel غير مثبت - تخطي تحسين قاعدة البيانات</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ خطأ في قاعدة البيانات: " . $e->getMessage() . "</p>";
}

// 6. فحص استخدام الموارد
echo "<h2>📊 فحص استخدام الموارد:</h2>";

$memoryUsage = memory_get_usage(true);
$memoryPeak = memory_get_peak_usage(true);
$memoryLimit = ini_get('memory_limit');

echo "<table border='1' style='border-collapse:collapse; width:100%; margin:10px 0;'>";
echo "<tr><th>المورد</th><th>الاستخدام الحالي</th><th>الحد الأقصى</th><th>النسبة</th></tr>";

// الذاكرة
$memoryUsageMB = round($memoryUsage / 1024 / 1024, 2);
$memoryLimitMB = (int)$memoryLimit;
$memoryPercent = round(($memoryUsageMB / $memoryLimitMB) * 100, 2);

echo "<tr>";
echo "<td>الذاكرة</td>";
echo "<td>{$memoryUsageMB} MB</td>";
echo "<td>{$memoryLimit}</td>";
echo "<td>{$memoryPercent}%</td>";
echo "</tr>";

// وقت التنفيذ
$executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
$maxExecutionTime = ini_get('max_execution_time');

echo "<tr>";
echo "<td>وقت التنفيذ</td>";
echo "<td>" . round($executionTime, 2) . "s</td>";
echo "<td>{$maxExecutionTime}s</td>";
echo "<td>" . round(($executionTime / $maxExecutionTime) * 100, 2) . "%</td>";
echo "</tr>";

echo "</table>";

// 7. تحسينات مقترحة
echo "<h2>💡 تحسينات مقترحة:</h2>";

echo "<div style='background:#e7f3ff; padding:20px; border-radius:10px; margin:20px 0;'>";
echo "<h3>🚀 تحسينات Cloudways:</h3>";
echo "<ul>";
echo "<li><strong>Redis:</strong> فعل Redis للكاش والجلسات</li>";
echo "<li><strong>CDN:</strong> استخدم CloudwaysCDN لتسريع الموقع</li>";
echo "<li><strong>OPcache:</strong> فعل OPcache في إعدادات PHP</li>";
echo "<li><strong>Monitoring:</strong> راقب الأداء باستمرار</li>";
echo "<li><strong>Backup:</strong> فعل النسخ الاحتياطية التلقائية</li>";
echo "<li><strong>SSL:</strong> تأكد من تفعيل SSL</li>";
echo "<li><strong>Cron Jobs:</strong> إعداد المهام المجدولة</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background:#fff3cd; padding:20px; border-radius:10px; margin:20px 0;'>";
echo "<h3>⚙️ إعدادات PHP المقترحة:</h3>";
echo "<ul>";
echo "<li><strong>memory_limit:</strong> 512M أو أكثر</li>";
echo "<li><strong>max_execution_time:</strong> 300</li>";
echo "<li><strong>upload_max_filesize:</strong> 100M</li>";
echo "<li><strong>post_max_size:</strong> 100M</li>";
echo "<li><strong>max_input_vars:</strong> 3000</li>";
echo "<li><strong>opcache.enable:</strong> 1</li>";
echo "<li><strong>opcache.memory_consumption:</strong> 256</li>";
echo "</ul>";
echo "</div>";

// 8. أوامر مفيدة
echo "<h2>🛠️ أوامر مفيدة:</h2>";

echo "<div style='background:#f8f9fa; padding:20px; border-radius:10px; margin:20px 0;'>";
echo "<h3>📝 أوامر Laravel المفيدة:</h3>";
echo "<pre style='background:#2d3748; color:#e2e8f0; padding:15px; border-radius:5px; overflow-x:auto;'>";
echo "# تنظيف شامل\n";
echo "php artisan optimize:clear\n\n";
echo "# تحسين للإنتاج\n";
echo "php artisan optimize\n\n";
echo "# مراقبة الطوابير\n";
echo "php artisan queue:work\n\n";
echo "# تشغيل المهام المجدولة\n";
echo "php artisan schedule:run\n\n";
echo "# فحص الصحة\n";
echo "php artisan about\n";
echo "</pre>";
echo "</div>";

// 9. تقرير نهائي
echo "<h2>📋 تقرير التحسين:</h2>";

echo "<div style='background:#d4edda; padding:20px; border-radius:10px; margin:20px 0;'>";
echo "<h3>✅ تم الانتهاء من التحسين!</h3>";
echo "<p><strong>الوقت:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>استخدام الذاكرة:</strong> {$memoryUsageMB} MB</p>";
echo "<p><strong>وقت التنفيذ:</strong> " . round($executionTime, 2) . "s</p>";
echo "</div>";

echo "<div style='text-align:center; margin:30px 0;'>";
echo "<a href='cloudways-check.php' style='background:#007bff; color:white; padding:15px 30px; text-decoration:none; border-radius:5px; margin:10px;'>فحص النشر</a>";
echo "<a href='/dashboard' style='background:#28a745; color:white; padding:15px 30px; text-decoration:none; border-radius:5px; margin:10px;'>الداشبورد</a>";
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
    border-bottom: 3px solid #28a745; 
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
}
th, td { 
    padding: 8px; 
    text-align: right; 
    border: 1px solid #ddd;
}
th { 
    background-color: #f2f2f2; 
}
pre { 
    background: #f8f9fa; 
    border: 1px solid #dee2e6; 
    border-radius: 4px; 
    padding: 10px;
    overflow-x: auto;
}
ul {
    text-align: right;
}
</style>
