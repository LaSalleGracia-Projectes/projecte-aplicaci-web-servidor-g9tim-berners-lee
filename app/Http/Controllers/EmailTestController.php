<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmailTestController extends Controller
{
    /**
     * Envía un correo de prueba usando la configuración actual.
     */
    public function sendTestEmail(Request $request)
    {
        try {
            // Validar la dirección de correo
            $request->validate([
                'email' => 'required|email',
            ]);

            // Crear un usuario temporal para la prueba
            $testUser = new User();
            $testUser->name = 'Usuario de Prueba';
            $testUser->email = $request->email;

            // Enviar el correo
            Mail::to($request->email)->send(new WelcomeEmail($testUser));

            // Logging para depuración
            Log::info('Correo de prueba enviado a: ' . $request->email);

            // Respuesta para la interfaz
            return back()->with('success', 'Correo enviado correctamente a ' . $request->email);
        } catch (\Exception $e) {
            Log::error('Error enviando correo de prueba: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->with('error', 'Error al enviar el correo: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la vista para probar el envío de correos.
     */
    public function showTestForm()
    {
        // Mostrar información de configuración de correo para depuración
        $mailConfig = [
            'driver' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username') ? 'Configurado' : 'No configurado',
            'password' => config('mail.mailers.smtp.password') ? 'Configurado' : 'No configurado',
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        return view('admin.email-test', compact('mailConfig'));
    }
}
