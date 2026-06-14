<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0; padding:0; background:#f4f4f5; font-family: Tahoma, Arial, sans-serif;">
    <div style="max-width:600px; margin:0 auto; padding:32px 16px;">
        <div style="background:#ffffff; border-radius:16px; padding:32px; border:1px solid #e4e4e7;">
            <h1 style="font-size:20px; color:#18181b; margin:0 0 8px;">
                مرحباً {{ $application->name }}،
            </h1>

            @php
                $jobTitle = $application->jobPosting?->title ?? $application->department;
                $jobTitleEn = $application->jobPosting?->title_en ?? $application->jobPosting?->title ?? $application->department;
            @endphp

            <p style="font-size:14px; color:#3f3f46; line-height:1.8;">
                @if ($application->jobPosting)
                    شكراً لتقديمك على وظيفة <strong>{{ $jobTitle }}</strong> لدى ريفا العقارية.
                @else
                    شكراً لاهتمامك بالانضمام إلى فريق <strong>ريفا العقارية</strong> في تخصص <strong>{{ $jobTitle }}</strong>.
                @endif
            </p>
            <p style="font-size:14px; color:#3f3f46; line-height:1.8;">
                تم استلام طلبك بنجاح، وسيقوم فريق التوظيف لدينا بمراجعته والتواصل معك في حال كان ملفك مناسباً.
            </p>

            <div style="background:#f4f4f5; border-radius:12px; padding:16px; margin:16px 0;">
                <p style="font-size:13px; color:#3f3f46; margin:0 0 4px;"><strong>{{ $application->jobPosting ? 'الوظيفة' : 'التخصص' }}:</strong> {{ $jobTitle }}</p>
                <p style="font-size:13px; color:#3f3f46; margin:0;"><strong>تاريخ التقديم:</strong> {{ $application->created_at->format('Y/m/d') }}</p>
            </div>

            <hr style="border:none; border-top:1px solid #e4e4e7; margin:24px 0;">

            <p style="font-size:14px; color:#3f3f46; line-height:1.8; direction:ltr; text-align:left;">
                @if ($application->jobPosting)
                    Thank you for applying for the <strong>{{ $jobTitleEn }}</strong> position at Riva Real Estate.
                @else
                    Thank you for your interest in joining <strong>Riva Real Estate</strong> in the <strong>{{ $jobTitleEn }}</strong> field.
                @endif
                <br>
                Your application has been received successfully. Our recruitment team will review it and contact you if your profile matches.
            </p>

            <p style="font-size:12px; color:#a1a1aa; margin-top:24px;">
                هذه رسالة آلية، الرجاء عدم الرد عليها مباشرة.
            </p>
        </div>
    </div>
</body>
</html>
