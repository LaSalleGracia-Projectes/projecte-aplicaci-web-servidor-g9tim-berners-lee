<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WelcomeEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        try {
            Log::info('Construyendo correo de bienvenida para: ' . $this->user->email);
            return $this->subject('¡Bienvenido a CritFlix!')
                        ->view('emails.welcome')
                        ->with([
                            'name' => $this->user->name,
                            'email' => $this->user->email
                        ]);
        } catch (\Exception $e) {
            Log::error('Error al construir el correo de bienvenida: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }
}
