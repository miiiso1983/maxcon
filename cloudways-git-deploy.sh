#!/bin/bash

# Git Deployment Script for Cloudways
echo "🚀 Git Deployment لـ Cloudways"

# التحقق من وجود Git
if ! command -v git &> /dev/null; then
    echo "❌ Git غير مثبت"
    exit 1
fi

# 1. إعداد Git Repository
echo "📁 إعداد Git Repository..."

# تهيئة Git إذا لم يكن موجود
if [ ! -d ".git" ]; then
    echo "🔧 تهيئة Git Repository..."
    git init
    echo "✅ تم تهيئة Git"
fi

# إنشاء .gitignore محسن
echo "📝 إنشاء .gitignore محسن..."
cat > .gitignore << 'EOF'
# Laravel
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.env.production
.phpunit.result.cache
Homestead.json
Homestead.yaml
auth.json
npm-debug.log
yarn-error.log

# IDE
/.idea
/.vscode
*.swp
*.swo

# OS
.DS_Store
.DS_Store?
._*
.Spotlight-V100
.Trashes
ehthumbs.db
Thumbs.db

# Logs
*.log
storage/logs/*.log

# Cache
bootstrap/cache/*.php
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*

# Compiled
/public/mix-manifest.json
/public/js/app.js
/public/css/app.css

# Testing
.phpunit.result.cache
coverage/

# Deployment
deployment-info.txt
cloudways-deployment-info.txt
*.deployment

# Temporary files
*.tmp
*.temp
*~

# Backup files
*.bak
*.backup

# Development tools
.php_cs.cache
.phpstorm.meta.php
_ide_helper.php
_ide_helper_models.php

# Local development
/storage/debugbar
EOF

echo "✅ تم إنشاء .gitignore"

# إضافة جميع الملفات
echo "📦 إضافة الملفات إلى Git..."
git add .

# التحقق من التغييرات
if git diff --cached --quiet; then
    echo "⚠️ لا توجد تغييرات للحفظ"
else
    # إنشاء commit
    echo "💾 إنشاء commit..."
    COMMIT_MESSAGE="Cloudways deployment - $(date '+%Y-%m-%d %H:%M:%S')"
    git commit -m "$COMMIT_MESSAGE"
    echo "✅ تم إنشاء commit: $COMMIT_MESSAGE"
fi

# 2. إعداد Remote Repository
echo "🌐 إعداد Remote Repository..."

# طلب رابط Repository من المستخدم
read -p "أدخل رابط Git Repository (أو اتركه فارغاً للتخطي): " REPO_URL

if [ ! -z "$REPO_URL" ]; then
    # إزالة origin إذا كان موجود
    git remote remove origin 2>/dev/null || true
    
    # إضافة origin جديد
    git remote add origin "$REPO_URL"
    echo "✅ تم إضافة remote origin: $REPO_URL"
    
    # دفع إلى Repository
    echo "📤 دفع إلى Repository..."
    
    # التحقق من الفرع الحالي
    CURRENT_BRANCH=$(git branch --show-current)
    if [ -z "$CURRENT_BRANCH" ]; then
        CURRENT_BRANCH="main"
        git checkout -b main
    fi
    
    # دفع الكود
    if git push -u origin "$CURRENT_BRANCH"; then
        echo "✅ تم دفع الكود إلى $CURRENT_BRANCH"
    else
        echo "❌ فشل في دفع الكود"
        echo "💡 تأكد من صحة رابط Repository وصلاحيات الوصول"
    fi
else
    echo "⏭️ تم تخطي إعداد Remote Repository"
fi

# 3. إنشاء ملف deployment hook
echo "🔗 إنشاء deployment hook..."
cat > deploy-hook.php << 'EOF'
<?php
/**
 * Cloudways Git Deployment Hook
 * ضع هذا الملف في مجلد public وأضف رابطه في Cloudways Git Deployment
 */

// التحقق من الأمان
$secret = 'your-secret-key-here'; // غير هذا المفتاح
$received_secret = $_GET['secret'] ?? '';

if ($received_secret !== $secret) {
    http_response_code(403);
    die('Unauthorized');
}

echo "<h1>🚀 Cloudways Git Deployment</h1>";
echo "<p>بدء عملية النشر...</p>";

// تسجيل وقت النشر
$deployment_time = date('Y-m-d H:i:s');
echo "<p><strong>وقت النشر:</strong> $deployment_time</p>";

// تشغيل أوامر النشر
$commands = [
    'git pull origin main',
    'composer install --no-dev --optimize-autoloader',
    'php artisan migrate --force',
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache',
    'php artisan queue:restart'
];

echo "<h2>📋 تنفيذ الأوامر:</h2>";
echo "<ul>";

