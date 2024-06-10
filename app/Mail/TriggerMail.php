<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TriggerMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $val, $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($val, $type)
    {
        $this->val = $val;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(env("MAIL_FROM_ADDRESS"), env("MAIL_FROM_NAME"))
            ->view('mail.contact', [
                "val" => $this->val,
                "type" => $this->type,
            ]);
    }
}
