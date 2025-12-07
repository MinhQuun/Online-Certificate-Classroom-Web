@php
    $studentCoreStyles = [
        'css/Student/foundation.css',
        'css/Student/layout.css',
        'css/Student/elements.css',
        'css/Student/notifications.css',
    ];

    $studentCoreScripts = [
        'js/Student/main.js',
        'js/Student/dropdown.js',
        'js/Student/accordion-toggle.js',
        'js/Student/notifications.js',
    ];
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="{{ asset('assets/favicon_io/favicon.ico') }}" type="image/x-icon">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Online Certificate Classroom')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    <script src="https://kit.fontawesome.com/cdbcf8b89b.js" crossorigin="anonymous" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @foreach ($studentCoreStyles as $style)
        <link rel="stylesheet" href="{{ asset($style) }}?v={{ student_asset_version($style) }}">
    @endforeach

    <link rel="stylesheet" href="{{ asset('css/Student/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common/toast.css') }}">
    <link href="{{ asset('css/Student/dictionary.css') }}" rel="stylesheet">

    @stack('styles')
    @php $responsiveStyle = 'css/Student/responsive-overrides.css'; @endphp
    <link rel="stylesheet" href="{{ asset($responsiveStyle) }}?v={{ student_asset_version($responsiveStyle) }}">
</head>
<body>
    @include('partials.header')
    @include('partials.auth-modal')
    @include('partials.flash')

    <div class="site-body">
        @yield('content')
        @include('components.dictionary-widget')
    </div>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    @foreach ($studentCoreScripts as $script)
        <script src="{{ asset($script) }}?v={{ student_asset_version($script) }}" defer></script>
    @endforeach

    <script src="{{ asset('js/Student/auth.js') }}" defer></script>
    <script src="{{ asset('js/Student/flash.js') }}" defer></script>
    <script src="{{ asset('js/Student/dictionary.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
