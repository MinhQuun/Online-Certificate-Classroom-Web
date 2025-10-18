<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Học viên - Online Certificate Classroom')</title>
    <link rel="icon" type="image/png" href="/favicon.ico">
    <link rel="stylesheet" href="/css/Student/student.css?v={{ filemtime(public_path('css/Student/student.css')) ?: '1' }}">
</head>
<body>
    <header class="oc-header">
        <div class="oc-container oc-header__content">
            <a href="{{ route('student.courses.index') }}" class="oc-logo">
                <img src="/Assets/logo.png" alt="Logo"/>
                <span>Online Certificate Classroom</span>
            </a>
            <nav class="oc-nav">
                <a href="{{ route('student.courses.index') }}">Khóa học</a>
            </nav>
        </div>
    </header>

    <main class="oc-main">
        @yield('content')
    </main>

    <footer class="oc-footer">
        <div class="oc-container">
            <p>© {{ date('Y') }} Online Certificate Classroom</p>
        </div>
    </footer>
    <script src="/js/Student/student.js?v={{ filemtime(public_path('js/Student/student.js')) ?: '1' }}" defer></script>
</body>
</html>

