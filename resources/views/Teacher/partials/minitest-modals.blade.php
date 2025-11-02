@php
    use App\Models\MiniTest;

    $skillOptions = [
        MiniTest::SKILL_LISTENING => 'Listening',
        MiniTest::SKILL_READING => 'Reading',
        MiniTest::SKILL_WRITING => 'Writing',
        MiniTest::SKILL_SPEAKING => 'Speaking',
    ];
@endphp

<!-- Create Mini-Test Modal -->
<div class="modal fade" id="createMiniTestModal" tabindex="-1" aria-labelledby="createMiniTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createMiniTestModalLabel">Tạo mini-test mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('teacher.minitests.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Khóa học <span class="text-danger">*</span></label>
                            <select name="course_id" class="form-select" required>
                                <option value="" disabled selected>Chọn khóa học</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->maKH }}"
                                            data-skill-default="{{ $course->default_skill_type ?? '' }}"
                                            data-skill-label="{{ $course->default_skill_type ? ($skillOptions[$course->default_skill_type] ?? $course->default_skill_type) : '' }}"
                                            @selected(optional($activeCourse)->maKH === $course->maKH)>
                                        {{ $course->tenKH }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Chương <span class="text-danger">*</span></label>
                            <select name="chapter_id" class="form-select" required>
                                <option value="" disabled selected>Chọn chương</option>
                                @foreach($courses as $course)
                                    @foreach($course->chapters as $chapter)
                                        <option value="{{ $chapter->maChuong }}"
                                                data-course="{{ $course->maKH }}"
                                                @selected(optional($activeCourse)->maKH === $course->maKH && $loop->first)>{{ $chapter->tenChuong }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kỹ năng <span class="text-danger">*</span></label>
                            <div data-skill-field>
                                <div class="form-control-plaintext px-3 py-2 border rounded d-none" data-skill-static></div>
                                <select name="skill_type" class="form-select" required data-skill-select>
                                    @foreach($skillOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Thứ tự</label>
                            <input type="number" name="order" class="form-control" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Điểm trọng số (%)</label>
                            <input type="number" name="weight" class="form-control" step="0.1" min="0" max="100" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Thời gian (phút)</label>
                            <input type="number" name="time_limit" class="form-control" min="0" value="30">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lượt làm</label>
                            <input type="number" name="attempts" class="form-control" min="1" value="1">
                        </div>                
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo mini-test</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Mini-Test Modal -->
<div class="modal fade" id="editMiniTestModal" tabindex="-1" aria-labelledby="editMiniTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMiniTestModalLabel">Chỉnh sửa mini-test</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMiniTestForm" method="POST" action="#">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Khóa học</label>
                            <select name="course_id" id="edit_course_id" class="form-select" required>
                                @foreach($courses as $course)
                                    <option value="{{ $course->maKH }}"
                                            data-skill-default="{{ $course->default_skill_type ?? '' }}"
                                            data-skill-label="{{ $course->default_skill_type ? ($skillOptions[$course->default_skill_type] ?? $course->default_skill_type) : '' }}">
                                        {{ $course->tenKH }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Chương</label>
                            <select name="chapter_id" id="edit_chapter_id" class="form-select" required>
                                @foreach($courses as $course)
                                    @foreach($course->chapters as $chapter)
                                        <option value="{{ $chapter->maChuong }}" data-course="{{ $course->maKH }}">{{ $chapter->tenChuong }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tiêu đề</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kỹ năng</label>
                            <div data-skill-field>
                                <div class="form-control-plaintext px-3 py-2 border rounded d-none" data-skill-static></div>
                                <select name="skill_type" id="edit_skill_type" class="form-select" required data-skill-select>
                                    @foreach($skillOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Thứ tự</label>
                            <input type="number" name="order" id="edit_order" class="form-control" min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Điểm trọng số (%)</label>
                            <input type="number" name="weight" id="edit_weight" class="form-control" step="0.1" min="0" max="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Thời gian (phút)</label>
                            <input type="number" name="time_limit" id="edit_time_limit" class="form-control" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lượt làm</label>
                            <input type="number" name="attempts" id="edit_attempts" class="form-control" min="1">
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                                <label class="form-check-label" for="edit_is_active">Kích hoạt</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Material Modal -->
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaterialModalLabel">Thêm tài liệu mini-test</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addMaterialForm" method="POST" action="#" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên tài liệu <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Loại tài liệu</label>
                            <select name="type" class="form-select" required>
                                <option value="pdf">PDF</option>
                                <option value="image">Hình ảnh</option>
                                <option value="audio">Audio</option>
                                <option value="zip">ZIP</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label d-block">Nguồn</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="source_type" id="source_file" value="file" checked>
                                <label class="form-check-label" for="source_file">Tải lên</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="source_type" id="source_url" value="url">
                                <label class="form-check-label" for="source_url">Liên kết</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="file_upload_section">
                        <label class="form-label">Chọn tệp <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control">
                        <small class="text-muted">Hỗ trợ PDF, hình ảnh (jpg/png), audio (mp3/wav), zip (tối đa 100MB).</small>
                    </div>
                    <div class="mb-3 d-none" id="url_input_section">
                        <label class="form-label">Đường dẫn <span class="text-danger">*</span></label>
                        <input type="url" name="url" class="form-control" placeholder="https://...">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Phạm vi hiển thị</label>
                        <select name="visibility" class="form-select">
                            <option value="public">Hiển thị cho học viên</option>
                            <option value="private">Chỉ giáo viên</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm tài liệu</button>
                </div>
            </form>
        </div>
    </div>
</div>

