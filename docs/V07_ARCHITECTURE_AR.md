# معمارية V0.7

**Data Provider → Sync/Revision → Laravel DB → Match Intelligence → Realtime Snapshot/SSE → Web/Flutter**.

طبقة realtime تستخدم revision + heartbeat + incremental commentary cursor. SSE هو fallback حقيقي يعمل فوق HTTP؛ ويمكن إضافة Laravel Reverb/Pusher-compatible WebSockets في بيئة الإنتاج مع الإبقاء على نفس Data Contract.

Firebase Push يعمل عبر HTTP v1 باستخدام Service Account وOAuth JWT قصير العمر مع cache للتوكن. لا تحفظ المفاتيح داخل Git.

News personalization يعتمد signals صريحة (view/open/like/share/save) ويعيد ترتيب الفئات المفضلة دون تخزين نصوص حساسة.
