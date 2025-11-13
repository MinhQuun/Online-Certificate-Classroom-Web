@php
    $primary = $theme['primary'] ?? '#2563eb';
    $primaryDark = $theme['primaryDark'] ?? '#1d4ed8';
    $accent = $theme['accent'] ?? '#f97316';
    $text = $theme['text'] ?? '#0f172a';
    $muted = $theme['muted'] ?? '#64748b';
    $studentName = strtoupper($student?->hoTen ?? $certificate->student?->hoTen ?? 'HỌC VIÊN OCC');
    $targetLabel = $certificate->loaiCC === \App\Models\Certificate::TYPE_COMBO
        ? ($combo->tenGoi ?? 'Combo OCC')
        : ($course->tenKH ?? 'Khóa học OCC');
    $typeLabel = $certificate->loaiCC === \App\Models\Certificate::TYPE_COMBO ? 'COMBO' : 'COURSE';
    $issuedDate = $issuedDateLabel ?? now()->format('d/m/Y');
    $courseNames = collect($courseList ?? [])
        ->pluck('tenKH')
        ->filter()
        ->values();
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chứng chỉ OCC - {{ $certificate->code }}</title>
    <style>
        @page { margin: 0; }
        body {
            margin: 0;
            font-family: "Inter", "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: #e3e9fb;
        }
        .canvas {
            padding: 52px 72px;
            min-height: 100vh;
            position: relative;
            background: radial-gradient(circle at top right, rgba(37, 99, 235, 0.12), transparent),
                        radial-gradient(circle at top left, rgba(249, 115, 22, 0.08), transparent),
                        #f8fafc;
        }
        .certificate {
            position: relative;
            background: #fff;
            border-radius: 32px;
            padding: 64px 80px;
            box-shadow: 0 40px 80px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }
        .certificate::after {
            content: '';
            position: absolute;
            inset: 24px;
            border: 2px solid rgba(100, 116, 139, 0.2);
            border-radius: 28px;
            pointer-events: none;
        }
        .brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 40px;
            position: relative;
            z-index: 2;
        }
        .brand__logo {
            display: flex;
            align-items: center;
            gap: 14px;
            font-weight: 600;
            font-size: 20px;
            color: {{ $text }};
        }
        .brand__logo img {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }
        .status-chip {
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .08em;
            background: {{ $primary }};
            color: #fff;
        }
        .headline {
            text-align: center;
            margin-bottom: 36px;
            color: {{ $text }};
            position: relative;
            z-index: 2;
        }
        .headline span {
            display: inline-block;
            font-size: 16px;
            letter-spacing: .4em;
            color: {{ $muted }};
        }
        .headline h1 {
            margin: 12px 0 0;
            font-size: 48px;
            letter-spacing: .08em;
            font-weight: 700;
        }
        .recipient {
            text-align: center;
            margin-bottom: 48px;
            position: relative;
            z-index: 2;
        }
        .recipient .label {
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: .3em;
            color: {{ $muted }};
        }
        .recipient .name {
            font-size: 42px;
            font-weight: 700;
            margin-top: 12px;
            color: {{ $primaryDark }};
        }
        .description {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-bottom: 48px;
            color: {{ $text }};
            font-size: 18px;
            line-height: 1.7;
        }
        .description strong {
            color: {{ $primary }};
        }
        .courses-list {
            margin-top: 18px;
            font-size: 15px;
            color: {{ $muted }};
        }
        .meta {
            display: flex;
            justify-content: space-between;
            gap: 32px;
            margin-top: 48px;
            position: relative;
            z-index: 2;
        }
        .meta__block {
            flex: 1;
            padding: 18px 20px;
            border-radius: 16px;
            background: rgba(37, 99, 235, 0.05);
            border: 1px solid rgba(37, 99, 235, 0.12);
        }
        .meta__label {
            font-size: 13px;
            letter-spacing: .3em;
            color: {{ $muted }};
        }
        .meta__value {
            margin-top: 10px;
            font-size: 20px;
            font-weight: 600;
            color: {{ $text }};
        }
        .signature {
            margin-top: 56px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            position: relative;
            z-index: 2;
        }
        .signature__line {
            width: 240px;
            height: 1px;
            background: rgba(15, 23, 42, 0.2);
            margin-bottom: 8px;
        }
        .signature__label {
            font-size: 14px;
            color: {{ $muted }};
            text-transform: uppercase;
            letter-spacing: .2em;
        }
        .watermark {
            position: absolute;
            inset: 0;
            opacity: 0.08;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .watermark img {
            max-width: 80%;
            filter: grayscale(1);
        }
    </style>
</head>
<body>
    <div class="canvas">
        <div class="certificate">
            <div class="watermark">
                @if(!empty($template?->template_url))
                    <img src="{{ $template->template_url }}" alt="Certificate watermark">
                @else
                    <svg width="520" height="520" viewBox="0 0 520 520" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="260" cy="260" r="240" stroke="{{ $primary }}" stroke-width="12" stroke-opacity="0.4" fill="none"/>
                        <circle cx="260" cy="260" r="200" stroke="{{ $accent }}" stroke-width="8" stroke-opacity="0.25" fill="none"/>
                        <circle cx="260" cy="260" r="160" stroke="{{ $primaryDark }}" stroke-width="6" stroke-opacity="0.18" fill="none"/>
                    </svg>
                @endif
            </div>
            <div class="brand">
                <div class="brand__logo">
                    <img src="{{ asset('Assets/logo.png') }}" alt="OCC Logo">
                    <span>Online Certificate Classroom</span>
                </div>
                <span class="status-chip">ISSUED {{ $typeLabel }}</span>
            </div>
            <div class="headline">
                <span>CHỨNG NHẬN</span>
                <h1>Certificate of Achievement</h1>
            </div>
            <div class="recipient">
                <div class="label">TRAO TẶNG CHO</div>
                <div class="name">{{ $studentName }}</div>
            </div>
            <div class="description">
                <p>
                    Chứng nhận đã hoàn thành xuất sắc
                    <strong>{{ $certificate->loaiCC === \App\Models\Certificate::TYPE_COMBO ? 'combo' : 'khóa học' }}</strong>
                    <strong>{{ $targetLabel }}</strong>
                    và đáp ứng đầy đủ tiêu chí do OCC thiết lập.
                </p>
                @if($courseNames->isNotEmpty())
                    <div class="courses-list">
                        Bao gồm: {{ $courseNames->implode(', ') }}.
                    </div>
                @endif
            </div>
            <div class="meta">
                <div class="meta__block">
                    <div class="meta__label">MÃ CHỨNG CHỈ</div>
                    <div class="meta__value">{{ $certificate->code }}</div>
                </div>
                <div class="meta__block">
                    <div class="meta__label">NGÀY CẤP</div>
                    <div class="meta__value">{{ $issuedDate }}</div>
                </div>
            </div>
            <div class="signature">
                <div>
                    <div class="signature__line"></div>
                    <div class="signature__label">HỆ THỐNG OCC</div>
                </div>
                <div class="meta__block" style="flex:0 0 auto;">
                    <div class="meta__label">NGƯỜI KÝ</div>
                    <div class="meta__value">Online Certificate Classroom</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
