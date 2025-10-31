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

        // Lấy danh sách hóa đơn với các khóa học đã được kích hoạt
        $invoices = Invoice::with(['items.course', 'paymentMethod'])
            ->where('maHV', $student->maHV)
            ->orderBy('ngayLap', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Tính tổng tiền tất cả đơn hàng
        $totalAmount = Invoice::where('maHV', $student->maHV)->sum('tongTien');
        
        // Đếm số đơn hàng
        $totalOrders = Invoice::where('maHV', $student->maHV)->count();

        return view('Student.order-history', compact('invoices', 'totalAmount', 'totalOrders'));
    }
}
