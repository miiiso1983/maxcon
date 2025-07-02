<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ToolsController extends Controller
{
    /**
     * عرض صفحة الأدوات الرئيسية
     */
    public function index()
    {
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_type' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
        ];

        return view('admin.tools.index', compact('systemInfo'));
    }

    /**
     * مدير قاعدة البيانات
     */
    public function databaseManager()
    {
        $tables = DB::select('SHOW TABLES');
        $databaseSize = 0; // TODO: حساب حجم قاعدة البيانات
        
        return view('admin.tools.database-manager', compact('tables', 'databaseSize'));
    }

    /**
     * مدير الذاكرة المؤقتة
     */
    public function cacheManager()
    {
        $cacheInfo = [
            'driver' => config('cache.default'),
            'size' => 0, // TODO: حساب حجم الذاكرة المؤقتة
        ];

        return view('admin.tools.cache-manager', compact('cacheInfo'));
    }

    /**
     * مسح الذاكرة المؤقتة
     */
    public function clearCache(Request $request)
    {
        $request->validate([
            'cache_type' => 'required|in:all,config,route,view,application'
        ]);

        try {
            switch ($request->cache_type) {
                case 'all':
                    Artisan::call('cache:clear');
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
                    break;
                case 'config':
                    Artisan::call('config:clear');
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    break;
                case 'view':
                    Artisan::call('view:clear');
                    break;
                case 'application':
                    Artisan::call('cache:clear');
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'تم مسح الذاكرة المؤقتة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مسح الذاكرة المؤقتة: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * عارض السجلات
     */
    public function logViewer(Request $request)
    {
        $logFile = $request->get('file', 'laravel.log');
        $logPath = storage_path('logs/' . $logFile);
        
        $logs = [];
        if (file_exists($logPath)) {
            $content = file_get_contents($logPath);
            // TODO: تحليل محتوى السجل وتنسيقه
            $logs = explode("\n", $content);
            $logs = array_slice(array_reverse($logs), 0, 100); // آخر 100 سطر
        }

        $logFiles = glob(storage_path('logs/*.log'));
        $logFiles = array_map('basename', $logFiles);

        return view('admin.tools.log-viewer', compact('logs', 'logFiles', 'logFile'));
    }

    /**
     * مراقب الطوابير
     */
    public function queueMonitor()
    {
        // TODO: تنفيذ مراقبة الطوابير
        $queueInfo = [
            'driver' => config('queue.default'),
            'pending_jobs' => 0,
            'failed_jobs' => 0,
        ];

        return view('admin.tools.queue-monitor', compact('queueInfo'));
    }
}
