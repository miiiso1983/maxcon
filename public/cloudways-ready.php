<?php
/**
 * فحص جاهزية المشروع للنشر على Cloudways
 */

echo "<h1>✅ فحص جاهزية المشروع لـ Cloudways</h1>";

$allChecks = [];
$passedChecks = 0;
$totalChecks = 0;

// 1. فحص الملفات الأساسية
echo "<h2>📁 فحص الملفات الأساسية:</h2>";

$requiredFiles = [
    '.env.cloudways' => 'ملف إعدادات Cloudways',
    'composer.json' => 'ملف Composer',
    'public/.htaccess' => 'ملف Apache Configuration',
    'cloudways-deployment-info.txt' => 'معلومات النشر',
    'README-CLOUDWAYS.md' => 'دليل النشر',
    'CLOUDWAYS-DEPLOYMENT-GUIDE.md' => 'الدليل الشامل'
];

echo "<table border='1' style='border-collapse:collapse; width:100%; margin:10px 0;'>";
echo "<tr><th>الملف</th><th>الوصف</th><th>الحالة</th></tr>";

foreach ($requiredFiles as $file => $description) {
    $exists = file_exists($file);
    $status = $exists ? '✅ موجود' : '❌ غير موجود';
    $color = $exists ? 'green' : 'red';
    
    echo "<tr>";
    echo "<td><strong>$file</strong></td>";
    echo "<td>$description</td>";
    echo "<td style='color:$color;'>$status</td>";
    echo "</tr>";
    
    $allChecks[] = ['name' => "ملف $file", 'status' => $exists];
    $totalChecks++;
    if ($exists) $passedChecks++;
}
echo "</table>";

// 2. فحص إعدادات PHP
echo "<h2>🐘 فحص إعدادات PHP:</h2>";

$phpChecks = [
    'PHP Version' => [
        'current' => phpversion(),
        'required' => '8.1.0',
        'check' => version_compare(phpversion(), '8.1.0', '>=')
    ],
    'Memory Limit' => [
        'current' => ini_get('memory_limit'),
        'required' => '512M',
        'check' => (int)ini_get('memory_limit') >= 512
    ],
    'Max Execution Time' => [
        'current' => ini_get('max_execution_time') . 's',
        'required' => '300s',
        'check' => ini_get('max_execution_time') >= 300
    ],
    'Upload Max Size' => [
        'current' => ini_get('upload_max_filesize'),
        'required' => '100M',
        'check' => (int)ini_get('upload_max_filesize') >= 100
    ]
];

echo "<table border='1' style='border-collapse:collapse; width:100%; margin:10px 0;'>";
echo "<tr><th>الإعداد</th><th>القيمة الحالية</th><th>المطلوب</th><th>الحالة</th></tr>";

foreach ($phpChecks as $setting => $info) {
    $status = $info['check'] ? '✅ مناسب' : '⚠️ يحتاج تحديث';
    $color = $info['check'] ? 'green' : 'orange';
    
    echo "<tr>";
    echo "<td><strong>$setting</strong></td>";
    echo "<td>{$info['current']}</td>";
    echo "<td>{$info['required']}</td>";
    echo "<td style='color:$color;'>$status</td>";
    echo "</tr>";
    
    $allChecks[] = ['name' => "إعداد PHP: $setting", 'status' => $info['check']];
    $totalChecks++;
    if ($info['check']) $passedChecks++;
}
echo "</table>";

// 3. فحص مجلدات Laravel
echo "<h2>📂 فحص مجلدات Laravel:</h2>";

$laravelDirs = [
    'storage' => 'مجلد التخزين',
    'storage/logs' => 'مجلد السجلات',
    'storage/framework' => 'مجلد Framework',
    'storage/framework/cache' => 'مجلد الكاش',
    'storage/framework/sessions' => 'مجلد الجلسات',
    'storage/framework/views' => 'مجلد العروض المترجمة',
    'bootstrap/cache' => 'مجلد Bootstrap Cache'
];

echo "<table border='1' style='border-collapse:collapse; width:100%; margin:10px 0;'>";
echo "<tr><th>المجلد</th><th>الوصف</th><th>موجود</th><th>قابل للكتابة</th></tr>";

