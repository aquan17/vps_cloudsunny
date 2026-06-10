<?php

namespace App\Mail;

use App\Models\VpsInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VpsExpiring extends Mailable
{
    use Queueable, SerializesModels;

    public $vps;
    public $daysLeft;

    public function __construct(VpsInstance $vps, int $daysLeft)
    {
        $this->vps = $vps;
        $this->daysLeft = $daysLeft;
    }

    public function build()
    {
        return $this->subject('Cảnh báo: VPS ' . $this->vps->label . ' sắp hết hạn (' . $this->daysLeft . ' ngày nữa)')
                    ->view('emails.vps_expiring');
    }
}
