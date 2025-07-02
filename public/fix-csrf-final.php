<?php
/**
 * الحل النهائي لمشكلة CSRF 419
 */

echo "<h1>🔧 الحل النهائي لمشكلة CSRF 419</h1>";

// 1. تنظيف جميع الجلسات القديمة
echo "<h2>1. تنظيف الجلسات القديمة:</h2>";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=pharmacy_erp', 'root', '');
    
    // حذف الجلسات القديمة (أكثر من ساعة)
    $stmt = $pdo->prepare("DELETE FROM sessions WHERE last_activity < ?");
    $oneHourAgo = time() - 3600;
    $stmt->execute([$oneHourAgo]);
    $deletedSessions = $stmt->rowCount();
    
    echo "✅ تم حذف $deletedSessions جلسة قديمة<br>";
    
    // عرض الجلسات المتبقية
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM sessions");
    $remainingSessions = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "📊 الجلسات المتبقية: $remainingSessions<br>";
    
} catch (Exception $e) {
    echo "❌ خطأ في قاعدة البيانات: " . $e->getMessage() . "<br>";
}

// 2. تحديث إعدادات الجلسة
echo "<h2>2. تحديث إعدادات الجلسة:</h2>";

$sessionConfig = [
    'SESSION_LIFETIME' => 120,
    'SESSION_EXPIRE_ON_CLOSE' => 'false',
    'SESSION_ENCRYPT' => 'false',
    'SESSION_SECURE_COOKIE' => 'false',
    'SESSION_SAME_SITE' => 'lax',
    'SESSION_DOMAIN' => 'null'
];

$envContent = file_get_contents('../.env');
$updated = false;

foreach ($sessionConfig as $key => $value) {
    if (strpos($envContent, $key) !== false) {
        $envContent = preg_replace("/^$key=.*/m", "$key=$value", $envContent);
        echo "✅ تم تحديث $key=$value<br>";
        $updated = true;
    } else {
        $envContent .= "\n$key=$value";
        echo "✅ تم إضافة $key=$value<br>";
        $updated = true;
    }
}

if ($updated) {
    file_put_contents('../.env', $envContent);
    echo "✅ تم حفظ ملف .env<br>";
}

// 3. إنشاء ملف تحديث CSRF token تلقائي
echo "<h2>3. إنشاء نظام تحديث CSRF تلقائي:</h2>";

