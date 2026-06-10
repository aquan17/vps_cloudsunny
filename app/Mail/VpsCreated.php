<?php

namespace App\Mail;

use App\Models\VpsInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VpsCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $vps;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(VpsInstance $vps, string $password)
    {
        $this->vps = $vps;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('VPS ' . $this->vps->label . ' đã khởi tạo thành công!')
                    ->view('emails.vps_created');
    }
}
