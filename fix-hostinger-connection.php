<?php
/**
 * حل مشاكل الاتصال بسيرفر Hostinger
 */

echo "<h1>🔧 حل مشاكل الاتصال بسيرفر Hostinger</h1>";

// 1. فحص إعدادات قاعدة البيانات
echo "<h2>1. فحص إعدادات قاعدة البيانات:</h2>";

$hostingerConfig = [
    'DB_HOST' => 'localhost',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'u123456789_pharmacy_erp',
    'DB_USERNAME' => 'u123456789_pharmacy',
    'DB_PASSWORD' => 'YourStrongPassword123!'
];

echo "<div style='background:#fff3cd; padding:15px; border-radius:5px; margin:10px 0;'>";
echo "<h3>⚠️ تحديث إعدادات قاعدة البيانات Hostinger:</h3>";
echo "<p>يجب تحديث هذه القيم بالقيم الصحيحة من لوحة تحكم Hostinger:</p>";
echo "<ul>";
foreach ($hostingerConfig as $key => $value) {
    echo "<li><strong>$key:</strong> $value</li>";
}
echo "</ul>";
echo "</div>";

// 2. إنشاء ملف اختبار الاتصال
echo "<h2>2. إنشاء ملف اختبار الاتصال:</h2>";

$testConnectionScript = '<?php
/**
 * اختبار الاتصال بقاعدة البيانات Hostinger
 */

// إعدادات قاعدة البيانات (يجب تحديثها)
$host = "localhost";
$dbname = "u123456789_pharmacy_erp";  // اسم قاعدة البيانات من Hostinger
$username = "u123456789_pharmacy";   // اسم المستخدم من Hostinger
$password = "YourStrongPassword123!"; // كلمة المرور من Hostinger

echo "<h1>اختبار الاتصال بقاعدة البيانات</h1>";

