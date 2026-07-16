<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب وحدة جديد</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #122818;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .content {
            padding: 40px 30px;
        }
        .info-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-right: 4px solid #122818;
        }
        .info-section h3 {
            margin-top: 0;
            color: #122818;
            font-size: 18px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 120px;
        }
        .info-value {
            color: #333;
            flex: 1;
            text-align: left;
        }
        .recipient-notice {
            background: #e3f2fd;
            border: 1px solid #122818;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            color: #122818;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px 15px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                margin-bottom: 5px;
                min-width: auto;
            }
            .info-value {
                text-align: right;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                 طلب وحدة جديد
            </h1>
        </div>

        <div class="content" dir="rtl">
            @if($recipientType == 'sales')
                <div class="recipient-notice">
                    <strong>📧 إشعار مدير المبيعات:</strong> تم استلام طلب وحدة جديد في مشروعك.
                </div>
            @elseif($recipientType == 'sales_manager')
                <div class="recipient-notice">
                    <strong>👔 إشعار الإدارة العامة:</strong> تم استلام طلب وحدة جديد يتطلب المراجعة.
                </div>
            @else
                <div class="recipient-notice">
                    <strong>🔔 إشعار:</strong> تم استلام طلب وحدة جديد تملك صلاحية الوصول إليه.
                </div>
            @endif

            <div class="info-section" dir="rtl">
                <h3>📋 تفاصيل العميل</h3>
                <div class="info-row">
                    <span class="info-label">الاسم:</span>
                    <span class="info-value">{{ $emailData['customer_name'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">البريد الإلكتروني:</span>
                    <span class="info-value">{{ $emailData['customer_email'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">رقم الهاتف:</span>
                    <span class="info-value">{{ $emailData['customer_phone'] }}</span>
                </div>
            </div>

            <div class="info-section" dir="rtl">
                <h3>🏢 تفاصيل المشروع والوحدة</h3>
                <div class="info-row">
                    <span class="info-label">اسم المشروع:</span>
                    <span class="info-value">{{ $emailData['project']->name ?? 'غير محدد' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">رقم الوحدة:</span>
                    <span class="info-value">{{ $emailData['unit']->unit_number ?? 'غير محدد' }}</span>
                </div>
                @if(isset($emailData['unit']->area))
                <div class="info-row">
                    <span class="info-label">المساحة:</span>
                    <span class="info-value">{{ $emailData['unit']->area }} متر مربع</span>
                </div>
                @endif
                @if(isset($emailData['unit']->price))
                <div class="info-row">
                    <span class="info-label">السعر:</span>
                    <span class="info-value">{{ number_format($emailData['unit']->price) }} ريال</span>
                </div>
                @endif
            </div>

            <div class="info-section" dir="rtl">
                <h3>💰 تفاصيل الشراء</h3>
                <div class="info-row">
                    <span class="info-label">طريقة الدفع:</span>
                    <span class="info-value">{{ $emailData['purchase_type'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">الغرض من الشراء:</span>
                    <span class="info-value">{{ $emailData['purchase_purpose'] }}</span>
                </div>
                @if($emailData['unit_order']->support_type)
                <div class="info-row">
                    <span class="info-label">نوع الدعم:</span>
                    <span class="info-value">{{ $emailData['unit_order']->support_type }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">حالة الطلب:</span>
                    <span class="info-value">
                        <span class="status-badge">في انتظار المراجعة</span>
                    </span>
                </div>
            </div>

            <div class="info-section" dir="rtl">
                <h3>📅 معلومات إضافية</h3>
                <div class="info-row">
                    <span class="info-label">تاريخ الطلب:</span>
                    <span class="info-value">{{ $emailData['unit_order']->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">رقم الطلب:</span>
                    <span class="info-value">#{{ $emailData['unit_order']->id }}</span>
                </div>
            </div>
        </div>

        <div class="footer" dir="rtl">
            <p>هذا إشعار تلقائي من نظام إدارة العقارات في ريفا</p>
            <p>{{ config('app.name') }} © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>