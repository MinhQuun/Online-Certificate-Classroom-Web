@extends('layouts.student')

@section('title', 'Thông tin cá nhân | Online Certificate Classroom')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-profile.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
<main class="profile-page">
    <div class="profile-container">
        <div class="profile-header">
            <h1>Thông tin cá nhân</h1>
            <p class="profile-subtitle">Quản lý thông tin tài khoản của bạn</p>
        </div>

        {{-- Hiển thị thông báo thành công --}}
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Thông tin cá nhân --}}
        <div class="profile-card">
            <div class="profile-card__header">
                <h2><i class="fa-solid fa-user"></i> Thông tin cá nhân</h2>
            </div>
            <div class="profile-card__body">
                <form action="{{ route('student.profile.update') }}" method="POST" class="profile-form">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name" class="form-label">
                            <i class="fa-solid fa-id-card"></i>
                            Họ và tên
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $user->hoTen ?? $user->name) }}"
                            placeholder="Nhập họ và tên của bạn"
                        >
                        @error('name')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fa-solid fa-envelope"></i>
                            Email
                        </label>
                        <input 
                            type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            id="email" 
                            name="email" 
                            value="{{ old('email', $user->email) }}"
                            placeholder="email@example.com"
                        >
                        @error('email')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">
                            <i class="fa-solid fa-phone"></i>
                            Số điện thoại
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('phone') is-invalid @enderror" 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone', $user->sdt ?? $user->phone) }}"
                            placeholder="Nhập số điện thoại"
                        >
                        @error('phone')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Cập nhật thông tin
                        </button>
                        <button type="button" class="btn btn-warning" data-modal-trigger="changePasswordModal">
                            <i class="fa-solid fa-key"></i>
                            Đổi mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

{{-- Modal đổi mật khẩu --}}
<div class="modal-overlay" id="changePasswordModal" data-modal>
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h3>
                    <i class="fa-solid fa-key"></i>
                    Đổi mật khẩu
                </h3>
                <button type="button" class="modal-close" data-modal-close>
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form action="{{ route('student.profile.changePassword') }}" method="POST" class="modal-form">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="current_password" class="form-label">
                            <i class="fa-solid fa-lock"></i>
                            Mật khẩu hiện tại
                        </label>
                        <input 
                            type="password" 
                            class="form-control @error('current_password') is-invalid @enderror" 
                            id="current_password" 
                            name="current_password"
                            placeholder="Nhập mật khẩu hiện tại"
                        >
                        @error('current_password')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password" class="form-label">
                            <i class="fa-solid fa-lock"></i>
                            Mật khẩu mới
                        </label>
                        <input 
                            type="password" 
                            class="form-control @error('new_password') is-invalid @enderror" 
                            id="new_password" 
                            name="new_password"
                            placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)"
                        >
                        @error('new_password')
                            <div class="invalid-feedback">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation" class="form-label">
                            <i class="fa-solid fa-lock"></i>
                            Xác nhận mật khẩu mới
                        </label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="new_password_confirmation" 
                            name="new_password_confirmation"
                            placeholder="Nhập lại mật khẩu mới"
                        >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>
                        <i class="fa-solid fa-xmark"></i>
                        Hủy
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fa-solid fa-check"></i>
                        Đổi mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @php
        $pageScript = 'js/Student/profile.js';
    @endphp
    <script src="{{ asset($pageScript) }}?v={{ student_asset_version($pageScript) }}"></script>
@endpush
