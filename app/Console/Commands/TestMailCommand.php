<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test {email}';
    protected $description = 'Envía un correo de prueba';

    public function handle()
    {
        $email = $this->argument('email');

        $this->info('Iniciando prueba de correo...');
        Log::info('Iniciando prueba de correo para: ' . $email);

        try {
            Mail::raw('Este es un correo de prueba desde CritFlix', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Prueba de correo CritFlix');
            });

            $this->info('¡Correo enviado con éxito!');
            Log::info('Correo de prueba enviado exitosamente a: ' . $email);
        } catch (\Exception $e) {
            $this->error('Error al enviar el correo: ' . $e->getMessage());
            Log::error('Error en prueba de correo: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}
