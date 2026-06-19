<?php

namespace App\Mail;

use App\Models\ProxyInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProxyExpiring extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $proxy;
    public $daysRemaining;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ProxyInstance $proxy, int $daysRemaining)
    {
        $this->proxy = $proxy;
        $this->daysRemaining = $daysRemaining;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Cảnh báo: Proxy #' . $this->proxy->id . ' sắp hết hạn!')
                    ->view('emails.proxy_expiring');
    }
}
