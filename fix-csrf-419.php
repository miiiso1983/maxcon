<?php
/**
 * إصلاح مشكلة CSRF 419 - Page Expired
 * هذا الملف يحل مشاكل انتهاء صلاحية الجلسة والـ CSRF Token
 */

echo "<h1>🔧 إصلاح مشكلة CSRF 419</h1>";

// 1. تنظيف الكاش والجلسات
echo "<h2>1. تنظيف الكاش والجلسات:</h2>";

$commands = [
    'php artisan config:clear',
    'php artisan cache:clear', 
    'php artisan session:table',
    'php artisan migrate',
    'php artisan route:clear',
    'php artisan view:clear'
];

foreach ($commands as $command) {
    echo "تنفيذ: $command<br>";
    $output = shell_exec($command . ' 2>&1');
    if ($output) {
        echo "<pre style='background:#f0f0f0; padding:10px; margin:10px 0;'>$output</pre>";
    }
}

// 2. التحقق من جدول الجلسات
echo "<h2>2. التحقق من جدول الجلسات:</h2>";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=pharmacy_erp', 'root', '');
    
    // التحقق من وجود جدول sessions
    $stmt = $pdo->query("SHOW TABLES LIKE 'sessions'");
    if ($stmt->rowCount() > 0) {
        echo "✅ جدول sessions موجود<br>";
        
        // عرض بنية الجدول
        $stmt = $pdo->query("DESCRIBE sessions");
        echo "<table border='1' style='border-collapse:collapse; margin:10px 0;'>";
        echo "<tr><th>العمود</th><th>النوع</th><th>Null</th><th>Key</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // عدد الجلسات النشطة
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM sessions");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "عدد الجلسات النشطة: $count<br>";
        
    } else {
        echo "❌ جدول sessions غير موجود - سيتم إنشاؤه<br>";
        
        // إنشاء جدول الجلسات
        $sql = "CREATE TABLE sessions (
            id varchar(255) NOT NULL,
            user_id bigint unsigned DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text,
            payload longtext NOT NULL,
            last_activity int NOT NULL,
            PRIMARY KEY (id),
            KEY sessions_user_id_index (user_id),
            KEY sessions_last_activity_index (last_activity)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "✅ تم إنشاء جدول sessions<br>";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في قاعدة البيانات: " . $e->getMessage() . "<br>";
}

// 3. إنشاء ملف middleware مخصص لحل مشكلة CSRF
echo "<h2>3. إنشاء middleware مخصص:</h2>";

$middlewareContent = '<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

class HandleCsrfErrors
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // إعادة توجيه مع رسالة خطأ واضحة
            if ($request->expectsJson()) {
                return response()->json([
                    "error" => "CSRF token mismatch",
                    "message" => "انتهت صلاحية الجلسة، يرجى تحديث الصفحة",
                    "redirect" => url()->current()
                ], 419);
            }
            
            return redirect()->back()
                ->withInput($request->except("_token"))
                ->withErrors([
                    "csrf" => "انتهت صلاحية الجلسة، يرجى المحاولة مرة أخرى"
                ]);
        }
    }
}';

if (!is_dir('app/Http/Middleware')) {
    mkdir('app/Http/Middleware', 0755, true);
}

file_put_contents('app/Http/Middleware/HandleCsrfErrors.php', $middlewareContent);
echo "✅ تم إنشاء HandleCsrfErrors middleware<br>";

// 4. تحديث bootstrap/app.php
echo "<h2>4. تحديث إعدادات التطبيق:</h2>";

$bootstrapContent = file_get_contents('bootstrap/app.php');

// إضافة middleware جديد إذا لم يكن موجود
if (strpos($bootstrapContent, 'HandleCsrfErrors') === false) {
    $newMiddleware = '        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\HandleCsrfErrors::class,
        ]);';
    
    $bootstrapContent = str_replace(
        '        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);',
        $newMiddleware,
        $bootstrapContent
    );
    
    file_put_contents('bootstrap/app.php', $bootstrapContent);
    echo "✅ تم تحديث bootstrap/app.php<br>";
} else {
    echo "✅ middleware موجود مسبقاً<br>";
}

// 5. إنشاء ملف JavaScript لحل مشاكل CSRF
echo "<h2>5. إنشاء ملف JavaScript للتعامل مع CSRF:</h2>";

