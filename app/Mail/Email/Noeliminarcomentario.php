<?php

namespace App\Mail\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Noeliminarcomentario extends Mailable
{
    use Queueable, SerializesModels;
    public $usuario;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('18090022@uttcampus.edu.mx')
        ->view('Noeliminarcomentario');
    }
}
