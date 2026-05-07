<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب جديد</title>
</head>
<body style="margin: 0; padding: 0; background-color: #fafafa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; direction: rtl;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #fafafa; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="520" cellpadding="0" cellspacing="0" style="max-width: 520px; width: 100%;">

                    {{-- Logo --}}
                    <tr>
                        <td style="padding: 0 0 20px; text-align: center;">
                            <img src="{{ asset('frontend/img/logoyy.png') }}" alt="Riva" style="height: 40px; border-radius: 6px;" />
                        </td>
                    </tr>

                    {{-- Main Card --}}
                    <tr>
                        <td style="background-color: #ffffff; border: 1px solid #e4e4e7; border-radius: 12px; overflow: hidden;">

                            {{-- Header --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 24px 24px 16px; border-bottom: 1px solid #f4f4f5;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td>
                                                    <p style="margin: 0 0 4px; font-size: 13px; color: #a1a1aa; font-weight: 500; letter-spacing: 0.5px;">طلب جديد</p>
                                                    <p style="margin: 0; font-size: 20px; color: #18181b; font-weight: 700;">#{{ $order->id }} — {{ $order->name ?? 'بدون اسم' }}</p>
                                                </td>
                                                <td align="left" style="vertical-align: top;">
                                                    <span style="display: inline-block; background-color: {{ \App\Models\UnitOrder::STATUS_COLORS[$order->status] ?? '#71717a' }}15; color: {{ \App\Models\UnitOrder::STATUS_COLORS[$order->status] ?? '#71717a' }}; border: 1px solid {{ \App\Models\UnitOrder::STATUS_COLORS[$order->status] ?? '#71717a' }}30; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">{{ $statusLabel }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Assignment Notice --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 16px 24px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f5; border-radius: 8px;">
                                            <tr>
                                                <td style="padding: 12px 16px;">
                                                    <p style="margin: 0; font-size: 13px; color: #52525b;">
                                                        تم تعيينك لمتابعة هذا الطلب — <span style="color: #71717a;">{{ $orderSourceLabel }}</span>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Client Section --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0 24px 20px;">
                                        <p style="margin: 0 0 10px; font-size: 12px; color: #a1a1aa; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">العميل</p>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e4e4e7; border-radius: 8px;">
                                            {{-- Name --}}
                                            <tr>
                                                <td style="padding: 10px 14px; border-bottom: 1px solid #f4f4f5; width: 100px;">
                                                    <span style="font-size: 12px; color: #a1a1aa;">الاسم</span>
                                                </td>
                                                <td style="padding: 10px 14px; border-bottom: 1px solid #f4f4f5;">
                                                    <span style="font-size: 14px; color: #18181b; font-weight: 500;">{{ $order->name ?? '—' }}</span>
                                                </td>
                                            </tr>
                                            {{-- Phone --}}
                                            <tr>
                                                <td style="padding: 10px 14px; border-bottom: 1px solid #f4f4f5;">
                                                    <span style="font-size: 12px; color: #a1a1aa;">الهاتف</span>
                                                </td>
                                                <td style="padding: 10px 14px; border-bottom: 1px solid #f4f4f5;">
                                                    <a href="tel:{{ $order->phone }}" style="font-size: 14px; color: #18181b; font-weight: 500; text-decoration: none; direction: ltr; unicode-bidi: embed;">{{ $order->phone ?? '—' }}</a>
                                                </td>
                                            </tr>
                                            {{-- Email --}}
                                            <tr>
                                                <td style="padding: 10px 14px;">
                                                    <span style="font-size: 12px; color: #a1a1aa;">البريد</span>
                                                </td>
                                                <td style="padding: 10px 14px;">
                                                    <a href="mailto:{{ $order->email }}" style="font-size: 14px; color: #18181b; font-weight: 500; text-decoration: none;">{{ $order->email ?? '—' }}</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Project & Unit Section --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0 24px 20px;">
                                        <p style="margin: 0 0 10px; font-size: 12px; color: #a1a1aa; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">المشروع والوحدة</p>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e4e4e7; border-radius: 8px;">
                                            <tr>
                                                <td style="padding: 10px 14px; border-bottom: 1px solid #f4f4f5; width: 100px;">
                                                    <span style="font-size: 12px; color: #a1a1aa;">المشروع</span>
                                                </td>
                                                <td style="padding: 10px 14px; border-bottom: 1px solid #f4f4f5;">
                                                    <span style="font-size: 14px; color: #18181b; font-weight: 500;">{{ $order->project->name ?? '—' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 14px; border-bottom: 1px solid #f4f4f5;">
                                                    <span style="font-size: 12px; color: #a1a1aa;">الوحدة</span>
                                                </td>
                                                <td style="padding: 10px 14px; border-bottom: 1px solid #f4f4f5;">
                                                    <span style="font-size: 14px; color: #18181b; font-weight: 500;">{{ $order->unit->title ?? '—' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 14px; border-bottom: 1px solid #f4f4f5;">
                                                    <span style="font-size: 12px; color: #a1a1aa;">نوع الشراء</span>
                                                </td>
                                                <td style="padding: 10px 14px; border-bottom: 1px solid #f4f4f5;">
                                                    <span style="font-size: 14px; color: #18181b; font-weight: 500;">{{ $purchaseTypeLabel }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 14px;">
                                                    <span style="font-size: 12px; color: #a1a1aa;">الغرض</span>
                                                </td>
                                                <td style="padding: 10px 14px;">
                                                    <span style="font-size: 14px; color: #18181b; font-weight: 500;">{{ $purchasePurposeLabel }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Extra Info (Marketing Source / Message) --}}
                            @if($order->message || $marketingSource)
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0 24px 20px;">
                                        <p style="margin: 0 0 10px; font-size: 12px; color: #a1a1aa; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">معلومات إضافية</p>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e4e4e7; border-radius: 8px;">
                                            @if($marketingSource)
                                            <tr>
                                                <td style="padding: 10px 14px;{{ $order->message ? ' border-bottom: 1px solid #f4f4f5;' : '' }} width: 100px;">
                                                    <span style="font-size: 12px; color: #a1a1aa;">المصدر</span>
                                                </td>
                                                <td style="padding: 10px 14px;{{ $order->message ? ' border-bottom: 1px solid #f4f4f5;' : '' }}">
                                                    <span style="font-size: 14px; color: #18181b; font-weight: 500;">{{ $marketingSource }}</span>
                                                </td>
                                            </tr>
                                            @endif
                                            @if($order->message)
                                            <tr>
                                                <td style="padding: 10px 14px; width: 100px; vertical-align: top;">
                                                    <span style="font-size: 12px; color: #a1a1aa;">الرسالة</span>
                                                </td>
                                                <td style="padding: 10px 14px;">
                                                    <span style="font-size: 13px; color: #3f3f46; line-height: 1.5;">{{ $order->message }}</span>
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            {{-- Bank Details --}}
                            @if($order->bank_name || $order->bank_employee_name)
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0 24px 20px;">
                                        <p style="margin: 0 0 10px; font-size: 12px; color: #a1a1aa; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">بيانات البنك</p>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e4e4e7; border-radius: 8px;">
                                            @if($order->bank_name)
                                            <tr>
                                                <td style="padding: 10px 14px; border-bottom: 1px solid #f4f4f5; width: 100px;">
                                                    <span style="font-size: 12px; color: #a1a1aa;">البنك</span>
                                                </td>
                                                <td style="padding: 10px 14px; border-bottom: 1px solid #f4f4f5;">
                                                    <span style="font-size: 14px; color: #18181b; font-weight: 500;">{{ $order->bank_name }}</span>
                                                </td>
                                            </tr>
                                            @endif
                                            @if($order->bank_employee_name)
                                            <tr>
                                                <td style="padding: 10px 14px;{{ $order->bank_employee_phone ? ' border-bottom: 1px solid #f4f4f5;' : '' }} width: 100px;">
                                                    <span style="font-size: 12px; color: #a1a1aa;">الموظف</span>
                                                </td>
                                                <td style="padding: 10px 14px;{{ $order->bank_employee_phone ? ' border-bottom: 1px solid #f4f4f5;' : '' }}">
                                                    <span style="font-size: 14px; color: #18181b; font-weight: 500;">{{ $order->bank_employee_name }}</span>
                                                </td>
                                            </tr>
                                            @endif
                                            @if($order->bank_employee_phone)
                                            <tr>
                                                <td style="padding: 10px 14px; width: 100px;">
                                                    <span style="font-size: 12px; color: #a1a1aa;">هاتفه</span>
                                                </td>
                                                <td style="padding: 10px 14px;">
                                                    <a href="tel:{{ $order->bank_employee_phone }}" style="font-size: 14px; color: #18181b; font-weight: 500; text-decoration: none; direction: ltr; unicode-bidi: embed;">{{ $order->bank_employee_phone }}</a>
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            {{-- CTA Button --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 4px 24px 24px;">
                                        <a href="{{ $orderUrl }}" style="display: block; background-color: #18181b; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; text-align: center;">
                                            عرض الطلب
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 20px 0; text-align: center;">
                            <p style="margin: 0 0 4px; font-size: 12px; color: #a1a1aa;">
                                {{ $order->created_at->translatedFormat('d M Y — h:i A') }}
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #d4d4d8;">
                                {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
