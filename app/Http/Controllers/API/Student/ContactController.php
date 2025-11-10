<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    protected string $table = 'contact_replies';

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'min:2', 'max:120'],
            'email'   => ['required', 'email:rfc,dns', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        if (! DB::getSchemaBuilder()->hasTable($this->table)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hệ thống tạm thời chưa sẵn sàng xử lý liên hệ.',
            ], 503);
        }

        DB::table($this->table)->insert([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'message'    => $validated['message'],
            'status'     => 'NEW',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã gửi yêu cầu liên hệ, chúng tôi sẽ phản hồi sớm nhất.',
        ], 201);
    }
}
