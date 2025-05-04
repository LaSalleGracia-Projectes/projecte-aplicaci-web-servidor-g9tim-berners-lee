<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class WelcomeEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * El usuario al que enviamos el correo.
     *
     * @var User
     */
    public $user;

    /**
     * Crear una nueva instancia del mensaje.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        Log::info('WelcomeEmail instanciado para: ' . $user->email);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenido a CritFlix!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        Log::info('Construyendo email para: ' . $this->user->email);

        return new Content(
            view: 'emails.welcome',
            text: 'emails.welcome_plain',
            with: [
                'name' => $this->user->name,
                'user' => $this->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
