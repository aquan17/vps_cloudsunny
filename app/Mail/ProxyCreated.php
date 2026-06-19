<?php

namespace App\Mail;

use App\Models\ProxyInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProxyCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $proxy;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ProxyInstance $proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Proxy #' . $this->proxy->id . ' đã được cấp phát thành công!')
                    ->view('emails.proxy_created');
    }
}
