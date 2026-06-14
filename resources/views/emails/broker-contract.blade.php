<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0; padding:0; background:#f4f4f5; font-family: Tahoma, Arial, sans-serif;">
    <div style="max-width:600px; margin:0 auto; padding:32px 16px;">
        <div style="background:#ffffff; border-radius:16px; padding:32px; border:1px solid #e4e4e7;">
            <h1 style="font-size:20px; color:#18181b; margin:0 0 8px;">
                مرحباً {{ $broker->name }}،
            </h1>
            <p style="font-size:14px; color:#3f3f46; line-height:1.8;">
                قامت الإدارة بإرسال <strong>عقد الوساطة</strong> الخاص بك بصيغة PDF.
            </p>
            <p style="font-size:14px; color:#3f3f46; line-height:1.8;">
                يرجى تسجيل الدخول إلى بوابة الوسطاء، وتحميل العقد ومراجعته، ثم الموافقة عليه ورفع النسخة الموقعة لتفعيل بوابتك بشكل كامل.
            </p>
            <div style="text-align:center; margin:24px 0;">
                <a href="{{ route('broker.login') }}" style="display:inline-block; background:#18181b; color:#ffffff; padding:12px 32px; border-radius:12px; text-decoration:none; font-size:14px; font-weight:bold;">
                    تسجيل الدخول واعتماد العقد
                </a>
            </div>
            <p style="font-size:12px; color:#71717a; line-height:1.8;">
                رقم العضوية: <strong>{{ $broker->reference_number }}</strong>
            </p>
        </div>
        <p style="text-align:center; font-size:11px; color:#a1a1aa; margin-top:16px;">
            © {{ date('Y') }} ريفا العقارية - جميع الحقوق محفوظة
        </p>
    </div>
</body>
</html>
