@extends('layouts.admin')
@section('title', 'Quản lý người dùng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Admin/admin-users.css') }}">
@endpush

@section('content')
    <section class="page-header">
        <span class="kicker">Admin</span>
        <h1 class="title">Quản lý người dùng</h1>
        <p class="muted">Thêm, chỉnh sửa, xóa và phân quyền tài khoản.</p>
    </section>

    {{-- Bộ lọc --}}
    <div class="card users-filter mb-3">
        <div class="card-body">
            <form class="row g-2 align-items-center" method="get" action="{{ route('admin.users.index') }}">
                <div class="col-lg-5">
                    <input class="form-control"
                           name="q"
                           value="{{ $q ?? request('q') }}"
                           placeholder="Tìm theo tên, email, số điện thoại...">
                </div>
                <div class="col-lg-3">
                    <select name="role" class="form-select">
                        @php
                            $requestRole = (string) request('role');
                            $rf = $roleFilter ?? (\App\Support\RoleResolver::map(strtoupper($requestRole), $requestRole)
                                ?? \Illuminate\Support\Str::slug($requestRole));
                        @endphp
                        <option value="">— Tất cả quyền —</option>
                        @foreach($roles as $r)
                            @php
                                $code = $r->MAQUYEN ?? $r->maQuyen ?? '';
                                $name = $r->TENQUYEN ?? $r->tenQuyen ?? $code;
                                $slug = \App\Support\RoleResolver::map($code, $name)
                                    ?? \Illuminate\Support\Str::slug($name);
                                $val  = strtolower((string) $slug);
                            @endphp
                            <option value="{{ $val }}" {{ $rf === $val ? 'selected' : '' }}>
                                {{ $name }} ({{ $code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2 justify-content-lg-end">
                    <button class="btn btn-outline-primary">Lọc</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Xóa lọc</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Bảng danh sách --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="m-0">Danh sách người dùng</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
                <i class="bi bi-plus-circle me-1"></i> Thêm mới
            </button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover users-table table-fixed">
                <colgroup>
                    <col style="width:60px;">
                    <col style="width:16%;">
                    <col style="width:20%;">
                    <col style="width:12%;">
                    <col style="width:32%;">
                    <col style="width:12%;">
                </colgroup>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Quyền</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $u)
                    @php
                        $firstRole = optional($u->roles->first());
                        $roleId    = $firstRole->maQuyen ?? null;
                        $roleName  = $firstRole->tenQuyen ?? null;
                        $isAdmin   = ($adminId && $roleId === $adminId);
                        $isTeacher = ($teacherId && $roleId === $teacherId);
                        $isStudent = ($studentId && $roleId === $studentId);
                    @endphp
                    <tr>
                        <td>{{ $u->id }}</td>
                        <td class="text-truncate" title="{{ $u->name }}">{{ $u->name }}</td>
                        <td class="text-truncate" title="{{ $u->email }}">{{ $u->email }}</td>
                        <td class="text-truncate" title="{{ $u->phone }}">{{ $u->phone }}</td>
                        <td class="role-cell">
                            <form action="{{ route('admin.users.updateRole', $u) }}" method="post">
                                @csrf
                                <div class="role-wrap">
                                    <span class="badge rounded-pill {{ $isAdmin ? 'text-bg-danger' : ($isTeacher ? 'text-bg-primary' : 'text-bg-success') }}">
                                        {{ $roleName ?? 'Chưa gán' }}
                                    </span>

                                    <select name="MAQUYEN"
                                            class="form-select form-select-sm role-select"
                                            data-lock="{{ $isAdmin ? 'admin' : '' }}">
                                        @foreach($roles as $r)
                                            @php
                                                $optionCode = $r->MAQUYEN ?? $r->maQuyen ?? '';
                                                $optionName = $r->TENQUYEN ?? $r->tenQuyen ?? $optionCode;
                                            @endphp
                                            <option value="{{ $optionCode }}" {{ $roleId === $optionCode ? 'selected' : '' }}>
                                                {{ $optionName }} ({{ $optionCode }})
                                            </option>
                                        @endforeach
                                    </select>

                                    <button class="btn btn-sm btn-success-soft role-save">
                                        <i class="bi bi-check2-circle me-1"></i>Lưu
                                    </button>
                                </div>
                            </form>
                        </td>
                        <td class="td-actions text-end">
                            <button class="btn btn-sm btn-primary-soft action-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit"
                                    data-id="{{ $u->id }}"
                                    data-name="{{ $u->name }}"
                                    data-email="{{ $u->email }}"
                                    data-phone="{{ $u->phone }}"
                                    data-role-id="{{ $roleId }}"
                                    data-role-name="{{ $roleName }}"
                                    data-chuyen-mon="{{ $u->chuyenMon ?? '' }}"> <!-- Thêm thuộc tính này -->
                                <i class="bi bi-pencil me-1"></i>
                            </button>

                            <form action="{{ route('admin.users.destroy', $u) }}" method="post" class="d-inline form-delete">
                                @csrf @method('delete')
                                <button class="btn btn-sm btn-danger-soft action-btn"
                                        @if($isAdmin) disabled title="Không thể xóa tài khoản admin" @endif>
                                    <i class="bi bi-trash me-1"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Không có dữ liệu.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @include('components.pagination', [
            'paginator' => $users,
            'ariaLabel' => 'Điều hướng trang người dùng',
        ])
    </div>

    {{-- Modal: Thêm người dùng --}}
    <div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content" action="{{ route('admin.users.store') }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <label class="form-label">Họ tên</label>
                        <input name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input name="phone" class="form-control" value="{{ old('phone') }}" placeholder="0xxxxxxxxx" pattern="0\d{9}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation" class="form-control" minlength="6" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Quyền</label>
                        <select name="MAQUYEN" class="form-select" id="roleSelect" required data-teacher-role="{{ $roles->firstWhere('tenQuyen', 'Giảng viên')?->maQuyen ?? 'Q002' }}">
                            @foreach($roles as $r)
                                @php
                                    $code = $r->MAQUYEN ?? $r->maQuyen ?? '';
                                    $name = $r->TENQUYEN ?? $r->tenQuyen ?? $code;
                                @endphp
                                <option value="{{ $code }}">
                                    {{ $name }} ({{ $code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                        <!-- Thêm cột chuyên môn, chỉ hiển thị khi chọn giảng viên -->
                    <div class="col-md-6 chuyenMonField" style="display: none;">
                        <label class="form-label">Chuyên môn</label>
                        <input name="chuyenMon" class="form-control" value="{{ old('chuyenMon') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary">Lưu người dùng</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal: Sửa người dùng -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="formEdit" class="modal-content" action="{{ route('admin.users.update', $u) }}" method="post">
                @csrf
                @method('put')
                <div class="modal-header">
                    <h5 class="modal-title">Sửa người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <label class="form-label">Họ tên</label>
                        <input id="e_name" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input id="e_email" type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input id="e_phone" name="phone" class="form-control" placeholder="0xxxxxxxxx" pattern="0\d{9}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Đổi mật khẩu (tuỳ chọn)</label>
                        <input type="password" name="password" class="form-control" minlength="6" placeholder="Để trống nếu không đổi">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Xác nhận mật khẩu (tuỳ chọn)</label>
                        <input type="password" name="password_confirmation" class="form-control" minlength="6" placeholder="Để trống nếu không đổi">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Quyền</label>
                        <select id="e_role" name="MAQUYEN" class="form-select" required data-teacher-role="{{ $roles->firstWhere('tenQuyen', 'Giảng viên')?->maQuyen ?? 'Q002' }}">
                            @foreach($roles as $r)
                                @php
                                    $code = $r->MAQUYEN ?? $r->maQuyen ?? '';
                                    $name = $r->TENQUYEN ?? $r->tenQuyen ?? $code;
                                @endphp
                                <option value="{{ $code }}">
                                    {{ $name }} ({{ $code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Thêm cột chuyên môn, chỉ hiển thị khi chọn giảng viên -->
                    <div class="col-md-6 chuyenMonField" style="display: none;">
                        <label class="form-label">Chuyên môn</label>
                        <input name="chuyenMon" class="form-control" value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/Admin/admin-users.js') }}"></script>
@endpush
