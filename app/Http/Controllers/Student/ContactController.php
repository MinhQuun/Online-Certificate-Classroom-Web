<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ContactController extends Controller
{
    protected string $table = 'CONTACT_REPLIES';

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:120',
            'email' => 'required|email:rfc,dns|max:255',
            'message' => 'required|string|min:10|max:5000',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.min' => 'Họ tên phải có ít nhất 2 ký tự.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'message.required' => 'Vui lòng nhập nội dung tin nhắn.',
            'message.min' => 'Nội dung tin nhắn phải có ít nhất 10 ký tự.',
            'message.max' => 'Nội dung tin nhắn không được quá 5000 ký tự.',
        ]);

        // Kiểm tra bảng tồn tại
        if (!DB::getSchemaBuilder()->hasTable($this->table)) {
            return back()->with('error', 'Hệ thống chưa sẵn sàng. Vui lòng thử lại sau.');
        }

        // Lưu vào database
        DB::table($this->table)->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'status' => 'NEW',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.');
    }
}
