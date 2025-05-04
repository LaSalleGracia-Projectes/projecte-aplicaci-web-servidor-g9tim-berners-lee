<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SendTestEmail extends Command
{
    /**
     * El nombre y la firma del comando.
     *
     * @var string
     */
    protected $signature = 'mail:send-test {email}';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Envía un correo electrónico de prueba';

    /**
     * Ejecuta el comando.
     */
    public function handle()
    {
        $email = $this->argument('email');

        try {
            // Crear un usuario ficticio para la prueba
            $testUser = new User();
            $testUser->name = 'Usuario de Prueba';
            $testUser->email = $email;

            $this->info("Enviando correo de prueba a {$email}...");

            // Enviar el correo
            Mail::to($email)->send(new WelcomeEmail($testUser));

            Log::info("Correo de prueba enviado a: {$email}");
            $this->info("✅ Correo enviado correctamente a {$email}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error("Error enviando correo de prueba: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            $this->error("❌ Error al enviar el correo: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
