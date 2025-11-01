<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    /**
     * Hiển thị lịch sử đơn hàng của học viên
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Lấy student record
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.courses.index')
                ->with('error', 'Bạn chưa có thông tin học viên.');
        }

        // Lấy danh sách hóa đơn với các khóa học và thông tin enrollment
        $invoices = Invoice::with([
                'items.course.category', 
                'items.course.teacher',
                'paymentMethod'
            ])
            ->where('maHV', $student->maHV)
            ->orderBy('ngayLap', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Load thông tin enrollment cho từng item để kiểm tra trạng thái
        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $item) {
                if ($item->course) {
                    $item->enrollment = \App\Models\Enrollment::where('maHV', $student->maHV)
                        ->where('maKH', $item->course->maKH)
                        ->first();
                }
            }
        }

        // Tính tổng tiền chỉ từ các khóa học đã được kích hoạt (ACTIVE)
        $totalAmount = 0;
        $activatedCoursesCount = 0;
        
        $allInvoices = Invoice::with('items.course')
            ->where('maHV', $student->maHV)
            ->get();
            
        foreach ($allInvoices as $invoice) {
            foreach ($invoice->items as $item) {
                if ($item->course) {
                    $enrollment = \App\Models\Enrollment::where('maHV', $student->maHV)
                        ->where('maKH', $item->course->maKH)
                        ->where('trangThai', 'ACTIVE')
                        ->first();
                    
                    if ($enrollment) {
                        $totalAmount += $item->donGia * ($item->soLuong ?? 1);
                        $activatedCoursesCount++;
                    }
                }
            }
        }
        
        // Đếm số đơn hàng
        $totalOrders = Invoice::where('maHV', $student->maHV)->count();

        return view('Student.order-history', compact('invoices', 'totalAmount', 'totalOrders', 'activatedCoursesCount'));
    }
}
