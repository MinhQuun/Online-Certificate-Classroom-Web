@php
    $typeMeta = [
        'success' => ['title' => 'Thành công', 'icon' => 'fa-circle-check'],
        'error'   => ['title' => 'Lỗi',        'icon' => 'fa-circle-exclamation'],
        'warning' => ['title' => 'Chú ý',      'icon' => 'fa-triangle-exclamation'],
        'info'    => ['title' => 'Thông báo',  'icon' => 'fa-circle-info'],
    ];

    $flashToasts = [];

    foreach ($typeMeta as $type => $meta) {
        if (!session()->has($type)) {
            continue;
        }

        $message = session($type);
        $title = session("{$type}_title") ?? $meta['title'];
        $autohide = session("{$type}_autohide");

        $flashToasts[] = [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'autohide' => $autohide,
        ];
    }

    $sessionToast = session('toast');
    if ($sessionToast) {
        $rawToasts = is_array($sessionToast) && array_is_list($sessionToast)
            ? $sessionToast
            : [is_array($sessionToast) ? $sessionToast : ['message' => $sessionToast]];

        foreach ($rawToasts as $toast) {
            if (!is_array($toast)) {
                $toast = ['message' => (string) $toast];
            }

            $type = $toast['type'] ?? 'info';
            $meta = $typeMeta[$type] ?? $typeMeta['info'];

            $flashToasts[] = [
                'type' => $type,
                'title' => $toast['title'] ?? $meta['title'],
                'message' => $toast['message'] ?? null,
                'list' => isset($toast['list']) && is_array($toast['list'])
                    ? array_values(array_filter($toast['list']))
                    : null,
                'autohide' => $toast['autohide'] ?? $toast['duration'] ?? null,
            ];
        }
    }

    $errorMessages = [];
    if ($errors instanceof \Illuminate\Support\ViewErrorBag) {
        foreach ($errors->getBags() as $bag) {
            foreach ($bag->all() as $message) {
                $errorMessages[] = $message;
            }
        }
    } elseif ($errors instanceof \Illuminate\Contracts\Support\MessageBag) {
        $errorMessages = $errors->all();
    }

    $errorMessages = array_values(array_unique(array_filter($errorMessages)));

    if (!empty($errorMessages)) {
        $flashToasts[] = [
            'type' => 'error',
            'title' => 'Dữ liệu chưa hợp lệ',
            'list' => $errorMessages,
            'autohide' => 6500,
        ];
    }
@endphp

@if (!empty($flashToasts))
    <div class="toast-stack" role="status" aria-live="polite">
        @foreach ($flashToasts as $toast)
            @php
                $type = $toast['type'] ?? 'info';
                $meta = $typeMeta[$type] ?? $typeMeta['info'];
                $autoHide = $toast['autohide'] ?? (isset($toast['list']) ? 6500 : 4200);
            @endphp
            <div class="toast-card is-{{ $type }}" data-autohide="{{ $autoHide }}">
                <div class="toast-icon">
                    <i class="fa-solid {{ $meta['icon'] }}" aria-hidden="true"></i>
                </div>
                <div class="toast-content">
                    <strong>{{ $toast['title'] ?? $meta['title'] }}</strong>
                    @if (!empty($toast['list']))
                        <ul class="toast-errors">
                            @foreach ($toast['list'] as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    @elseif (!empty($toast['message']) && trim((string) $toast['message']) !== trim((string) ($toast['title'] ?? '')))
                        <div class="toast-text">{{ $toast['message'] }}</div>
                    @endif
                </div>
                <button class="toast-close" aria-label="Đóng">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endforeach
    </div>
@endif
