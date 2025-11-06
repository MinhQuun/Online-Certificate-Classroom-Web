<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivationCodeMail extends Mailable
{
    use SerializesModels;

    public $hocVienName;
    public $courseCodes;
    public $comboCodes;

    public function __construct($hocVienName, array $courseCodes, array $comboCodes = [])
    {
        $this->hocVienName = $hocVienName;
        $this->courseCodes = $courseCodes;
        $this->comboCodes = $comboCodes;
    }

    public function build()
    {
        return $this->subject('Ma kich hoat khoa hoc cua ban')
            ->view('emails.activation_code');
    }
}
