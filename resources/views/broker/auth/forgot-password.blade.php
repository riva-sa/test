<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نسيت كلمة المرور - {{ config('app.name', 'ريفا') }}</title>
    <link rel="shortcut icon" href="{{ asset('frontend/img/logoyy.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/broker.css'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <img src="{{ asset('frontend/img/logoyy.png') }}" class="h-14 w-auto mx-auto mb-4" alt="Logo">
            <h1 class="text-2xl font-black text-gray-900">نسيت كلمة المرور؟</h1>
            <p class="text-sm text-gray-500 mt-2">أدخل بريدك الإلكتروني وسنرسل لك رابطًا لإعادة التعيين</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 text-sm font-bold rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('broker.password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm"
                           placeholder="example@email.com">
                    @error('email')
                        <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-3.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-black rounded-xl transition-all">
                    إرسال رابط إعادة التعيين
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
