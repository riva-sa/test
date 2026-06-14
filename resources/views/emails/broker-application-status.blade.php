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

            @if ($broker->isApproved())
                <p style="font-size:14px; color:#3f3f46; line-height:1.8;">
                    يسعدنا إبلاغك بأنه تم <strong style="color:#16a34a;">اعتماد حسابك</strong> في بوابة الوسطاء العقاريين لدى ريفا العقارية.
                </p>
                <p style="font-size:14px; color:#3f3f46; line-height:1.8;">
                    رقم العضوية الخاص بك: <strong>{{ $broker->reference_number }}</strong>
                </p>
                <p style="font-size:14px; color:#3f3f46; line-height:1.8;">
                    يمكنك الآن تسجيل الدخول وتصفح المشاريع والوحدات المتاحة والبدء بإرسال عملائك ومتابعة حالة طلباتهم.
                </p>
                <div style="text-align:center; margin:24px 0;">
                    <a href="{{ route('broker.login') }}" style="display:inline-block; background:#18181b; color:#ffffff; padding:12px 32px; border-radius:12px; text-decoration:none; font-size:14px; font-weight:bold;">
                        تسجيل الدخول
                    </a>
                </div>
            @else
                <p style="font-size:14px; color:#3f3f46; line-height:1.8;">
                    نشكرك على اهتمامك بالانضمام إلى بوابة الوسطاء لدى ريفا العقارية.
                </p>
                <p style="font-size:14px; color:#3f3f46; line-height:1.8;">
                    نأسف لإبلاغك بأنه <strong style="color:#dc2626;">لم يتم اعتماد طلب التسجيل</strong> الخاص بك في الوقت الحالي.
                </p>
                @if ($broker->rejection_reason)
                    <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:16px; margin:16px 0;">
                        <p style="font-size:13px; color:#991b1b; margin:0;">
                            <strong>سبب الرفض:</strong> {{ $broker->rejection_reason }}
                        </p>
                    </div>
                @endif
                <p style="font-size:14px; color:#3f3f46; line-height:1.8;">
                    يمكنك التواصل معنا في حال وجود أي استفسار.
                </p>
            @endif
        </div>
        <p style="text-align:center; font-size:11px; color:#a1a1aa; margin-top:16px;">
            © {{ date('Y') }} ريفا العقارية - جميع الحقوق محفوظة
        </p>
    </div>
</body>
</html>
