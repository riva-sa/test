<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: 'dejavusans', sans-serif; }
        body { color: #1f2937; font-size: 11px; }

        .header { width: 100%; border-bottom: 2px solid #111827; padding-bottom: 10px; margin-bottom: 6px; }
        .header td { vertical-align: middle; }
        .logo-img { max-height: 60px; max-width: 150px; }
        .header .title { text-align: center; }
        .header .title h1 { font-size: 17px; margin: 0; color: #111827; }
        .header .title .sub { font-size: 10px; color: #6b7280; margin-top: 3px; }

        .meta { width: 100%; margin-bottom: 12px; font-size: 10px; color: #6b7280; }
        .meta td { padding: 2px 0; }
        .meta .label { color: #9ca3af; }

        table.units { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.units thead th {
            background: #111827; color: #ffffff; font-size: 10px;
            padding: 8px 6px; text-align: center; font-weight: bold;
        }
        table.units tbody td {
            border-bottom: 1px solid #e5e7eb; padding: 7px 6px;
            text-align: center; font-size: 10px;
        }
        table.units tbody tr.alt td { background: #f9fafb; }
        .price { font-weight: bold; color: #047857; white-space: nowrap; }
        .on-request { color: #9ca3af; font-size: 9px; }
        .status { font-weight: bold; font-size: 9px; white-space: nowrap; }
        .status-available { color: #047857; }
        .status-reserved { color: #b45309; }
        .status-sold { color: #b91c1c; }

        .empty { text-align: center; padding: 30px; color: #9ca3af; }

        .notice {
            margin-top: 14px; padding: 8px 10px; background: #fffbeb;
            border: 1px solid #fde68a; border-radius: 6px;
            font-size: 9px; color: #92400e; text-align: center;
        }
        .footer { margin-top: 16px; border-top: 1px solid #e5e7eb; padding-top: 8px;
                  font-size: 8.5px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td width="28%" style="text-align: right;">
                @if ($rivaLogo)
                    <img class="logo-img" src="{{ $rivaLogo }}" alt="ريفا">
                @endif
            </td>
            <td width="44%" class="title">
                <h1>قائمة أسعار الوحدات</h1>
                <div class="sub">{{ $project->name }}</div>
            </td>
            <td width="28%" style="text-align: left;">
                @if ($developerLogo)
                    <img class="logo-img" src="{{ $developerLogo }}" alt="{{ $project->developer->name ?? '' }}">
                @endif
            </td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td width="50%">
                <span class="label">المطوّر:</span> {{ $project->developer->name ?? '—' }}
            </td>
            <td width="50%" style="text-align: left;">
                <span class="label">المدينة:</span> {{ $project->city->name ?? '—' }}
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">إجمالي الوحدات:</span> {{ $units->count() }}
                <span class="label" style="margin-right: 6px;">المتاحة:</span> {{ $units->where('case', 0)->count() }}
            </td>
            <td style="text-align: left;">
                <span class="label">تاريخ الاستخراج:</span> {{ $generatedAt->format('Y-m-d') }} — {{ $generatedAt->format('H:i') }}
            </td>
        </tr>
    </table>

    @if ($units->isEmpty())
        <div class="empty">لا توجد وحدات في هذا المشروع.</div>
    @else
        <table class="units">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="24%">الوحدة</th>
                    <th width="14%">النوع</th>
                    <th width="11%">المساحة</th>
                    <th width="8%">الدور</th>
                    <th width="8%">الغرف</th>
                    <th width="13%">الحالة</th>
                    <th width="17%">السعر</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($units as $i => $unit)
                    <tr class="{{ $i % 2 ? 'alt' : '' }}">
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $unit->title }}</td>
                        <td>{{ $unit->unit_type ?: '—' }}</td>
                        <td>{{ $unit->unit_area ? $unit->unit_area . ' م²' : '—' }}</td>
                        <td>{{ $unit->floor !== null && $unit->floor !== '' ? $unit->floor : '—' }}</td>
                        <td>{{ $unit->beadrooms ?: '—' }}</td>
                        <td>
                            @if ($unit->case == 0)
                                <span class="status status-available">متاحة</span>
                            @elseif ($unit->case == 1)
                                <span class="status status-reserved">محجوزة</span>
                            @elseif ($unit->case == 3)
                                <span class="status status-reserved">تحت الإنشاء</span>
                            @else
                                <span class="status status-sold">مباعة</span>
                            @endif
                        </td>
                        <td>
                            @if (in_array($unit->case, [1, 2]))
                                <span class="on-request">—</span>
                            @elseif ($unit->show_price && $unit->unit_price)
                                <span class="price">{{ number_format((float) $unit->unit_price) }} ر.س</span>
                            @else
                                <span class="on-request">السعر عند الطلب</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="notice">
        الأسعار وحالة الوحدات المعروضة في هذا المستند صحيحة بتاريخ الاستخراج فقط، وقابلة للتغيير في أي وقت دون إشعار مسبق.
    </div>

    <div class="footer">
        {{ config('app.name', 'ريفا') }} — تم الاستخراج بتاريخ {{ $generatedAt->format('Y-m-d H:i') }}
    </div>
</body>
</html>
