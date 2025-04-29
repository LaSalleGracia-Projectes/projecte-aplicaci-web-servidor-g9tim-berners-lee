<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\WelcomeEmail;

class RegisterController extends Controller
{
    protected function create(array $data)
    {
        try {
            Log::info('=== INICIO DEL PROCESO DE REGISTRO ===');
            Log::info('Datos recibidos:', $data);
            Log::info('Configuración de correo:', [
                'MAIL_MAILER' => config('mail.default'),
                'MAIL_HOST' => config('mail.mailers.smtp.host'),
                'MAIL_PORT' => config('mail.mailers.smtp.port'),
                'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
                'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            Log::info('Usuario creado correctamente:', [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]);

            try {
                Log::info('=== INICIO DEL ENVÍO DE CORREO ===');
                Log::info('Preparando envío de correo a:', ['email' => $data['email']]);

                // Verificar que la clase WelcomeEmail existe
                if (!class_exists(WelcomeEmail::class)) {
                    Log::error('La clase WelcomeEmail no existe');
                    throw new \Exception('La clase WelcomeEmail no existe');
                }

                // Crear instancia del Mailable
                $welcomeEmail = new WelcomeEmail($user);
                Log::info('Instancia de WelcomeEmail creada');

                // Intentar enviar el correo
                Mail::to($data['email'])->send($welcomeEmail);
                Log::info('Correo enviado exitosamente a: ' . $data['email']);
            } catch (\Exception $e) {
                Log::error('=== ERROR EN EL ENVÍO DE CORREO ===');
                Log::error('Mensaje de error: ' . $e->getMessage());
                Log::error('Archivo: ' . $e->getFile());
                Log::error('Línea: ' . $e->getLine());
                Log::error('Stack trace completo:');
                Log::error($e->getTraceAsString());

                // Intentar enviar un correo de prueba simple
                try {
                    Log::info('Intentando enviar correo de prueba simple');
                    Mail::raw('Prueba de correo', function ($message) use ($data) {
                        $message->to($data['email'])
                                ->subject('Prueba de correo');
                    });
                    Log::info('Correo de prueba enviado exitosamente');
                } catch (\Exception $testError) {
                    Log::error('Error en correo de prueba: ' . $testError->getMessage());
                }
            }

            return $user;
        } catch (\Exception $e) {
            Log::error('=== ERROR EN EL REGISTRO DE USUARIO ===');
            Log::error('Mensaje de error: ' . $e->getMessage());
            Log::error('Archivo: ' . $e->getFile());
            Log::error('Línea: ' . $e->getLine());
            Log::error('Stack trace completo:');
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }
}
