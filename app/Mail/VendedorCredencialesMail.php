<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendedorCredencialesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚀 Instrucciones de Activación de Tienda - Vendedor',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendedor', // 💡 Aquí cambiamos el 'view.name' por tu vista real
        );
    }
}