try {
    // محاولة الاتصال
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    echo "<div style=\"background:#d4edda; padding:15px; border-radius:5px; color:#155724;\">";
    echo "<h2>✅ نجح الاتصال بقاعدة البيانات!</h2>";
    echo "<p>تفاصيل الاتصال:</p>";
    echo "<ul>";
    echo "<li>الخادم: $host</li>";
    echo "<li>قاعدة البيانات: $dbname</li>";
    echo "<li>المستخدم: $username</li>";
    echo "<li>حالة الاتصال: نشط</li>";
    echo "</ul>";
    echo "</div>";
    
    // اختبار إنشاء جدول بسيط
    $pdo->exec("CREATE TABLE IF NOT EXISTS connection_test (
        id INT AUTO_INCREMENT PRIMARY KEY,
        test_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("INSERT INTO connection_test (test_time) VALUES (NOW())");
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM connection_test");
    $count = $stmt->fetch()[\"count\"];
    
    echo "<div style=\"background:#cce5ff; padding:10px; border-radius:5px; margin:10px 0;\">";
    echo "<p>✅ تم اختبار العمليات بنجاح - عدد السجلات: $count</p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style=\"background:#f8d7da; padding:15px; border-radius:5px; color:#721c24;\">";
    echo "<h2>❌ فشل الاتصال بقاعدة البيانات!</h2>";
    echo "<p><strong>رسالة الخطأ:</strong> " . $e->getMessage() . "</p>";
    echo "<h3>الحلول المقترحة:</h3>";
    echo "<ul>";
    echo "<li>تحقق من صحة اسم قاعدة البيانات</li>";
    echo "<li>تحقق من صحة اسم المستخدم وكلمة المرور</li>";
    echo "<li>تأكد من أن قاعدة البيانات تم إنشاؤها في لوحة تحكم Hostinger</li>";
    echo "<li>تحقق من أن المستخدم له صلاحيات على قاعدة البيانات</li>";
    echo "<li>تأكد من أن عنوان IP مسموح له بالوصول</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<h2>معلومات الخادم:</h2>";
echo "<ul>";
echo "<li>إصدار PHP: " . phpversion() . "</li>";
echo "<li>الوقت الحالي: " . date(\"Y-m-d H:i:s\") . "</li>";
echo "<li>المنطقة الزمنية: " . date_default_timezone_get() . "</li>";
echo "</ul>";
?>';

file_put_contents('test-hostinger-connection.php', $testConnectionScript);
echo "✅ تم إنشاء ملف test-hostinger-connection.php<br>";

// 3. إنشاء ملف .htaccess محسن
echo "<h2>3. إنشاء ملف .htaccess محسن لـ Hostinger:</h2>";

$htaccessContent = 'RewriteEngine On

# إعادة توجيه HTTPS (إجباري في الإنتاج)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# إعادة توجيه www
RewriteCond %{HTTP_HOST} !^www\. [NC]
RewriteRule ^(.*)$ https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Laravel Routes
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]

# منع الوصول للملفات الحساسة
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.lock">
    Order allow,deny
    Deny from all
</Files>

# تحسين الأداء
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
</IfModule>

# ضغط الملفات
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# حماية إضافية
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>';

file_put_contents('.htaccess', $htaccessContent);
echo "✅ تم إنشاء ملف .htaccess محسن<br>";

// 4. إنشاء ملف index.php للتحقق
echo "<h2>4. إنشاء ملف index.php للتحقق:</h2>";

$indexContent = '<?php
/**
 * نقطة دخول التطبيق - Hostinger
 */

// التحقق من وجود Laravel
if (file_exists(__DIR__."/vendor/autoload.php")) {
    require_once __DIR__."/vendor/autoload.php";
} else {
    die("❌ Laravel غير مثبت - يرجى تشغيل composer install");
}

// التحقق من ملف .env
if (!file_exists(__DIR__."/.env")) {
    die("❌ ملف .env غير موجود - يرجى نسخ .env.hostinger إلى .env");
}

// بدء التطبيق
$app = require_once __DIR__."/bootstrap/app.php";

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
?>';

file_put_contents('index.php', $indexContent);
echo "✅ تم إنشاء ملف index.php<br>";

// 5. إرشادات النشر على Hostinger
echo "<h2>5. إرشادات النشر على Hostinger:</h2>";

echo "<div style='background:#e7f3ff; padding:20px; border-radius:10px; margin:20px 0;'>";
echo "<h3>📋 خطوات النشر على Hostinger:</h3>";
echo "<ol>";
echo "<li><strong>رفع الملفات:</strong>";
echo "<ul>";
echo "<li>ارفع جميع ملفات المشروع إلى مجلد public_html</li>";
echo "<li>تأكد من رفع مجلد vendor (أو شغل composer install على السيرفر)</li>";
echo "</ul></li>";

echo "<li><strong>إعداد قاعدة البيانات:</strong>";
echo "<ul>";
echo "<li>أنشئ قاعدة بيانات جديدة من لوحة تحكم Hostinger</li>";
echo "<li>أنشئ مستخدم قاعدة بيانات وأعطه صلاحيات كاملة</li>";
echo "<li>احفظ اسم قاعدة البيانات واسم المستخدم وكلمة المرور</li>";
echo "</ul></li>";

echo "<li><strong>تحديث ملف .env:</strong>";
echo "<ul>";
echo "<li>انسخ ملف .env.hostinger إلى .env</li>";
echo "<li>حدث إعدادات قاعدة البيانات بالقيم الصحيحة</li>";
echo "<li>حدث APP_URL بعنوان موقعك</li>";
echo "<li>حدث معلومات البريد الإلكتروني</li>";
echo "</ul></li>";

echo "<li><strong>تشغيل الأوامر:</strong>";
echo "<ul>";
echo "<li>php artisan key:generate</li>";
echo "<li>php artisan migrate --force</li>";
echo "<li>php artisan db:seed --force</li>";
echo "<li>php artisan config:cache</li>";
echo "<li>php artisan route:cache</li>";
echo "</ul></li>";

echo "<li><strong>ضبط الصلاحيات:</strong>";
echo "<ul>";
echo "<li>chmod 755 storage/</li>";
echo "<li>chmod 755 bootstrap/cache/</li>";
echo "<li>chmod 644 .env</li>";
echo "</ul></li>";
echo "</ol>";
echo "</div>";

// 6. ملف تحديث الإعدادات
echo "<h2>6. إنشاء ملف تحديث الإعدادات:</h2>";

$updateConfigScript = '<?php
/**
 * تحديث إعدادات Hostinger
 */

echo "<h1>تحديث إعدادات Hostinger</h1>";

// قراءة ملف .env.hostinger
if (file_exists(".env.hostinger")) {
    $hostingerEnv = file_get_contents(".env.hostinger");
    
    // نسخ إلى .env
    file_put_contents(".env", $hostingerEnv);
    echo "<p>✅ تم نسخ إعدادات .env.hostinger إلى .env</p>";
    
    // تشغيل أوامر Laravel
    $commands = [
        "php artisan config:clear",
        "php artisan cache:clear",
        "php artisan route:clear",
        "php artisan view:clear"
    ];
    
    foreach ($commands as $command) {
        echo "<p>تنفيذ: $command</p>";
        $output = shell_exec($command . " 2>&1");
        if ($output) {
            echo "<pre>$output</pre>";
        }
    }
    
    echo "<div style=\"background:#d4edda; padding:15px; border-radius:5px; margin:20px 0;\">";
    echo "<h2>✅ تم تحديث الإعدادات بنجاح!</h2>";
    echo "<p>الخطوات التالية:</p>";
    echo "<ul>";
    echo "<li>تحديث إعدادات قاعدة البيانات في ملف .env</li>";
    echo "<li>تشغيل php artisan migrate --force</li>";
    echo "<li>تشغيل php artisan db:seed --force</li>";
    echo "</ul>";
    echo "</div>";
    
} else {
    echo "<p style=\"color:red;\">❌ ملف .env.hostinger غير موجود</p>";
}
?>';

file_put_contents('update-hostinger-config.php', $updateConfigScript);
echo "✅ تم إنشاء ملف update-hostinger-config.php<br>";

// 7. معلومات مهمة
echo "<h2>✅ تم إنشاء جميع الملفات المطلوبة!</h2>";

echo "<div style='background:#d1ecf1; padding:20px; border-radius:10px; margin:20px 0;'>";
echo "<h3>📁 الملفات المنشأة:</h3>";
echo "<ul>";
echo "<li><strong>test-hostinger-connection.php</strong> - اختبار الاتصال بقاعدة البيانات</li>";
echo "<li><strong>.htaccess</strong> - إعدادات الخادم المحسنة</li>";
echo "<li><strong>index.php</strong> - نقطة دخول التطبيق</li>";
echo "<li><strong>update-hostinger-config.php</strong> - تحديث الإعدادات</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background:#fff3cd; padding:20px; border-radius:10px; margin:20px 0;'>";
echo "<h3>⚠️ ملاحظات مهمة:</h3>";
echo "<ul>";
echo "<li>يجب تحديث إعدادات قاعدة البيانات في ملف .env بالقيم الصحيحة من Hostinger</li>";
echo "<li>تأكد من رفع جميع الملفات إلى مجلد public_html</li>";
echo "<li>شغل composer install على السيرفر إذا لم ترفع مجلد vendor</li>";
echo "<li>تأكد من ضبط الصلاحيات الصحيحة للمجلدات</li>";
echo "</ul>";
echo "</div>";

echo "<p style='text-align:center; margin:30px 0;'>";
echo "<a href='test-hostinger-connection.php' style='background:#007bff; color:white; padding:15px 30px; text-decoration:none; border-radius:5px; margin:10px;'>اختبار الاتصال</a>";
echo "<a href='update-hostinger-config.php' style='background:#28a745; color:white; padding:15px 30px; text-decoration:none; border-radius:5px; margin:10px;'>تحديث الإعدادات</a>";
echo "</p>";
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
pre { 
    background: #f8f9fa; 
    border: 1px solid #dee2e6; 
    border-radius: 4px; 
    padding: 10px;
    max-height: 200px;
    overflow-y: auto;
}
ul, ol {
    text-align: right;
}
</style>
