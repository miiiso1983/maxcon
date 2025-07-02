<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use App\Models\SuperAdmin;
use App\Models\TenantUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class SecurityMonitoringService
{
    /**
     * تسجيل محاولة تسجيل دخول مشبوهة
     */
    public function logSuspiciousLoginAttempt(Request $request, string $email, string $reason): void
    {
        $data = [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'email' => $email,
            'reason' => $reason,
            'timestamp' => now()->toISOString(),
            'headers' => $request->headers->all(),
        ];

        Log::warning('Suspicious login attempt detected', $data);

        // تسجيل في قاعدة البيانات
        AdminAuditLog::createLog([
            'action' => 'suspicious_login',
            'description' => "محاولة تسجيل دخول مشبوهة: {$reason}",
            'metadata' => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'category' => 'authentication',
            'severity' => 'high',
            'status' => 'failed',
            'error_message' => $reason,
        ]);

        // زيادة عداد المحاولات المشبوهة
        $this->incrementSuspiciousAttempts($request->ip());
    }

    /**
     * فحص محاولات تسجيل الدخول المشبوهة
     */
    public function detectSuspiciousLogin(Request $request, string $email): array
    {
        $suspiciousIndicators = [];

        // فحص عدد المحاولات من نفس IP
        $ipAttempts = $this->getFailedAttemptsFromIP($request->ip());
        if ($ipAttempts > 10) {
            $suspiciousIndicators[] = "عدد كبير من المحاولات الفاشلة من نفس IP: {$ipAttempts}";
        }

        // فحص الموقع الجغرافي
        $location = $this->getLocationFromIP($request->ip());
        if ($this->isUnusualLocation($email, $location)) {
            $suspiciousIndicators[] = "تسجيل دخول من موقع غير معتاد: {$location['country']}";
        }

        // فحص User Agent
        if ($this->isSuspiciousUserAgent($request->userAgent())) {
            $suspiciousIndicators[] = "User Agent مشبوه";
        }

        // فحص التوقيت
        if ($this->isUnusualTime($email)) {
            $suspiciousIndicators[] = "تسجيل دخول في وقت غير معتاد";
        }

        // فحص سرعة المحاولات
        if ($this->isTooFastAttempts($request->ip())) {
            $suspiciousIndicators[] = "محاولات سريعة جداً";
        }

        return $suspiciousIndicators;
    }

    /**
     * زيادة عداد المحاولات المشبوهة
     */
    private function incrementSuspiciousAttempts(string $ip): void
    {
        $key = "suspicious_attempts:{$ip}";
        $attempts = Cache::get($key, 0);
        Cache::put($key, $attempts + 1, now()->addHours(24));

        // إذا تجاوز العدد حد معين، حظر IP مؤقتاً
        if ($attempts > 20) {
            $this->blockIP($ip, 'عدد كبير من المحاولات المشبوهة');
        }
    }

    /**
     * الحصول على عدد المحاولات الفاشلة من IP
     */
    private function getFailedAttemptsFromIP(string $ip): int
    {
        return Cache::get("failed_attempts:{$ip}", 0);
    }

    /**
     * الحصول على الموقع الجغرافي من IP
     */
    private function getLocationFromIP(string $ip): array
    {
        // يمكن استخدام خدمة مثل GeoIP أو MaxMind
        // هنا مثال بسيط
        return [
            'country' => 'Unknown',
            'city' => 'Unknown',
            'latitude' => 0,
            'longitude' => 0,
        ];
    }

    /**
     * فحص الموقع غير المعتاد
     */
    private function isUnusualLocation(string $email, array $location): bool
    {
        // فحص المواقع السابقة للمستخدم
        $recentLocations = Cache::get("user_locations:{$email}", []);
        
        if (empty($recentLocations)) {
            return false;
        }

        // إذا كان الموقع مختلف عن المواقع السابقة
        foreach ($recentLocations as $recentLocation) {
            if ($recentLocation['country'] === $location['country']) {
                return false;
            }
        }

        return true;
    }

    /**
     * فحص User Agent المشبوه
     */
    private function isSuspiciousUserAgent(string $userAgent): bool
    {
        $suspiciousPatterns = [
            'bot', 'crawler', 'spider', 'scraper',
            'curl', 'wget', 'python', 'java',
            'postman', 'insomnia',
        ];

        $userAgent = strtolower($userAgent);
        
        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * فحص التوقيت غير المعتاد
     */
    private function isUnusualTime(string $email): bool
    {
        $hour = now()->hour;
        
        // ساعات العمل العادية (8 صباحاً - 10 مساءً)
        if ($hour < 8 || $hour > 22) {
            return true;
        }

        return false;
    }

    /**
     * فحص سرعة المحاولات
     */
    private function isTooFastAttempts(string $ip): bool
    {
        $key = "last_attempt:{$ip}";
        $lastAttempt = Cache::get($key);
        
        if ($lastAttempt && now()->diffInSeconds($lastAttempt) < 2) {
            return true;
        }

        Cache::put($key, now(), now()->addMinutes(5));
        return false;
    }

    /**
     * حظر IP مؤقتاً
     */
    public function blockIP(string $ip, string $reason, int $durationMinutes = 60): void
    {
        $key = "blocked_ip:{$ip}";
        $blockData = [
            'reason' => $reason,
            'blocked_at' => now()->toISOString(),
            'expires_at' => now()->addMinutes($durationMinutes)->toISOString(),
        ];

        Cache::put($key, $blockData, now()->addMinutes($durationMinutes));

        Log::warning("IP blocked: {$ip}", $blockData);

        // تسجيل في قاعدة البيانات
        AdminAuditLog::createLog([
            'action' => 'ip_blocked',
            'description' => "حظر IP: {$ip} - السبب: {$reason}",
            'metadata' => $blockData,
            'ip_address' => $ip,
            'category' => 'security',
            'severity' => 'high',
            'status' => 'success',
        ]);
    }

    /**
     * فحص ما إذا كان IP محظور
     */
    public function isIPBlocked(string $ip): bool
    {
        return Cache::has("blocked_ip:{$ip}");
    }

    /**
     * إلغاء حظر IP
     */
    public function unblockIP(string $ip): void
    {
        Cache::forget("blocked_ip:{$ip}");
        
        Log::info("IP unblocked: {$ip}");

        AdminAuditLog::createLog([
            'action' => 'ip_unblocked',
            'description' => "إلغاء حظر IP: {$ip}",
            'ip_address' => $ip,
            'category' => 'security',
            'severity' => 'medium',
            'status' => 'success',
        ]);
    }

    /**
     * مراقبة الأنشطة المشبوهة
     */
    public function monitorSuspiciousActivity(): array
    {
        $report = [
            'failed_logins_last_hour' => $this->getFailedLoginsLastHour(),
            'blocked_ips' => $this->getBlockedIPs(),
            'unusual_activities' => $this->getUnusualActivities(),
            'security_alerts' => $this->getSecurityAlerts(),
        ];

        return $report;
    }

    /**
     * الحصول على محاولات تسجيل الدخول الفاشلة في الساعة الأخيرة
     */
    private function getFailedLoginsLastHour(): int
    {
        return AdminAuditLog::where('action', 'login_failed')
            ->where('created_at', '>=', now()->subHour())
            ->count();
    }

    /**
     * الحصول على قائمة IPs المحظورة
     */
    private function getBlockedIPs(): array
    {
        // البحث في Cache عن IPs المحظورة
        $blockedIPs = [];
        
        // هذا مثال بسيط - في التطبيق الحقيقي يمكن استخدام Redis scan
        for ($i = 1; $i <= 255; $i++) {
            for ($j = 1; $j <= 255; $j++) {
                $ip = "192.168.{$i}.{$j}";
                if ($this->isIPBlocked($ip)) {
                    $blockedIPs[] = $ip;
                }
            }
        }

        return $blockedIPs;
    }

    /**
     * الحصول على الأنشطة غير المعتادة
     */
    private function getUnusualActivities(): array
    {
        return AdminAuditLog::where('severity', 'high')
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * الحصول على تنبيهات الأمان
     */
    private function getSecurityAlerts(): array
    {
        $alerts = [];

        // فحص محاولات تسجيل الدخول الفاشلة
        $failedLogins = $this->getFailedLoginsLastHour();
        if ($failedLogins > 50) {
            $alerts[] = [
                'type' => 'high_failed_logins',
                'message' => "عدد كبير من محاولات تسجيل الدخول الفاشلة: {$failedLogins}",
                'severity' => 'high',
            ];
        }

        // فحص المستخدمين المقفلين
        $lockedUsers = SuperAdmin::where('locked_until', '>', now())->count() +
                      TenantUser::where('locked_until', '>', now())->count();
        
        if ($lockedUsers > 10) {
            $alerts[] = [
                'type' => 'many_locked_users',
                'message' => "عدد كبير من المستخدمين المقفلين: {$lockedUsers}",
                'severity' => 'medium',
            ];
        }

        // فحص كلمات المرور المنتهية الصلاحية
        $expiredPasswords = $this->getExpiredPasswordsCount();
        if ($expiredPasswords > 20) {
            $alerts[] = [
                'type' => 'expired_passwords',
                'message' => "عدد كبير من كلمات المرور المنتهية الصلاحية: {$expiredPasswords}",
                'severity' => 'medium',
            ];
        }

        return $alerts;
    }

    /**
     * الحصول على عدد كلمات المرور المنتهية الصلاحية
     */
    private function getExpiredPasswordsCount(): int
    {
        $count = 0;
        
        // فحص السوبر أدمن
        $superAdmins = SuperAdmin::whereNotNull('password_changed_at')->get();
        foreach ($superAdmins as $admin) {
            if ($admin->password_changed_at->addDays(90) < now()) {
                $count++;
            }
        }

        // فحص مستخدمي المستأجرين
        $tenantUsers = TenantUser::whereNotNull('password_changed_at')->get();
        foreach ($tenantUsers as $user) {
            if ($user->password_changed_at->addDays(90) < now()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * إرسال تنبيه أمني
     */
    public function sendSecurityAlert(string $type, string $message, array $data = []): void
    {
        // تسجيل التنبيه
        AdminAuditLog::createLog([
            'action' => 'security_alert',
            'description' => $message,
            'metadata' => array_merge($data, ['alert_type' => $type]),
            'category' => 'security',
            'severity' => 'critical',
            'status' => 'warning',
        ]);

        // إرسال إشعار للمديرين (يمكن تطويره لاحقاً)
        Log::critical("Security Alert: {$message}", $data);
    }

    /**
     * تنظيف البيانات القديمة
     */
    public function cleanupOldData(): void
    {
        // حذف سجلات التدقيق القديمة (أكثر من 6 أشهر)
        AdminAuditLog::where('created_at', '<', now()->subMonths(6))->delete();

        // تنظيف Cache للمحاولات القديمة
        // هذا يتطلب تنفيذ مخصص حسب نوع Cache المستخدم
    }
}
