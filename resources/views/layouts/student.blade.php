@php
    $studentCoreStyles = [
        'css/Student/foundation.css',
        'css/Student/layout.css',
        'css/Student/elements.css',
    ];

    $studentCoreScripts = [
        'js/Student/main.js',
        'js/Student/dropdown.js',
        'js/Student/accordion-toggle.js',
    ];
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Online Certificate Classroom')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    @foreach ($studentCoreStyles as $style)
        <link rel="stylesheet" href="{{ asset($style) }}?v={{ student_asset_version($style) }}">
    @endforeach
    @stack('styles')
</head>
<body>
    @include('partials.header')

    <div class="site-body">
        @yield('content')
    </div>

    @include('partials.footer')

    @foreach ($studentCoreScripts as $script)
        <script src="{{ asset($script) }}?v={{ student_asset_version($script) }}" defer></script>
    @endforeach
    @stack('scripts')
</body>
</html>
