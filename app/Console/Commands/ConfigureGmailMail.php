<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class ConfigureGmailMail extends Command
{
    /**
     * El nombre y la firma del comando.
     *
     * @var string
     */
    protected $signature = 'mail:configure-gmail {email?} {password?}';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Configura los ajustes de correo para Gmail SMTP';

    /**
     * Ejecuta el comando.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: $this->ask('Introduce tu dirección de Gmail');
        $password = $this->argument('password') ?: $this->secret('Introduce tu contraseña de aplicación de Gmail');

        // Establecer la configuración de Gmail SMTP en tiempo de ejecución
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
        Config::set('mail.mailers.smtp.port', 587);
        Config::set('mail.mailers.smtp.username', $email);
        Config::set('mail.mailers.smtp.password', $password);
        Config::set('mail.mailers.smtp.encryption', 'tls');
        Config::set('mail.mailers.smtp.timeout', 30);
        Config::set('mail.from.address', $email);
        Config::set('mail.from.name', 'CritFlix');

        $this->info('Configuración de Gmail SMTP actualizada correctamente.');
        $this->info('Valores configurados:');
        $this->table(
            ['Configuración', 'Valor'],
            [
                ['driver', Config::get('mail.default')],
                ['host', Config::get('mail.mailers.smtp.host')],
                ['port', Config::get('mail.mailers.smtp.port')],
                ['username', Config::get('mail.mailers.smtp.username')],
                ['password', '***********'],
                ['encryption', Config::get('mail.mailers.smtp.encryption')],
                ['timeout', Config::get('mail.mailers.smtp.timeout')],
                ['from address', Config::get('mail.from.address')],
                ['from name', Config::get('mail.from.name')],
            ]
        );

        // Enviar un correo de prueba
        if ($this->confirm('¿Quieres enviar un correo de prueba?')) {
            $testEmail = $this->ask('Introduce una dirección de correo para la prueba');

            $this->call('mail:send-test', [
                'email' => $testEmail
            ]);
        }
    }
}
