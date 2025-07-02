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
