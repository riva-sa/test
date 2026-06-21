<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور - {{ config('app.name', 'ريفا') }}</title>
    <link rel="shortcut icon" href="{{ asset('frontend/img/logoyy.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/broker.css'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <img src="{{ asset('frontend/img/logoyy.png') }}" class="h-14 w-auto mx-auto mb-4" alt="Logo">
            <h1 class="text-2xl font-black text-gray-900">إعادة تعيين كلمة المرور</h1>
            <p class="text-sm text-gray-500 mt-2">قم بتعيين كلمة مرور جديدة قوية لحسابك</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 text-sm font-bold rounded-xl leading-relaxed">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('broker.password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm"
                           placeholder="example@email.com">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">كلمة المرور الجديدة</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm"
                           placeholder="8 أحرف على الأقل">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm"
                           placeholder="أعد إدخال كلمة المرور">
                </div>

                <button type="submit" class="w-full py-3.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-black rounded-xl transition-all">
                    تغيير كلمة المرور
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('broker.login') }}" class="text-sm font-bold text-gray-500 hover:text-gray-900 transition-colors">
                    العودة لتسجيل الدخول
                </a>
            </div>
        </div>
    </div>
</body>
</html>
