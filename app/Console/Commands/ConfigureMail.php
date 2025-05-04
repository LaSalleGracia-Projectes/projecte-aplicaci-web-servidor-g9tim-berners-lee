<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class ConfigureMail extends Command
{
    /**
     * El nombre y la firma del comando.
     *
     * @var string
     */
    protected $signature = 'mail:configure';

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Configura los ajustes de correo para Mailtrap';

    /**
     * Ejecuta el comando.
     */
    public function handle()
    {
        // Establecer la configuración de Mailtrap en tiempo de ejecución
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'sandbox.smtp.mailtrap.io');
        Config::set('mail.mailers.smtp.port', 2525);
        Config::set('mail.mailers.smtp.username', '06bb0b172a03ce');
        Config::set('mail.mailers.smtp.password', 'e44908a6dbd86b');
        Config::set('mail.mailers.smtp.encryption', 'tls');
        Config::set('mail.from.address', 'no-reply@critflix.com');
        Config::set('mail.from.name', 'CritFlix');

        $this->info('Configuración de correo actualizada correctamente.');
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
                ['from address', Config::get('mail.from.address')],
                ['from name', Config::get('mail.from.name')],
            ]
        );
    }
}
