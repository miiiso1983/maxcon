<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordPolicyService
{
    /**
     * سياسات كلمة المرور الافتراضية
     */
    private array $defaultPolicies = [
        'min_length' => 8,
        'max_length' => 128,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => true,
        'min_symbols' => 1,
        'min_numbers' => 1,
        'prevent_common_passwords' => true,
        'prevent_personal_info' => true,
        'prevent_reuse_count' => 5,
        'expiry_days' => 90,
        'warning_days' => 14,
        'max_attempts' => 5,
        'lockout_duration_minutes' => 30,
    ];

    /**
     * التحقق من صحة كلمة المرور
     */
    public function validatePassword(string $password, array $userInfo = [], array $customPolicies = []): array
    {
        $policies = array_merge($this->defaultPolicies, $customPolicies);
        $errors = [];

        // فحص الطول الأدنى
        if (strlen($password) < $policies['min_length']) {
            $errors[] = "كلمة المرور يجب أن تكون على الأقل {$policies['min_length']} أحرف";
        }

        // فحص الطول الأقصى
        if (strlen($password) > $policies['max_length']) {
            $errors[] = "كلمة المرور يجب ألا تزيد عن {$policies['max_length']} حرف";
        }

        // فحص الأحرف الكبيرة
        if ($policies['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'كلمة المرور يجب أن تحتوي على حرف كبير واحد على الأقل';
        }

        // فحص الأحرف الصغيرة
        if ($policies['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'كلمة المرور يجب أن تحتوي على حرف صغير واحد على الأقل';
        }

        // فحص الأرقام
        if ($policies['require_numbers']) {
            $numberCount = preg_match_all('/[0-9]/', $password);
            if ($numberCount < $policies['min_numbers']) {
                $errors[] = "كلمة المرور يجب أن تحتوي على {$policies['min_numbers']} رقم على الأقل";
            }
        }

        // فحص الرموز
        if ($policies['require_symbols']) {
            $symbolCount = preg_match_all('/[^a-zA-Z0-9]/', $password);
            if ($symbolCount < $policies['min_symbols']) {
                $errors[] = "كلمة المرور يجب أن تحتوي على {$policies['min_symbols']} رمز خاص على الأقل";
            }
        }

        // فحص كلمات المرور الشائعة
        if ($policies['prevent_common_passwords'] && $this->isCommonPassword($password)) {
            $errors[] = 'كلمة المرور هذه شائعة جداً، يرجى اختيار كلمة مرور أكثر أماناً';
        }

        // فحص المعلومات الشخصية
        if ($policies['prevent_personal_info'] && !empty($userInfo)) {
            if ($this->containsPersonalInfo($password, $userInfo)) {
                $errors[] = 'كلمة المرور يجب ألا تحتوي على معلوماتك الشخصية';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'strength' => $this->calculatePasswordStrength($password),
        ];
    }

    /**
     * حساب قوة كلمة المرور
     */
    public function calculatePasswordStrength(string $password): array
    {
        $score = 0;
        $feedback = [];

        // الطول
        $length = strlen($password);
        if ($length >= 8) $score += 1;
        if ($length >= 12) $score += 1;
        if ($length >= 16) $score += 1;

        // التنوع في الأحرف
        if (preg_match('/[a-z]/', $password)) {
            $score += 1;
            $feedback[] = 'يحتوي على أحرف صغيرة';
        }
        if (preg_match('/[A-Z]/', $password)) {
            $score += 1;
            $feedback[] = 'يحتوي على أحرف كبيرة';
        }
        if (preg_match('/[0-9]/', $password)) {
            $score += 1;
            $feedback[] = 'يحتوي على أرقام';
        }
        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $score += 1;
            $feedback[] = 'يحتوي على رموز خاصة';
        }

        // تحديد مستوى القوة
        if ($score <= 2) {
            $level = 'ضعيف';
            $color = 'danger';
        } elseif ($score <= 4) {
            $level = 'متوسط';
            $color = 'warning';
        } elseif ($score <= 6) {
            $level = 'قوي';
            $color = 'info';
        } else {
            $level = 'قوي جداً';
            $color = 'success';
        }

        return [
            'score' => $score,
            'level' => $level,
            'color' => $color,
            'percentage' => min(100, ($score / 7) * 100),
            'feedback' => $feedback,
        ];
    }

    /**
     * فحص كلمات المرور الشائعة
     */
    private function isCommonPassword(string $password): bool
    {
        $commonPasswords = [
            '123456', 'password', '123456789', '12345678', '12345',
            '1234567', '1234567890', 'qwerty', 'abc123', '111111',
            '123123', 'admin', 'letmein', 'welcome', 'monkey',
            'password123', '123qwe', 'qwerty123', 'admin123',
        ];

        return in_array(strtolower($password), $commonPasswords);
    }

    /**
     * فحص احتواء كلمة المرور على معلومات شخصية
     */
    private function containsPersonalInfo(string $password, array $userInfo): bool
    {
        $password = strtolower($password);
        
        $personalData = [
            $userInfo['name'] ?? '',
            $userInfo['email'] ?? '',
            $userInfo['phone'] ?? '',
            $userInfo['username'] ?? '',
        ];

        foreach ($personalData as $data) {
            if (empty($data)) continue;
            
            $data = strtolower($data);
            
            // فحص الاحتواء المباشر
            if (strpos($password, $data) !== false) {
                return true;
            }
            
            // فحص أجزاء من البيانات (أكثر من 3 أحرف)
            if (strlen($data) > 3) {
                for ($i = 0; $i <= strlen($data) - 4; $i++) {
                    $substring = substr($data, $i, 4);
                    if (strpos($password, $substring) !== false) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * توليد كلمة مرور قوية
     */
    public function generateStrongPassword(int $length = 12, array $options = []): string
    {
        $options = array_merge([
            'include_uppercase' => true,
            'include_lowercase' => true,
            'include_numbers' => true,
            'include_symbols' => true,
            'exclude_ambiguous' => true,
        ], $options);

        $characters = '';
        
        if ($options['include_lowercase']) {
            $characters .= $options['exclude_ambiguous'] ? 'abcdefghjkmnpqrstuvwxyz' : 'abcdefghijklmnopqrstuvwxyz';
        }
        
        if ($options['include_uppercase']) {
            $characters .= $options['exclude_ambiguous'] ? 'ABCDEFGHJKMNPQRSTUVWXYZ' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        
        if ($options['include_numbers']) {
            $characters .= $options['exclude_ambiguous'] ? '23456789' : '0123456789';
        }
        
        if ($options['include_symbols']) {
            $characters .= $options['exclude_ambiguous'] ? '!@#$%^&*()_+-=[]{}|;:,.<>?' : '!@#$%^&*()_+-=[]{}|;:,.<>?';
        }

        $password = '';
        $charactersLength = strlen($characters);
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $password;
    }

    /**
     * فحص انتهاء صلاحية كلمة المرور
     */
    public function isPasswordExpired($user, array $policies = []): bool
    {
        $policies = array_merge($this->defaultPolicies, $policies);
        
        if (!isset($user->password_changed_at)) {
            return false;
        }

        $expiryDate = $user->password_changed_at->addDays($policies['expiry_days']);
        return now()->isAfter($expiryDate);
    }

    /**
     * فحص اقتراب انتهاء صلاحية كلمة المرور
     */
    public function isPasswordExpiringSoon($user, array $policies = []): bool
    {
        $policies = array_merge($this->defaultPolicies, $policies);
        
        if (!isset($user->password_changed_at)) {
            return false;
        }

        $warningDate = $user->password_changed_at
            ->addDays($policies['expiry_days'] - $policies['warning_days']);
            
        return now()->isAfter($warningDate) && !$this->isPasswordExpired($user, $policies);
    }

    /**
     * الحصول على عدد الأيام المتبقية لانتهاء كلمة المرور
     */
    public function getDaysUntilExpiry($user, array $policies = []): int
    {
        $policies = array_merge($this->defaultPolicies, $policies);
        
        if (!isset($user->password_changed_at)) {
            return $policies['expiry_days'];
        }

        $expiryDate = $user->password_changed_at->addDays($policies['expiry_days']);
        return max(0, now()->diffInDays($expiryDate, false));
    }

    /**
     * فحص إعادة استخدام كلمة المرور
     */
    public function isPasswordReused(string $newPassword, $user, array $policies = []): bool
    {
        $policies = array_merge($this->defaultPolicies, $policies);
        
        if ($policies['prevent_reuse_count'] <= 0) {
            return false;
        }

        // فحص كلمة المرور الحالية
        if (Hash::check($newPassword, $user->password)) {
            return true;
        }

        // فحص كلمات المرور السابقة (إذا كانت محفوظة)
        if (method_exists($user, 'passwordHistories')) {
            $recentPasswords = $user->passwordHistories()
                ->latest()
                ->limit($policies['prevent_reuse_count'] - 1)
                ->get();

            foreach ($recentPasswords as $passwordHistory) {
                if (Hash::check($newPassword, $passwordHistory->password)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * حفظ كلمة المرور في التاريخ
     */
    public function savePasswordHistory($user, string $hashedPassword): void
    {
        if (method_exists($user, 'passwordHistories')) {
            $user->passwordHistories()->create([
                'password' => $hashedPassword,
                'created_at' => now(),
            ]);

            // الاحتفاظ بعدد محدود من كلمات المرور السابقة
            $keepCount = $this->defaultPolicies['prevent_reuse_count'];
            $user->passwordHistories()
                ->oldest()
                ->skip($keepCount)
                ->delete();
        }
    }

    /**
     * الحصول على قواعد التحقق لـ Laravel
     */
    public function getLaravelValidationRules(array $customPolicies = []): array
    {
        $policies = array_merge($this->defaultPolicies, $customPolicies);
        
        $rules = [
            'required',
            'string',
            "min:{$policies['min_length']}",
            "max:{$policies['max_length']}",
        ];

        // إضافة قواعد Laravel Password
        $passwordRule = Password::min($policies['min_length']);
        
        if ($policies['require_uppercase']) {
            $passwordRule->mixedCase();
        }
        
        if ($policies['require_numbers']) {
            $passwordRule->numbers();
        }
        
        if ($policies['require_symbols']) {
            $passwordRule->symbols();
        }

        $rules[] = $passwordRule;

        return $rules;
    }
}
