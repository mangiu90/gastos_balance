<?php

namespace App\Mail;

use App\Helpers\Exportar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Resumen extends Mailable
{
    use Queueable, SerializesModels;

    public $eventos;
    public $movimientos;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($eventos, $headerData, $arrayData)
    {
        $this->eventos = $eventos;
        $this->movimientos = Exportar::xlsx_attach($headerData, $arrayData);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.resumen')
        ->attachData($this->movimientos, 'movimientos.xlsx', [
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
