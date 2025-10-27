@if (session('success') || session('error') || session('warning') || session('info') || $errors->any())
    <div class="toast-stack" role="status" aria-live="polite">
        @php
            $toastLabels = [
                'success' => 'Thành công',
                'error'   => 'Lỗi',
                'warning' => 'Chú ý',
                'info'    => 'Thông báo',
            ];
        @endphp

        @foreach ($toastLabels as $type => $defaultLabel)
            @php
                $message = session($type);
                $title   = session("{$type}_title") ?? $defaultLabel;
                $showBody = $message !== null && trim((string) $message) !== trim((string) $title);
            @endphp

            @if ($message)
                <div class="toast-card is-{{ $type }}" data-autohide="4200">
                    <div class="toast-icon">
                        @switch($type)
                            @case('success') <i class="fa-solid fa-circle-check"></i> @break
                            @case('error')   <i class="fa-solid fa-circle-exclamation"></i> @break
                            @case('warning') <i class="fa-solid fa-triangle-exclamation"></i> @break
                            @default         <i class="fa-solid fa-circle-info"></i>
                        @endswitch
                    </div>
                    <div class="toast-content">
                        <strong>{{ $title }}</strong>
                        @if ($showBody)
                            <div class="toast-text">{{ $message }}</div>
                        @endif
                    </div>
                    <button class="toast-close" aria-label="Đóng"><i class="fa-solid fa-xmark"></i></button>
                </div>
            @endif
        @endforeach

        @if ($errors->any())
        <div class="toast-card is-error" data-autohide="6500">
            <div class="toast-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
            <div class="toast-content">
            <strong>Dữ liệu chưa hợp lệ</strong>
            <ul class="toast-errors">
                @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
            </div>
            <button class="toast-close" aria-label="Đóng"><i class="fa-solid fa-xmark"></i></button>
        </div>
        @endif
    </div>
@endif
