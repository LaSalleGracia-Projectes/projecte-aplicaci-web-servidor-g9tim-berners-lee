<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class ConfigureMailgun extends Command
{
    /**
     * El nombre y la firma del comando.
     *
     * @var string
     */
    protected $signature = 'mail:configure-smtp {username?} {password?}';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Configura los ajustes de correo para SMTP (Gmail)';

    /**
     * Ejecuta el comando.
     */
    public function handle()
    {
        // Ya tenemos valores predeterminados, pero permitimos que el usuario los cambie si quiere
        $username = $this->argument('username') ?: 'critflixteam@gmail.com';
        $password = $this->argument('password') ?: 'iyja ghmq jifz cnso';

        // Establecer la configuración de SMTP en tiempo de ejecución
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
        Config::set('mail.mailers.smtp.port', 587);
        Config::set('mail.mailers.smtp.encryption', 'tls');
        Config::set('mail.mailers.smtp.username', $username);
        Config::set('mail.mailers.smtp.password', $password);
        Config::set('mail.from.address', $username);
        Config::set('mail.from.name', 'CritFlix');

        $this->info('Configuración de SMTP (Gmail) actualizada correctamente.');
        $this->info('Valores configurados:');
        $this->table(
            ['Configuración', 'Valor'],
            [
                ['driver', Config::get('mail.default')],
                ['host', Config::get('mail.mailers.smtp.host')],
                ['port', Config::get('mail.mailers.smtp.port')],
                ['encryption', Config::get('mail.mailers.smtp.encryption')],
                ['username', Config::get('mail.mailers.smtp.username')],
                ['password', '**********'],
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
