<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivationCodeMail extends Mailable
{
    use SerializesModels;

    public $hocVienName;
    public $courseCodes; // mảng [ ['tenKH' => ..., 'code' => ...], ... ]

    public function __construct($hocVienName, $courseCodes)
    {
        $this->hocVienName = $hocVienName;
        $this->courseCodes = $courseCodes;
    }

    public function build()
    {
        return $this->subject('Mã kích hoạt khóa học của bạn')
                    ->view('emails.activation_code');
    }
}