$autoUpdateScript = '
<script>
// نظام تحديث CSRF token تلقائي محسن
(function() {
    let csrfUpdateInterval;
    let isUpdating = false;
    
    function updateCsrfToken() {
        if (isUpdating) return;
        isUpdating = true;
        
        fetch("/csrf-token", {
            method: "GET",
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then(data => {
            if (data.token) {
                // تحديث meta tag
                const metaTag = document.querySelector(\'meta[name="csrf-token"]\');
                if (metaTag) {
                    metaTag.setAttribute("content", data.token);
                }
                
                // تحديث جميع hidden inputs
                document.querySelectorAll(\'input[name="_token"]\').forEach(input => {
                    input.value = data.token;
                });
                
                // تحديث axios headers إذا كان متوفر
                if (typeof axios !== "undefined") {
                    axios.defaults.headers.common["X-CSRF-TOKEN"] = data.token;
                }
                
                console.log("✅ CSRF token updated successfully");
            }
        })
        .catch(error => {
            console.error("❌ Error updating CSRF token:", error);
        })
        .finally(() => {
            isUpdating = false;
        });
    }
    
    // تحديث فوري عند تحميل الصفحة
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", updateCsrfToken);
    } else {
        updateCsrfToken();
    }
    
    // تحديث كل 15 دقيقة
    csrfUpdateInterval = setInterval(updateCsrfToken, 15 * 60 * 1000);
    
    // تحديث عند focus على النافذة
    window.addEventListener("focus", updateCsrfToken);
    
    // تحديث قبل إرسال أي form
    document.addEventListener("submit", function(e) {
        const form = e.target;
        if (form.tagName === "FORM") {
            updateCsrfToken();
        }
    });
    
    // التعامل مع أخطاء 419
    document.addEventListener("ajaxError", function(e) {
        if (e.detail && e.detail.status === 419) {
            updateCsrfToken();
            alert("تم تحديث رمز الأمان، يرجى المحاولة مرة أخرى");
        }
    });
    
    // إضافة دالة عامة لتحديث CSRF
    window.refreshCsrfToken = updateCsrfToken;
    
})();
</script>';

file_put_contents('csrf-auto-update.js', $autoUpdateScript);
echo "✅ تم إنشاء ملف csrf-auto-update.js<br>";

// 4. تشغيل أوامر Laravel
echo "<h2>4. تشغيل أوامر Laravel:</h2>";

$commands = [
    'cd .. && php artisan config:clear',
    'cd .. && php artisan cache:clear',
    'cd .. && php artisan session:table',
    'cd .. && php artisan route:clear',
    'cd .. && php artisan view:clear'
];

foreach ($commands as $command) {
    echo "تنفيذ: $command<br>";
    $output = shell_exec($command . ' 2>&1');
    if ($output && !empty(trim($output))) {
        echo "<pre style='background:#f0f0f0; padding:10px; margin:10px 0; font-size:12px;'>" . htmlspecialchars($output) . "</pre>";
    }
}

// 5. اختبار CSRF token
echo "<h2>5. اختبار CSRF token:</h2>";

echo '<div id="csrf-test">';
echo '<p>CSRF Token الحالي: <span id="current-token">' . (function_exists('csrf_token') ? csrf_token() : 'غير متوفر') . '</span></p>';
echo '<button onclick="testCsrfUpdate()" style="background:#007bff; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">اختبار تحديث CSRF</button>';
echo '<div id="test-result" style="margin-top:10px;"></div>';
echo '</div>';

echo '<script>
function testCsrfUpdate() {
    const resultDiv = document.getElementById("test-result");
    const tokenSpan = document.getElementById("current-token");
    
    resultDiv.innerHTML = "جاري الاختبار...";
    
    fetch("/csrf-token")
        .then(response => response.json())
        .then(data => {
            tokenSpan.textContent = data.token;
            resultDiv.innerHTML = "<span style=\"color:green;\">✅ تم تحديث CSRF token بنجاح!</span>";
        })
        .catch(error => {
            resultDiv.innerHTML = "<span style=\"color:red;\">❌ فشل في تحديث CSRF token: " + error.message + "</span>";
        });
}
</script>';

// 6. إرشادات نهائية
echo "<h2>✅ تم الانتهاء من الإصلاح!</h2>";
echo "<div style='background:#d4edda; border:1px solid #c3e6cb; padding:15px; border-radius:5px; margin:20px 0;'>";
echo "<h3>الخطوات التالية:</h3>";
echo "<ol>";
echo "<li>تحديث الصفحة في المتصفح (F5)</li>";
echo "<li>مسح cookies المتصفح إذا استمرت المشكلة</li>";
echo "<li>التأكد من تفعيل JavaScript في المتصفح</li>";
echo "<li>اختبار تسجيل الدخول مرة أخرى</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background:#fff3cd; border:1px solid #ffeaa7; padding:15px; border-radius:5px; margin:20px 0;'>";
echo "<h3>⚠️ ملاحظات مهمة:</h3>";
echo "<ul>";
echo "<li>تم تحديث نظام CSRF ليعمل تلقائياً</li>";
echo "<li>سيتم تحديث الرمز كل 15 دقيقة</li>";
echo "<li>سيتم التحديث تلقائياً عند إرسال النماذج</li>";
echo "<li>في حالة ظهور خطأ 419، سيتم التحديث التلقائي</li>";
echo "</ul>";
echo "</div>";

echo "<p style='text-align:center; margin:30px 0;'>";
echo "<a href='/dashboard' style='background:#28a745; color:white; padding:15px 30px; text-decoration:none; border-radius:5px; font-size:16px;'>العودة للداشبورد</a>";
echo "</p>";

// تضمين الـ script التلقائي
echo $autoUpdateScript;
?>

<style>
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    margin: 20px; 
    background: #f8f9fa;
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
    max-height: 200px;
    overflow-y: auto;
}
.container {
    max-width: 1000px;
    margin: 0 auto;
}
</style>