foreach ($commands as $command) {
    echo "<li>تنفيذ: <code>$command</code><br>";
    
    $output = shell_exec($command . ' 2>&1');
    
    if ($output) {
        echo "<pre style='background:#f0f0f0; padding:10px; margin:10px 0; font-size:12px;'>";
        echo htmlspecialchars($output);
        echo "</pre>";
    } else {
        echo "<span style='color:green;'>✅ تم بنجاح</span>";
    }
    
    echo "</li>";
}

echo "</ul>";

// تسجيل النشر
$log_entry = "[$deployment_time] Git deployment completed\n";
file_put_contents('deployment.log', $log_entry, FILE_APPEND | LOCK_EX);

echo "<div style='background:#d4edda; padding:15px; border-radius:5px; margin:20px 0;'>";
echo "<h2>✅ تم الانتهاء من النشر!</h2>";
echo "<p>تم تحديث الموقع بنجاح.</p>";
echo "<p><a href='/'>زيارة الموقع</a></p>";
echo "</div>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; direction: rtl; }
h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
h2 { color: #555; margin-top: 20px; }
code { background: #f8f9fa; padding: 2px 5px; border-radius: 3px; }
pre { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; }
ul { text-align: right; }
</style>
EOF

echo "✅ تم إنشاء deploy-hook.php"

# 4. إنشاء ملف إرشادات Cloudways
echo "📖 إنشاء ملف إرشادات Cloudways..."
cat > CLOUDWAYS-GIT-SETUP.md << 'EOF'
# 🚀 إعداد Git Deployment في Cloudways

## الخطوات في لوحة تحكم Cloudways:

### 1. الذهاب إلى Git Deployment
- اختر التطبيق الخاص بك
- اذهب إلى تبويب "Git Deployment"

### 2. إعداد Repository
- **Git Repository URL:** رابط المستودع الخاص بك
- **Branch:** main (أو الفرع المطلوب)
- **Deployment Path:** `/public_html`

### 3. إعداد Deploy Key (إذا كان Repository خاص)
- انسخ المفتاح العام من Cloudways
- أضفه في إعدادات Repository (Deploy Keys)

### 4. إعداد Deployment Script
```bash
#!/bin/bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
chmod -R 755 storage bootstrap/cache
```

### 5. تفعيل Auto Deploy
- فعل "Auto Deploy" للنشر التلقائي عند Push

## استخدام Deployment Hook:

### 1. رفع ملف deploy-hook.php
- ارفع الملف إلى مجلد `public`
- غير المفتاح السري في الملف

### 2. إعداد Webhook
- في GitHub/GitLab: اذهب إلى Settings > Webhooks
- أضف URL: `https://yourdomain.com/deploy-hook.php?secret=your-secret-key`
- اختر "Push events"

## أوامر Git المفيدة:

```bash
# إضافة تغييرات جديدة
git add .
git commit -m "Update: description"
git push origin main

# إنشاء فرع جديد للتطوير
git checkout -b feature/new-feature
git push -u origin feature/new-feature

# دمج التغييرات
git checkout main
git merge feature/new-feature
git push origin main
```

## نصائح مهمة:

1. **اختبر محلياً أولاً** قبل Push
2. **استخدم فروع منفصلة** للميزات الجديدة
3. **اكتب رسائل commit واضحة**
4. **راجع التغييرات** قبل الدمج
5. **احتفظ بنسخ احتياطية** قبل النشر الكبير

## استكشاف الأخطاء:

### مشكلة في الصلاحيات:
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### مشكلة في Composer:
```bash
composer clear-cache
composer install --no-dev --optimize-autoloader
```

### مشكلة في Laravel:
```bash
php artisan config:clear
php artisan cache:clear
php artisan optimize
```
EOF

echo "✅ تم إنشاء CLOUDWAYS-GIT-SETUP.md"

# 5. عرض ملخص الإعداد
echo ""
echo "🎉 تم الانتهاء من إعداد Git Deployment!"
echo ""
echo "📋 الملفات المنشأة:"
echo "✅ .gitignore - قائمة الملفات المستبعدة"
echo "✅ deploy-hook.php - webhook للنشر التلقائي"
echo "✅ CLOUDWAYS-GIT-SETUP.md - دليل الإعداد"
echo ""
echo "🚀 الخطوات التالية:"
echo "1. ارفع الكود إلى Git Repository"
echo "2. اربط Repository في Cloudways"
echo "3. إعداد Deployment Script"
echo "4. اختبر النشر"
echo ""

if [ ! -z "$REPO_URL" ]; then
    echo "🔗 Repository URL: $REPO_URL"
    echo "📤 تم دفع الكود إلى Repository"
    echo ""
fi

echo "📖 راجع ملف CLOUDWAYS-GIT-SETUP.md للتفاصيل الكاملة"
echo ""
