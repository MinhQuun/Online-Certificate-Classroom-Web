<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactReplyMail;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactReplyController extends Controller
{
    protected string $table = 'contact_replies';

    public function index(Request $req)
    {
        if (!DB::getSchemaBuilder()->hasTable($this->table)) {
            $items = new LengthAwarePaginator([], 0, 12);
            return view('Admin.contact-replies', [
                'items'  => $items,
                'badges' => ['new' => 0],
            ])->with('warning', 'Chưa có bảng contact_replies. Vui lòng tạo bảng trước.');
        }

        $q = trim((string) $req->get('q', ''));
        $status = strtoupper(trim((string) $req->get('status', '')));

        $query = DB::table($this->table);

        // Search: hỗ trợ nhiều từ (tên, email, nội dung)
        if ($q !== '') {
            $tokens = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($tokens as $t) {
                $like = '%' . $t . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('message', 'like', $like);
                });
            }
        }

        // Filter status
        $allowed = ['NEW', 'READ', 'REPLIED'];
        if ($status !== '' && $status !== 'ALL' && in_array($status, $allowed, true)) {
            $query->where('status', $status);
        }

        $items = $query->orderByDesc('id')->paginate(12)->withQueryString();

        $badges = [
            'new' => (int) DB::table($this->table)->where('status', 'NEW')->count(),
        ];

        return view('Admin.contact-replies', [
            'items'  => $items,
            'q'      => $q,
            'status' => $status,
            'badges' => $badges,
        ]);
    }

    public function update($id, Request $req)
    {
        if (!DB::getSchemaBuilder()->hasTable($this->table)) {
            return back()->with('error', 'Chưa có bảng contact_replies.');
        }

        $action = $req->input('action');

        if ($action === 'mark_read') {
            DB::table($this->table)->where('id', $id)->update([
                'status'     => 'READ',
                'updated_at' => Carbon::now(),
            ]);
            return back()->with('success', 'Đã đánh dấu: ĐÃ ĐỌC.');
        }

        if ($action === 'mark_unread') {
            DB::table($this->table)->where('id', $id)->update([
                'status'     => 'NEW',
                'updated_at' => Carbon::now(),
            ]);
            return back()->with('success', 'Đã đánh dấu: MỚI.');
        }

        if ($action === 'save_reply') {
            $msg = $req->validate([
                'reply_message' => ['nullable', 'string', 'max:5000'],
            ])['reply_message'] ?? null;

            DB::table($this->table)->where('id', $id)->update([
                'reply_message' => $msg,
                'status'        => $msg ? 'REPLIED' : DB::raw('status'),
                'replied_at'    => $msg ? Carbon::now() : null,
                'reply_by'      => $req->user()?->maND,
                'updated_at'    => Carbon::now(),
            ]);

            $it = DB::table($this->table)->where('id', $id)
                ->first(['name', 'email', 'message']);
            $sent = false;

            if ($msg && $it && filter_var($it->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($it->email)->send(
                        new ContactReplyMail(
                            name: $it->name ?? 'bạn',
                            originalMessage: $it->message ?? '',
                            replyMessage: $msg,
                            repliedAt: Carbon::now()
                        )
                    );
                    $sent = true;
                } catch (\Throwable $e) {
                    \Log::error('Failed to send contact reply email: ' . $e->getMessage());
                }
            }

            return back()->with(
                'success',
                $msg
                    ? ('Đã lưu phản hồi.' . ($sent ? ' Đã gửi email cho học viên.' : ' (Email chưa gửi được – kiểm tra cấu hình mail)'))
                    : 'Đã lưu.'
            );
        }

        return back()->with('info', 'Không có hành động nào được thực hiện.');
    }

    public function destroy($id)
    {
        if (!DB::getSchemaBuilder()->hasTable($this->table)) {
            return back()->with('error', 'Chưa có bảng contact_replies.');
        }
        DB::table($this->table)->where('id', $id)->delete();
        return back()->with('success', 'Đã xoá liên hệ.');
    }
}
