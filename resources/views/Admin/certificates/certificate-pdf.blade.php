@php
    use Illuminate\Support\Str;

    $primary = $theme['primary'] ?? '#2563eb';
    $primaryDark = $theme['primaryDark'] ?? '#1d4ed8';
    $accent = $theme['accent'] ?? '#f97316';

    $studentName = strtoupper($student?->hoTen ?? $certificate->student?->hoTen ?? 'OCC STUDENT');

    // Fix: courseName bị gán nhầm hoTen ở code cũ (dòng này bạn có thể bỏ nếu muốn)
    $courseName = $course->tenKH ?? 'OCC Course';

    $issuedDate = $issuedDateLabel ?? now()->format('d/m/Y');

    /**
     * (1) Fix watermark path:
     * - Chỉ dùng ảnh khi path local tồn tại
     * - Nếu là URL thì giữ nguyên (nhưng nếu dompdf không bật remote sẽ không load được)
     */
    $templatePath = null;

    if (!empty($template?->template_url)) {
        if (Str::startsWith($template->template_url, ['http://', 'https://'])) {
            $templatePath = $template->template_url;
        } else {
            $localPath = public_path(ltrim($template->template_url, '/'));
            if (file_exists($localPath)) {
                $templatePath = $localPath;
            }
        }
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
    }
    @font-face {
        font-family: 'Inter';
        src: url("{{ storage_path('fonts/Inter_18pt-Bold.ttf') }}") format('truetype');
        font-weight: 700;
    }

    /* Đảm bảo đúng kích thước A4 */
    @page {
        size: A4;
        margin: 0;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', sans-serif;
        background: #0f172a;
        height: 297mm;
        width: 210mm;
    }

    .wrapper {
        width: 210mm;
        height: 297mm;
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
        top: 0; right: 0; bottom: 0; left: 0;
        background:
            radial-gradient(circle at 18% 22%, rgba(37,99,235,.45), transparent 58%),
            radial-gradient(circle at 82% 12%, rgba(249,115,22,.35), transparent 48%);
        z-index: 0;
    }

    /* (2) Fix watermark: absolute full overlay + z-index để KHÔNG chiếm layout => không đẩy sang trang 2 */
    .watermark {
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        opacity: 0.07;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        z-index: 1;
    }
    .watermark img { max-width: 70%; }

    .card {
        width: 185mm;           /* ~88% chiều ngang A4 */
        height: 260mm;          /* để vừa trong chiều dọc */
        background: #ffffff;
        border-radius: 20px;
        padding: 25mm 20mm;     /* margin trên/dưới/trái/phải */
        box-shadow: 0 25px 70px rgba(0,0,0,0.35);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        box-sizing: border-box;
        z-index: 2;             /* nằm trên watermark */
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5mm;
    }
    .branding span { font-size: 13pt; color: #475569; letter-spacing: 1.8px; }
    .branding strong { font-size: 19pt; color: {{ $primary }}; letter-spacing: 4px; font-weight: 700; }

    .badge {
        padding: 6px 18px;
        background: {{ $primary }};
        color: white;
        border-radius: 50px;
        font-size: 10pt;
        letter-spacing: 2px;
    }

    .title { text-align: center; margin: 10mm 0 12mm 0; }
    .title p { margin:0; color:#94a3b8; letter-spacing:5px; font-size:11pt; }
    .title h1 { margin:4px 0 0; font-size:34pt; letter-spacing:3px; color:#0f172a; font-weight:700; }

    .recipient { text-align:center; margin-bottom:10mm; }
    .recipient label { color:#94a3b8; letter-spacing:5px; font-size:11pt; }
    .recipient h2 { margin:3px 0 0; font-size:42pt; color:{{ $primaryDark }}; font-weight:700; line-height:1.1; }

    .statement {
        text-align:center;
        font-size:15pt;
        line-height:1.7;
        color:#475569;
        margin-bottom:10mm;
        padding:0 5mm;
    }
    .statement strong { color:{{ $primary }}; }

    .details {
        display:flex;
        justify-content:center;
        gap:20mm;
        margin-bottom:10mm;
        flex-wrap:wrap;
    }
    .details .item {
        text-align:center;
        min-width:120px;
    }
    .details .item span {
        display:block;
        color:#94a3b8;
        font-size:10pt;
        letter-spacing:3px;
        margin-bottom:2px;
    }
    .details .item strong {
        font-size:14pt;
        color:#0f172a;
        letter-spacing:1px;
    }

    .seal {
        text-align:center;
        padding:12px 30px;
        border:2px solid {{ $primary }};
        border-radius:15px;
        align-self:center;
    }
    .seal span { display:block; color:#94a3b8; font-size:10pt; letter-spacing:3px; }
    .seal strong { font-size:15pt; color:#0f172a; margin-top:1px; }
</style>
</head>
<body>
<div class="wrapper">
    {{-- <div class="watermark">
        @if(!empty($templatePath))
            <img src="{{ $templatePath }}" alt="watermark">
        @else
            <svg width="500" height="500" viewBox="0 0 500 500">
                <circle cx="250" cy="250" r="200" fill="none" stroke="{{ $primary }}" stroke-width="15" opacity="0.3"/>
            </svg>
        @endif
    </div> --}}

    <div class="card">
        <div class="header">
            <div class="branding">
                <span>Online Certificate Classroom</span>
                <strong>OCC</strong>
            </div>
            <div class="badge">ISSUED COURSE</div>
        </div>

        <div class="title">
            <p>THIS CERTIFIES</p>
            <h1>CERTIFICATE OF ACHIEVEMENT</h1>
        </div>

        <div class="recipient">
            <label>AWARDED TO</label>
            <h2>{{ $studentName }}</h2>
        </div>

        <div class="statement">
            This certificate recognizes outstanding completion of the <strong>{{ $courseName }}</strong> program and meeting every proficiency standard established by Online Certificate Classroom.
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

        <div class="seal">
            <span>AUTHORIZED BY</span>
            <strong>Online Certificate Classroom</strong>
        </div>
    </div>
</div>
</body>
</html>
