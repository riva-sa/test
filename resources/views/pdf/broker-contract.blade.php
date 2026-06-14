<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="UTF-8"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>عقد وساطة عقارية</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', 'Arial', sans-serif;
        font-size: 13px;
        color: #1a1a2e;
        background: #fff;
        direction: rtl;
        line-height: 1.7;
    }

    .page {
        padding: 40px 50px;
        min-height: 100vh;
    }

    /* ─── Header ──────────────────────────────────────────────── */
    .header {
        text-align: center;
        border-bottom: 3px solid #1a1a2e;
        padding-bottom: 20px;
        margin-bottom: 28px;
    }
    .header .company-name {
        font-size: 22px;
        font-weight: bold;
        color: #1a1a2e;
        letter-spacing: 1px;
    }
    .header .contract-title {
        font-size: 17px;
        font-weight: bold;
        color: #c9a84c;
        margin-top: 6px;
    }
    .header .contract-subtitle {
        font-size: 11px;
        color: #666;
        margin-top: 4px;
    }

    /* ─── Reference box ───────────────────────────────────────── */
    .ref-box {
        display: table;
        width: 100%;
        margin-bottom: 24px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        background: #f9f9fb;
    }
    .ref-box .ref-item {
        display: table-cell;
        padding: 10px 16px;
        text-align: center;
        border-left: 1px solid #e0e0e0;
        vertical-align: middle;
    }
    .ref-box .ref-item:last-child { border-left: none; }
    .ref-box .ref-label {
        font-size: 9px;
        color: #888;
        font-weight: bold;
        text-transform: uppercase;
        display: block;
        margin-bottom: 2px;
    }
    .ref-box .ref-value {
        font-size: 13px;
        font-weight: bold;
        color: #1a1a2e;
    }

    /* ─── Parties ─────────────────────────────────────────────── */
    .section-title {
        font-size: 13px;
        font-weight: bold;
        color: #1a1a2e;
        border-right: 4px solid #c9a84c;
        padding-right: 10px;
        margin-bottom: 14px;
        margin-top: 22px;
    }

    .parties-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
    }
    .parties-table td {
        padding: 7px 12px;
        font-size: 12.5px;
        vertical-align: top;
    }
    .parties-table tr:nth-child(even) { background: #f7f8fb; }
    .parties-table .label {
        font-weight: bold;
        color: #444;
        width: 38%;
        white-space: nowrap;
    }
    .parties-table .value {
        color: #1a1a2e;
        font-weight: bold;
    }
    .parties-table .dot { color: #c9a84c; font-weight: bold; }

    /* ─── Clause / article list ───────────────────────────────── */
    .clauses { margin-top: 4px; }
    .clause {
        margin-bottom: 12px;
        padding-right: 0;
    }
    .clause-num {
        font-weight: bold;
        color: #c9a84c;
        font-size: 13px;
    }
    .clause-text {
        font-size: 12.5px;
        color: #333;
        margin-top: 3px;
        text-align: justify;
    }

    /* ─── Divider ─────────────────────────────────────────────── */
    .divider {
        border: none;
        border-top: 1px solid #e0e0e0;
        margin: 20px 0;
    }

    /* ─── Signature section ───────────────────────────────────── */
    .signature-section {
        margin-top: 36px;
        display: table;
        width: 100%;
    }
    .sig-block {
        display: table-cell;
        width: 50%;
        text-align: center;
        vertical-align: bottom;
        padding: 0 20px;
    }
    .sig-label {
        font-size: 11px;
        font-weight: bold;
        color: #555;
        margin-bottom: 8px;
    }
    .sig-party {
        font-size: 13px;
        font-weight: bold;
        color: #1a1a2e;
        margin-bottom: 4px;
    }
    .sig-image-wrap {
        height: 72px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 4px;
    }
    .sig-image-wrap img {
        max-height: 68px;
        max-width: 220px;
    }
    .sig-line {
        border-top: 1.5px solid #1a1a2e;
        margin: 0 10px;
    }
    .sig-date {
        font-size: 10px;
        color: #777;
        margin-top: 6px;
    }

    /* ─── Footer ──────────────────────────────────────────────── */
    .footer {
        margin-top: 36px;
        border-top: 1px solid #e0e0e0;
        padding-top: 10px;
        text-align: center;
        font-size: 10px;
        color: #aaa;
    }

    .highlight { color: #c9a84c; font-weight: bold; }

    /* page break helpers */
    .page-break { page-break-after: always; }
</style>
</head>
<body>
<div class="page">

    {{-- ══════════════ Header ══════════════ --}}
    <div class="header">
        <div class="company-name">ريفا العقارية</div>
        <div class="contract-title">عقد وساطة عقارية</div>
        <div class="contract-subtitle">Real Estate Brokerage Agreement</div>
    </div>

    {{-- ══════════════ Reference strip ══════════════ --}}
    <div class="ref-box">
        <div class="ref-item">
            <span class="ref-label">رقم العضوية</span>
            <span class="ref-value highlight">{{ $broker->reference_number }}</span>
        </div>
        <div class="ref-item">
            <span class="ref-label">تاريخ الاعتماد</span>
            <span class="ref-value">{{ $broker->approved_at?->format('Y-m-d') ?? now()->format('Y-m-d') }}</span>
        </div>
        <div class="ref-item">
            <span class="ref-label">نوع الوسيط</span>
            <span class="ref-value">{{ $broker->brokerTypeLabel() }}</span>
        </div>
        <div class="ref-item">
            <span class="ref-label">تاريخ إصدار العقد</span>
            <span class="ref-value">{{ now()->format('Y-m-d') }}</span>
        </div>
    </div>

    {{-- ══════════════ Parties ══════════════ --}}
    <div class="section-title">أطراف العقد</div>

    <table class="parties-table">
        <tr>
            <td class="label">الطرف الأول (الشركة)</td>
            <td class="dot">:</td>
            <td class="value">ريفا العقارية</td>
        </tr>
        <tr>
            <td class="label">الطرف الثاني (الوسيط)</td>
            <td class="dot">:</td>
            <td class="value">{{ $broker->name }}</td>
        </tr>
        <tr>
            <td class="label">رقم الهوية / الإقامة</td>
            <td class="dot">:</td>
            <td class="value">{{ $broker->national_id ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">المدينة</td>
            <td class="dot">:</td>
            <td class="value">{{ $broker->city ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">رقم الواتساب</td>
            <td class="dot">:</td>
            <td class="value" dir="ltr">{{ $broker->whatsapp ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">البريد الإلكتروني</td>
            <td class="dot">:</td>
            <td class="value" dir="ltr">{{ $broker->email }}</td>
        </tr>
        <tr>
            <td class="label">رقم الآيبان (IBAN)</td>
            <td class="dot">:</td>
            <td class="value" dir="ltr">{{ $broker->iban ?? '—' }}</td>
        </tr>
    </table>

    <hr class="divider">

    {{-- ══════════════ Contract Clauses ══════════════ --}}
    <div class="section-title">بنود العقد</div>

    <div class="clauses">

        <div class="clause">
            <div class="clause-num">المادة الأولى — الغرض من العقد</div>
            <div class="clause-text">
                يُبرم هذا العقد بين ريفا العقارية (الشركة) والوسيط المُشار إليه بياناته أعلاه،
                بهدف تنظيم علاقة الوساطة العقارية وتحديد حقوق كل طرف وواجباته وفق أحكام
                الأنظمة والتشريعات العقارية المعمول بها في المملكة العربية السعودية.
            </div>
        </div>

        <div class="clause">
            <div class="clause-num">المادة الثانية — التزامات الوسيط</div>
            <div class="clause-text">
                يلتزم الوسيط بما يلي:<br>
                أ) التسويق لمشاريع الشركة بأسلوب احترافي يليق بسمعة الشركة.<br>
                ب) عدم التصرف باسم الشركة أو إبرام أي عقود دون تفويض خطي مسبق.<br>
                ج) الإفصاح الكامل عن بياناته وتحديثها فور أي تغيير.<br>
                د) الامتناع عن أي ممارسات تسويقية مضللة أو مخالفة للأنظمة.
            </div>
        </div>

        <div class="clause">
            <div class="clause-num">المادة الثالثة — العمولات والمستحقات</div>
            <div class="clause-text">
                تُحدَّد نسب العمولة بموجب جدول العمولات الصادر عن الشركة والمعتمد لكل مشروع،
                وتُصرف بعد اكتمال إجراءات البيع والتحقق من سلامة الصفقة وفق السياسات الداخلية للشركة.
                تُحتسب العمولات على إجمالي قيمة الصفقة المبرمة، ولا تُعتمد أي عمولة إلا بعد توقيع
                عقد البيع النهائي وإتمام إجراءات التسجيل الرسمي.
            </div>
        </div>

        <div class="clause">
            <div class="clause-num">المادة الرابعة — مدة العقد</div>
            <div class="clause-text">
                يسري هذا العقد لمدة سنة كاملة من تاريخ توقيعه، ويُجدَّد تلقائياً لفترات مماثلة
                ما لم يُبدِ أحد الطرفين رغبته في إنهائه كتابةً قبل (30) يوماً من تاريخ انتهائه.
            </div>
        </div>

        <div class="clause">
            <div class="clause-num">المادة الخامسة — السرية وحماية البيانات</div>
            <div class="clause-text">
                يتعهد الوسيط بالحفاظ على سرية جميع المعلومات التجارية والعقارية التي يطلع عليها
                خلال فترة تعاونه مع الشركة، وعدم الإفصاح عنها لأي طرف ثالث دون إذن خطي مسبق،
                وذلك خلال فترة العقد وبعد انتهائه لمدة (3) سنوات.
            </div>
        </div>

        <div class="clause">
            <div class="clause-num">المادة السادسة — إنهاء العقد</div>
            <div class="clause-text">
                يحق للشركة إنهاء هذا العقد فوراً دون إشعار مسبق في الحالات التالية:<br>
                أ) ثبوت مخالفة الوسيط لأي بند من بنود هذا العقد.<br>
                ب) ارتكاب الوسيط أي تصرف يُلحق ضرراً بسمعة الشركة أو عملائها.<br>
                ج) إفصاح الوسيط عن معلومات سرية دون إذن.
            </div>
        </div>

        <div class="clause">
            <div class="clause-num">المادة السابعة — فض النزاعات</div>
            <div class="clause-text">
                في حال نشوء أي نزاع بين الطرفين بشأن تفسير أو تطبيق هذا العقد،
                يسعى الطرفان إلى تسويته وداً. فإن تعذّر ذلك، يُحال النزاع إلى الجهات
                القضائية المختصة في المملكة العربية السعودية وفق الأنظمة المرعية.
            </div>
        </div>

        <div class="clause">
            <div class="clause-num">المادة الثامنة — الأحكام العامة</div>
            <div class="clause-text">
                يُعدّ هذا العقد الاتفاقية الكاملة بين الطرفين ويحلّ محل أي اتفاقيات سابقة.
                لا يجوز لأي طرف التنازل عن حقوقه أو التزاماته دون موافقة خطية من الطرف الآخر.
                تُطبَّق على هذا العقد أحكام نظام الوساطة العقارية السعودي وسائر التشريعات ذات الصلة.
            </div>
        </div>

    </div>

    <hr class="divider">

    {{-- ══════════════ Signature Section ══════════════ --}}
    <div class="section-title">التوقيعات</div>

    <div class="signature-section">
        {{-- Company side --}}
        <div class="sig-block">
            <div class="sig-label">الطرف الأول</div>
            <div class="sig-party">ريفا العقارية</div>
            <div class="sig-image-wrap">
                {{-- Company stamp placeholder --}}
                <span style="font-size:11px; color:#aaa; font-style:italic;">ختم الشركة</span>
            </div>
            <div class="sig-line"></div>
            <div class="sig-date">التوقيع والختم</div>
        </div>

        {{-- Broker side --}}
        <div class="sig-block">
            <div class="sig-label">الطرف الثاني (الوسيط)</div>
            <div class="sig-party">{{ $broker->name }}</div>

            @if (! empty($signatureImage))
                <div class="sig-image-wrap">
                    <img src="{{ $signatureImage }}" alt="توقيع الوسيط">
                </div>
            @else
                <div class="sig-image-wrap">
                    <span style="font-size:11px; color:#aaa; font-style:italic;">مكان التوقيع</span>
                </div>
            @endif

            <div class="sig-line"></div>
            <div class="sig-date">
                {{ $broker->reference_number }}
                @if (! empty($signedAt))
                    &nbsp;·&nbsp; {{ $signedAt }}
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════ Footer ══════════════ --}}
    <div class="footer">
        ريفا العقارية &mdash; {{ now()->format('Y') }} &mdash;
        هذا العقد أُنشئ إلكترونياً ويحمل القوة القانونية ذاتها للعقود الورقية الموقعة
    </div>

</div>
</body>
</html>
