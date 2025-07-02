<?php
echo "<h1>🔧 إصلاح Routes</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

try {
    echo "<h2>1. فحص ملف routes/web.php:</h2>";
    
    if (file_exists('routes/web.php')) {
        $webRoutes = file_get_contents('routes/web.php');
        echo "<span class='info'>ملف routes/web.php موجود</span><br>";
        
        // التحقق من وجود routes مهمة
        $requiredRoutes = [
            'dashboard' => "Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard')->middleware('auth');",
            'home' => "Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');",
            'login' => "Route::get('/login', [App\Http\Controllers\Web\AuthController::class, 'showLoginForm'])->name('login');",
            'login.post' => "Route::post('/login', [App\Http\Controllers\Web\AuthController::class, 'login']);",
            'logout' => "Route::post('/logout', [App\Http\Controllers\Web\AuthController::class, 'logout'])->name('logout');"
        ];
        
        $missingRoutes = [];
        foreach ($requiredRoutes as $name => $route) {
            if (strpos($webRoutes, $name) === false) {
                $missingRoutes[] = $route;
            }
        }
        
        if (!empty($missingRoutes)) {
            echo "<h3>إضافة Routes مفقودة:</h3>";
            $newRoutes = "\n\n// Routes المضافة تلقائياً\n" . implode("\n", $missingRoutes);
            $webRoutes .= $newRoutes;
            file_put_contents('routes/web.php', $webRoutes);
            echo "<span class='success'>✅ تم إضافة " . count($missingRoutes) . " routes</span><br>";
        } else {
            echo "<span class='info'>جميع Routes الأساسية موجودة</span><br>";
        }
        
    } else {
        echo "<span class='error'>❌ ملف routes/web.php غير موجود</span><br>";
        
        // إنشاء ملف routes/web.php جديد
        $newWebRoutes = '<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// الصفحة الرئيسية
Route::get("/", function () {
    return redirect()->route("login");
});

// Authentication Routes
Route::get("/login", [AuthController::class, "showLoginForm"])->name("login");
Route::post("/login", [AuthController::class, "login"]);
Route::post("/logout", [AuthController::class, "logout"])->name("logout");

// Protected Routes
Route::middleware("auth")->group(function () {
    Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard");
    Route::get("/home", [HomeController::class, "index"])->name("home");
    
    // Items Routes
    Route::resource("items", ItemController::class);
    
    // Categories Routes
    Route::resource("categories", CategoryController::class);
    
    // Suppliers Routes
    Route::resource("suppliers", SupplierController::class);
});

// Super Admin Routes
Route::prefix("super-admin")->name("super-admin.")->group(function () {
    Route::get("/login", [AuthController::class, "showSuperAdminLoginForm"])->name("login");
    Route::post("/login", [AuthController::class, "superAdminLogin"]);
    
    Route::middleware("auth:super_admin")->group(function () {
        Route::get("/dashboard", [AuthController::class, "superAdminDashboard"])->name("dashboard");
        Route::post("/logout", [AuthController::class, "superAdminLogout"])->name("logout");
    });
});
';
        
        if (!is_dir('routes')) {
            mkdir('routes', 0755, true);
        }
        
        file_put_contents('routes/web.php', $newWebRoutes);
        echo "<span class='success'>✅ تم إنشاء ملف routes/web.php جديد</span><br>";
    }
    
    echo "<h2>2. فحص AuthController:</h2>";
    
    if (!file_exists('app/Http/Controllers/Web/AuthController.php')) {
        echo "<span class='warning'>⚠️ AuthController مفقود، سيتم إنشاؤه</span><br>";
        
        if (!is_dir('app/Http/Controllers/Web')) {
            mkdir('app/Http/Controllers/Web', 0755, true);
        }
        
        $authController = '<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view("auth.login");
    }
    
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);
        
        $credentials = $request->only("email", "password");
        
        if (Auth::attempt($credentials, $request->filled("remember"))) {
            $request->session()->regenerate();
            return redirect()->intended(route("dashboard"));
        }
        
        return back()->withErrors([
            "email" => "بيانات الدخول غير صحيحة.",
        ])->onlyInput("email");
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route("login");
    }
    
    public function showSuperAdminLoginForm()
    {
        return view("super-admin.auth.login");
    }
    
    public function superAdminLogin(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);
        
        // البحث عن Super Admin
        $superAdmin = DB::table("super_admins")
            ->where("email", $request->email)
            ->first();
        
        if ($superAdmin && Hash::check($request->password, $superAdmin->password)) {
            // تسجيل دخول Super Admin
            session(["super_admin" => $superAdmin]);
            return redirect()->route("super-admin.dashboard");
        }
        
        return back()->withErrors([
            "email" => "بيانات الدخول غير صحيحة.",
        ])->onlyInput("email");
    }
    
    public function superAdminDashboard()
    {
        $superAdmin = session("super_admin");
        if (!$superAdmin) {
            return redirect()->route("super-admin.login");
        }
        
        return view("super-admin.dashboard", compact("superAdmin"));
    }
    
    public function superAdminLogout(Request $request)
    {
        $request->session()->forget("super_admin");
        return redirect()->route("super-admin.login");
    }
}
';
        
        file_put_contents('app/Http/Controllers/Web/AuthController.php', $authController);
        echo "<span class='success'>✅ تم إنشاء AuthController</span><br>";
    } else {
        echo "<span class='info'>AuthController موجود</span><br>";
    }
    
    echo "<h2>3. مسح الكاش:</h2>";
    
    try {
        // مسح route cache
        if (file_exists('bootstrap/cache/routes-v7.php')) {
            unlink('bootstrap/cache/routes-v7.php');
            echo "<span class='success'>✅ تم مسح route cache</span><br>";
        }
        
        // مسح config cache
        if (file_exists('bootstrap/cache/config.php')) {
            unlink('bootstrap/cache/config.php');
            echo "<span class='success'>✅ تم مسح config cache</span><br>";
        }
        
        // مسح view cache
        $viewFiles = glob('storage/framework/views/*.php');
        foreach ($viewFiles as $file) {
            unlink($file);
        }
        echo "<span class='success'>✅ تم مسح view cache</span><br>";
        
    } catch (Exception $e) {
        echo "<span class='error'>❌ خطأ في مسح الكاش: " . $e->getMessage() . "</span><br>";
    }
    
    echo "<h2>✅ تم إصلاح جميع مشاكل Routes!</h2>";
    
    echo "<h3>🔗 اختبار النظام:</h3>";
    echo "<a href='/login' target='_blank' style='display:inline-block;padding:10px;background:#007cba;color:white;text-decoration:none;margin:5px;border-radius:5px;'>تسجيل دخول جديد</a><br>";
    
    echo "<h3>🔑 بيانات تسجيل الدخول:</h3>";
    echo "<div style='background:#f8f9fa;padding:15px;border-radius:5px;'>";
    echo "<strong>مستخدم اختبار:</strong> test@test.com / 123456<br>";
    echo "<strong>Super Admin:</strong> superadmin@pharmacy-erp.com / SuperAdmin@2024";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<span class='error'>❌ خطأ عام: " . $e->getMessage() . "</span><br>";
}

echo "<h3>⚠️ الخطوات التالية:</h3>";
echo "<ol>";
echo "<li>شغل الأمر: <code>php artisan route:clear</code></li>";
echo "<li>شغل الأمر: <code>php artisan config:clear</code></li>";
echo "<li>شغل الأمر: <code>php artisan view:clear</code></li>";
echo "<li>جرب تسجيل الدخول مرة أخرى</li>";
echo "</ol>";
?>
