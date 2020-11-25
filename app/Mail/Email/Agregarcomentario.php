<?php

namespace App\Mail\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Agregarcomentario extends Mailable
{
    use Queueable, SerializesModels;
    public $hi;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($hi)
    {
        $this->hi = $hi;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('18090022@uttcampus.edu.mx')
        ->view('Agregarcomentario');
    }
}
