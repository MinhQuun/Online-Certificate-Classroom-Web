<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang thông tin cá nhân
     */
    public function show()
    {
        $user = Auth::user();
        return view('Student.profile', compact('user'));
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:nguoidung,email,' . $user->maND . ',maND',
            'phone' => 'nullable|string|max:15',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'name.max' => 'Họ tên không được quá 255 ký tự.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.max' => 'Số điện thoại không được quá 15 ký tự.',
        ]);

        $user->update([
            'hoTen' => $validated['name'],
            'email' => $validated['email'],
            'sdt' => $validated['phone'] ?? null,
        ]);

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
        ]);

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($validated['current_password'], $user->matKhau)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.'])
                        ->withInput();
        }

        // Cập nhật mật khẩu mới
        $user->update([
            'matKhau' => Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
}
