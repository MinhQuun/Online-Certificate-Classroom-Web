@php
    $primary = $theme['primary'] ?? '#2563eb';
    $primaryDark = $theme['primaryDark'] ?? '#1d4ed8';
    $accent = $theme['accent'] ?? '#f97316';
    $studentName = strtoupper($student?->hoTen ?? $certificate->student?->hoTen ?? 'OCC STUDENT');
    $courseName = $course->tenKH ?? 'OCC Course';
    $issuedDate = $issuedDateLabel ?? now()->format('M d, Y');
    $teacherName = $course->teacher?->hoTen ?? 'OCC Academic Board';
    $templatePath = null;
    if (!empty($template?->template_url)) {
        $templatePath = Str::startsWith($template->template_url, ['http://', 'https://'])
            ? $template->template_url
            : public_path(trim($template->template_url, '/'));
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OCC Certificate - {{ $certificate->code }}</title>
    <style>
        @font-face {
        font-family: 'Inter';
        src: url("{{ storage_path('fonts/Inter_18pt-Regular.ttf') }}") format('truetype');
        font-weight: 400;
        font-style: normal;
        }
        @font-face {
        font-family: 'Inter';
        src: url("{{ storage_path('fonts/Inter_18pt-Bold.ttf') }}") format('truetype');
        font-weight: 700;
        font-style: normal;
        }
        
        @page { margin: 0; }
        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            background: #0f172a;
        }
        .wrapper {
            width: 11.7in;
            height: 8.3in;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, rgba(15,23,42,.95), rgba(15,23,42,.75));
            position: relative;
            overflow: hidden;
        }
        .wrapper::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 18% 22%, rgba(37,99,235,.45), transparent 58%),
                radial-gradient(circle at 82% 12%, rgba(249,115,22,.35), transparent 48%);
        }
        .card {
            width: 80%;
            min-height: 6.5in;
            padding: 50px 70px;
            background: #fbfdff;
            color: #0f172a;
            border-radius: 28px;
            position: relative;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .3);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .card::before,
        .card::after {
            content: '';
            position: absolute;
            inset: 18px;
            border-radius: 22px;
            border: 1px solid rgba(15,23,42,.1);
            pointer-events: none;
        }
        .card::after {
            inset: 30px;
            border-color: rgba(148, 163, 184, .2);
        }
        .card > * { position: relative; z-index: 2; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .branding {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .branding span {
            font-size: 18px;
            color: #475569;
            letter-spacing: .08em;
        }
        .branding strong {
            font-size: 22px;
            letter-spacing: .25em;
            color: {{ $primary }};
        }
        .badge {
            padding: 8px 22px;
            border-radius: 999px;
            background: {{ $primary }};
            color: #fff;
            letter-spacing: .25em;
            font-size: 12px;
        }
        .title-block {
            text-align: center;
        }
        .title-block p {
            margin: 0;
            letter-spacing: .45em;
            color: #94a3b8;
            font-size: 13px;
        }
        .title-block h1 {
            margin: 6px 0 0;
            font-size: 42px;
            letter-spacing: .08em;
        }
        .recipient {
            text-align: center;
        }
        .recipient label {
            letter-spacing: .4em;
            color: #94a3b8;
            font-size: 12px;
        }
        .recipient h2 {
            margin: 8px 0 0;
            font-size: 38px;
            color: {{ $primaryDark }};
        }
        .statement {
            text-align: center;
            font-size: 18px;
            color: #475569;
            line-height: 1.8;
        }
        .statement strong {
            color: {{ $primary }};
        }
        .details {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .details .item {
            padding: 14px 18px;
            border-radius: 14px;
            background: rgba(37, 99, 235, .08);
            min-width: 180px;
            text-align: center;
        }
        .details .item span {
            display: block;
            letter-spacing: .4em;
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 6px;
        }
        .details .item strong {
            font-size: 18px;
            color: #0f172a;
            letter-spacing: .08em;
        }
        .footer {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        .seal {
            padding: 18px 30px;
            border-radius: 18px;
            border: 2px solid {{ $primary }};
            text-align: center;
        }
        .seal span {
            display: block;
            letter-spacing: .4em;
            font-size: 12px;
            color: #94a3b8;
        }
        .seal strong {
            font-size: 18px;
            color: #0f172a;
        }
        .watermark {
            position: absolute;
            inset: 0;
            opacity: 0.08;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .watermark img {
            max-width: 55%;
            filter: grayscale(1);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="watermark">
            @if(!empty($templatePath))
                <img src="{{ $templatePath }}" alt="Certificate watermark">
            @else
                <svg width="420" height="420" viewBox="0 0 420 420" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="210" cy="210" r="180" stroke="{{ $primary }}" stroke-width="10" stroke-opacity="0.3" fill="none"/>
                    <circle cx="210" cy="210" r="140" stroke="{{ $accent }}" stroke-width="7" stroke-opacity="0.2" fill="none"/>
                    <circle cx="210" cy="210" r="100" stroke="{{ $primaryDark }}" stroke-width="5" stroke-opacity="0.15" fill="none"/>
                </svg>
            @endif
        </div>
        <div class="card">
            <div class="header">
                <div class="branding">
                    <span>Online Certificate Classroom</span>
                    <strong>OCC</strong>
                </div>
                <span class="badge">ISSUED COURSE</span>
            </div>
            <div class="title-block">
                <p>THIS CERTIFIES</p>
                <h1>CERTIFICATE OF ACHIEVEMENT</h1>
            </div>
            <div class="recipient">
                <label>AWARDED TO</label>
                <h2>{{ $studentName }}</h2>
            </div>
            <div class="statement">
                This certificate recognizes outstanding completion of the <strong>{{ $courseName }}</strong> program
                and meeting every proficiency standard established by Online Certificate Classroom.
            </div>
            <div class="details">
                <div class="item">
                    <span>CERT NO.</span>
                    <strong>{{ $certificate->code }}</strong>
                </div>
                <div class="item">
                    <span>ISSUED</span>
                    <strong>{{ $issuedDate }}</strong>
                </div>
                <div class="item">
                    <span>PROGRAM</span>
                    <strong>{{ Str::upper($courseName) }}</strong>
                </div>
            </div>
            <div class="footer">
                <div class="seal">
                    <span>AUTHORIZED BY</span>
                    <strong>Online Certificate Classroom</strong>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
