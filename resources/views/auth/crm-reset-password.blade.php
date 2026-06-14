<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعادة تعيين كلمة المرور - ريفا العقارية</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-[#fcfcfc] min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary-100 rounded-full blur-[120px] opacity-50"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary-50 rounded-full blur-[120px] opacity-50"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <div class="text-center mb-10">
            <img src="{{ asset('frontend/img/logoyy.png') }}" width="64px" alt="ريفا" class="mx-auto mb-6 shadow-sm rounded-xl">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">إعادة تعيين كلمة المرور</h1>
            <p class="text-gray-500 mt-2 text-sm">قم بتعيين كلمة مرور جديدة قوية لحسابك</p>
        </div>

        <div class="glass border border-gray-100 rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.04)] p-8 md:p-10">
            @if ($errors->any())
                <div class="mb-8 p-4 bg-red-50 border border-red-100 rounded-2xl">
                    <div class="flex items-center mb-1 text-red-700 font-bold text-sm">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        هناك بعض المشاكل:
                    </div>
                    @foreach ($errors->all() as $error)
                        <p class="text-xs text-red-600 mr-6 mt-1 leading-relaxed">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 mr-1">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required autofocus
                           class="w-full px-5 py-4 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-50 focus:border-primary-300 outline-none transition-all text-gray-900 bg-gray-50/50 text-sm placeholder:text-gray-300"
                           placeholder="name@example.com">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 mr-1">كلمة المرور الجديدة</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-5 py-4 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-50 focus:border-primary-300 outline-none transition-all text-gray-900 bg-gray-50/50 text-sm placeholder:text-gray-300"
                           placeholder="8 أحرف على الأقل">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 mr-1">تأكيد كلمة المرور</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-5 py-4 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary-50 focus:border-primary-300 outline-none transition-all text-gray-900 bg-gray-50/50 text-sm placeholder:text-gray-300"
                           placeholder="أعد إدخال كلمة المرور">
                </div>

                <button type="submit"
                        class="w-full py-4 px-6 bg-primary-800 hover:bg-primary-900 text-white font-bold rounded-2xl transition-all duration-300 text-sm shadow-lg shadow-primary-900/10 active:scale-[0.98]">
                    تغيير كلمة المرور
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-gray-50 text-center">
                <a href="{{ route('login') }}" class="text-xs font-bold text-gray-400 hover:text-primary-600 transition-colors uppercase tracking-widest">
                    العودة لتسجيل الدخول
                </a>
            </div>
        </div>
        
        <p class="text-center mt-10 text-[10px] text-gray-400 font-medium tracking-wide uppercase">
            &copy; {{ date('Y') }} ريفا العقارية. جميع الحقوق محفوظة.
        </p>
    </div>
</body>
</html>