$jsContent = '// حل مشاكل CSRF Token
document.addEventListener("DOMContentLoaded", function() {
    
    // تحديث CSRF token كل 30 دقيقة
    setInterval(function() {
        fetch("/csrf-token")
            .then(response => response.json())
            .then(data => {
                // تحديث جميع meta tags
                document.querySelector(\'meta[name="csrf-token"]\').setAttribute("content", data.token);
                
                // تحديث جميع hidden inputs
                document.querySelectorAll(\'input[name="_token"]\').forEach(function(input) {
                    input.value = data.token;
                });
                
                console.log("CSRF token updated");
            })
            .catch(error => {
                console.error("Error updating CSRF token:", error);
            });
    }, 30 * 60 * 1000); // كل 30 دقيقة
    
    // التعامل مع أخطاء CSRF في AJAX
    $(document).ajaxError(function(event, xhr, settings) {
        if (xhr.status === 419) {
            alert("انتهت صلاحية الجلسة، سيتم تحديث الصفحة");
            location.reload();
        }
    });
    
    // إضافة CSRF token لجميع النماذج تلقائياً
    $("form").each(function() {
        if (!$(this).find(\'input[name="_token"]\').length) {
            $(this).append(\'<input type="hidden" name="_token" value="\' + $(\'meta[name="csrf-token"]\').attr("content") + \'">\');
        }
    });
});';

if (!is_dir('public/js')) {
    mkdir('public/js', 0755, true);
}

file_put_contents('public/js/csrf-handler.js', $jsContent);
echo "✅ تم إنشاء ملف csrf-handler.js<br>";

// 6. إضافة route للحصول على CSRF token جديد
echo "<h2>6. إضافة route لتحديث CSRF token:</h2>";

$routeContent = '
// Route لتحديث CSRF token
Route::get("/csrf-token", function() {
    return response()->json([
        "token" => csrf_token()
    ]);
})->name("csrf.token");';

$webRoutes = file_get_contents('routes/web.php');
if (strpos($webRoutes, 'csrf-token') === false) {
    file_put_contents('routes/web.php', $webRoutes . $routeContent);
    echo "✅ تم إضافة route لتحديث CSRF token<br>";
} else {
    echo "✅ route موجود مسبقاً<br>";
}

// 7. تحديث layout لتضمين الـ JavaScript
echo "<h2>7. تحديث layout الرئيسي:</h2>";

$layoutPath = 'resources/views/layouts/app.blade.php';
if (file_exists($layoutPath)) {
    $layoutContent = file_get_contents($layoutPath);
    
    if (strpos($layoutContent, 'csrf-handler.js') === false) {
        // إضافة script قبل إغلاق body
        $scriptTag = '    <script src="{{ asset(\'js/csrf-handler.js\') }}"></script>
</body>';
        
        $layoutContent = str_replace('</body>', $scriptTag, $layoutContent);
        file_put_contents($layoutPath, $layoutContent);
        echo "✅ تم تحديث layout لتضمين csrf-handler.js<br>";
    } else {
        echo "✅ csrf-handler.js موجود مسبقاً في layout<br>";
    }
} else {
    echo "❌ ملف layout غير موجود<br>";
}

// 8. تنظيف نهائي
echo "<h2>8. تنظيف نهائي:</h2>";

$finalCommands = [
    'php artisan config:cache',
    'php artisan route:cache'
];

foreach ($finalCommands as $command) {
    echo "تنفيذ: $command<br>";
    $output = shell_exec($command . ' 2>&1');
    if ($output) {
        echo "<pre style='background:#f0f0f0; padding:10px; margin:10px 0;'>$output</pre>";
    }
}

echo "<h2>✅ تم الانتهاء من إصلاح مشكلة CSRF 419</h2>";
echo "<p><strong>الخطوات التالية:</strong></p>";
echo "<ul>";
echo "<li>تحديث الصفحة في المتصفح</li>";
echo "<li>مسح cookies المتصفح إذا استمرت المشكلة</li>";
echo "<li>التأكد من أن JavaScript مفعل في المتصفح</li>";
echo "<li>التحقق من أن الخادم يعمل بشكل صحيح</li>";
echo "</ul>";

echo "<p><a href='/dashboard' style='background:#007bff; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>العودة للداشبورد</a></p>";
?>

<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; }
h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
h2 { color: #555; margin-top: 30px; }
table { width: 100%; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
pre { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; }
</style>
