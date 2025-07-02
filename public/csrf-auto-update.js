
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
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute("content", data.token);
                }
                
                // تحديث جميع hidden inputs
                document.querySelectorAll('input[name="_token"]').forEach(input => {
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
</script>