foreach ($laravelDirs as $dir => $description) {
    $exists = is_dir($dir);
    $writable = $exists ? is_writable($dir) : false;
    
    $existsStatus = $exists ? '✅ نعم' : '❌ لا';
    $writableStatus = $writable ? '✅ نعم' : '❌ لا';
    
    $existsColor = $exists ? 'green' : 'red';
    $writableColor = $writable ? 'green' : 'red';
    
    echo "<tr>";
    echo "<td><strong>$dir</strong></td>";
    echo "<td>$description</td>";
    echo "<td style='color:$existsColor;'>$existsStatus</td>";
    echo "<td style='color:$writableColor;'>$writableStatus</td>";
    echo "</tr>";
    
    $dirCheck = $exists && $writable;
    $allChecks[] = ['name' => "مجلد $dir", 'status' => $dirCheck];
    $totalChecks++;
    if ($dirCheck) $passedChecks++;
}
echo "</table>";

// 4. فحص إعدادات Cloudways
echo "<h2>☁️ فحص إعدادات Cloudways:</h2>";

if (file_exists('.env.cloudways')) {
    $envContent = file_get_contents('.env.cloudways');
    
    $cloudwaysChecks = [
        'APP_ENV=production' => strpos($envContent, 'APP_ENV=production') !== false,
        'APP_DEBUG=false' => strpos($envContent, 'APP_DEBUG=false') !== false,
        'SESSION_SECURE_COOKIE=true' => strpos($envContent, 'SESSION_SECURE_COOKIE=true') !== false,
        'DB_HOST configured' => strpos($envContent, 'DB_HOST=your_cloudways_db_host') !== false,
        'MAIL_HOST configured' => strpos($envContent, 'MAIL_HOST=smtp.mailgun.org') !== false
    ];
    
    echo "<table border='1' style='border-collapse:collapse; width:100%; margin:10px 0;'>";
    echo "<tr><th>الإعداد</th><th>الحالة</th></tr>";
    
    foreach ($cloudwaysChecks as $setting => $status) {
        $statusText = $status ? '✅ مُعد' : '⚠️ يحتاج تحديث';
        $color = $status ? 'green' : 'orange';
        
        echo "<tr>";
        echo "<td><strong>$setting</strong></td>";
        echo "<td style='color:$color;'>$statusText</td>";
        echo "</tr>";
        
        $allChecks[] = ['name' => "إعداد Cloudways: $setting", 'status' => $status];
        $totalChecks++;
        if ($status) $passedChecks++;
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>❌ ملف .env.cloudways غير موجود</p>";
    $allChecks[] = ['name' => 'ملف .env.cloudways', 'status' => false];
    $totalChecks++;
}

// 5. فحص Composer
echo "<h2>📦 فحص Composer:</h2>";

$composerChecks = [
    'composer.json exists' => file_exists('composer.json'),
    'vendor directory exists' => is_dir('vendor'),
    'autoload.php exists' => file_exists('vendor/autoload.php')
];

echo "<table border='1' style='border-collapse:collapse; width:100%; margin:10px 0;'>";
echo "<tr><th>الفحص</th><th>الحالة</th></tr>";

foreach ($composerChecks as $check => $status) {
    $statusText = $status ? '✅ موجود' : '❌ غير موجود';
    $color = $status ? 'green' : 'red';
    
    echo "<tr>";
    echo "<td><strong>$check</strong></td>";
    echo "<td style='color:$color;'>$statusText</td>";
    echo "</tr>";
    
    $allChecks[] = ['name' => "Composer: $check", 'status' => $status];
    $totalChecks++;
    if ($status) $passedChecks++;
}
echo "</table>";

// 6. حساب النتيجة النهائية
$percentage = round(($passedChecks / $totalChecks) * 100, 2);

echo "<h2>📊 النتيجة النهائية:</h2>";

if ($percentage >= 90) {
    $grade = 'ممتاز';
    $color = '#28a745';
    $bgColor = '#d4edda';
    $message = 'المشروع جاهز تماماً للنشر على Cloudways!';
} elseif ($percentage >= 75) {
    $grade = 'جيد';
    $color = '#ffc107';
    $bgColor = '#fff3cd';
    $message = 'المشروع جاهز للنشر مع بعض التحسينات البسيطة.';
} elseif ($percentage >= 50) {
    $grade = 'مقبول';
    $color = '#fd7e14';
    $bgColor = '#ffeaa7';
    $message = 'المشروع يحتاج بعض التحسينات قبل النشر.';
} else {
    $grade = 'يحتاج عمل';
    $color = '#dc3545';
    $bgColor = '#f8d7da';
    $message = 'المشروع يحتاج عمل كبير قبل النشر.';
}

echo "<div style='background:$bgColor; border:1px solid $color; padding:20px; border-radius:10px; margin:20px 0;'>";
echo "<h3 style='color:$color; margin:0 0 10px 0;'>🎯 النتيجة: $percentage% - $grade</h3>";
echo "<p style='margin:0; font-size:16px;'>$message</p>";
echo "<p style='margin:10px 0 0 0;'><strong>اجتاز:</strong> $passedChecks من $totalChecks فحص</p>";
echo "</div>";

// 7. قائمة المشاكل (إن وجدت)
$failedChecks = array_filter($allChecks, function($check) {
    return !$check['status'];
});

if (!empty($failedChecks)) {
    echo "<h2>⚠️ المشاكل التي تحتاج إصلاح:</h2>";
    echo "<ul style='color:#dc3545;'>";
    foreach ($failedChecks as $check) {
        echo "<li>{$check['name']}</li>";
    }
    echo "</ul>";
}

// 8. خطوات النشر التالية
echo "<h2>🚀 خطوات النشر التالية:</h2>";

echo "<div style='background:#e7f3ff; padding:20px; border-radius:10px; margin:20px 0;'>";
echo "<h3>📋 قائمة المهام:</h3>";
echo "<ol>";
echo "<li>✅ تحضير المشروع محلياً (مكتمل)</li>";
echo "<li>🔄 إنشاء خادم في Cloudways</li>";
echo "<li>🔄 رفع المشروع عبر Git أو SFTP</li>";
echo "<li>🔄 تحديث إعدادات قاعدة البيانات</li>";
echo "<li>🔄 تشغيل أوامر Laravel</li>";
echo "<li>🔄 إعداد SSL Certificate</li>";
echo "<li>🔄 تفعيل CDN والنسخ الاحتياطية</li>";
echo "<li>🔄 اختبار النظام</li>";
echo "</ol>";
echo "</div>";

// 9. روابط مفيدة
echo "<h2>🔗 روابط مفيدة:</h2>";

echo "<div style='display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:15px; margin:20px 0;'>";

$links = [
    ['url' => 'cloudways-check.php', 'title' => 'فحص النشر', 'color' => '#007bff'],
    ['url' => 'cloudways-optimize.php', 'title' => 'تحسين الأداء', 'color' => '#28a745'],
    ['url' => '/dashboard', 'title' => 'الداشبورد', 'color' => '#6f42c1'],
    ['url' => 'README-CLOUDWAYS.md', 'title' => 'دليل النشر', 'color' => '#fd7e14']
];

foreach ($links as $link) {
    echo "<a href='{$link['url']}' style='background:{$link['color']}; color:white; padding:15px; text-decoration:none; border-radius:10px; text-align:center; display:block;'>";
    echo $link['title'];
    echo "</a>";
}

echo "</div>";

// 10. معلومات إضافية
echo "<div style='background:#f8f9fa; padding:20px; border-radius:10px; margin:20px 0; border:1px solid #dee2e6;'>";
echo "<h3>ℹ️ معلومات إضافية:</h3>";
echo "<ul>";
echo "<li><strong>تاريخ الفحص:</strong> " . date('Y-m-d H:i:s') . "</li>";
echo "<li><strong>إصدار PHP:</strong> " . phpversion() . "</li>";
echo "<li><strong>نظام التشغيل:</strong> " . php_uname('s') . "</li>";
echo "<li><strong>الذاكرة المستخدمة:</strong> " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB</li>";
echo "</ul>";
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
    font-weight: bold;
}
ul, ol {
    text-align: right;
}
a {
    text-decoration: none;
}
a:hover {
    opacity: 0.8;
}
</style>
