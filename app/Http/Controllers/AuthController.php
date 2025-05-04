<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth; 
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    // ✅ REGISTRO
    public function register(Request $request)
    {
        try {
            // Validar los campos necesarios
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            // Crear el usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => 'usuario', // Asignar rol por defecto
            ]);

            $emailSent = false;
            $emailError = null;

            // Enviar el correo de bienvenida
            try {
                // Configurar el correo directamente
                $this->configureMailSettings();

                // Imprimir información sobre las configuraciones de correo
                Log::info('Configuración SMTP verificada antes de enviar', [
                    'mailer' => config('mail.default'),
                    'driver' => config('mail.default') === 'smtp' ? config('mail.mailers.smtp.transport') : 'N/A',
                    'host' => config('mail.default') === 'smtp' ? config('mail.mailers.smtp.host') : 'N/A',
                    'port' => config('mail.default') === 'smtp' ? config('mail.mailers.smtp.port') : 'N/A'
                ]);

                // Intentar enviar el correo
                Log::info('Iniciando envío de email de bienvenida a: ' . $user->email);
                Mail::to($user->email)->send(new WelcomeEmail($user));

                // Verificar si hubo errores (solo para SMTP, no para log)
                if (config('mail.default') !== 'log' && method_exists(Mail::class, 'failures') && count(Mail::failures()) > 0) {
                    throw new \Exception('Falló el envío del email: ' . json_encode(Mail::failures()));
                }

                // Verificar si estamos usando el driver de log
                if (config('mail.default') === 'log') {
                    Log::info('Email guardado en los logs (usando driver LOG) para: ' . $user->email);
                    $emailSent = true;
                    $emailError = 'Usando driver de logs - el correo no se envía realmente';
                } else {
                    Log::info('Email de bienvenida enviado correctamente a: ' . $user->email);
                    $emailSent = true;
                }
            } catch (\Swift_TransportException $e) {
                $emailError = 'Error de conexión SMTP: ' . $e->getMessage();
                Log::error('Error de conexión SMTP: ' . $e->getMessage());
                Log::error('Traza: ' . $e->getTraceAsString());
            } catch (\Exception $e) {
                $emailError = $e->getMessage();
                Log::error('Error enviando email: ' . $e->getMessage());
                Log::error('Traza: ' . $e->getTraceAsString());
                // Continuamos con el proceso aunque falle el email
            }

            // Para solicitudes AJAX, devolver respuesta JSON
            if ($request->expectsJson()) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'message' => 'Usuario registrado correctamente',
                    'email_sent' => $emailSent,
                    'email_error' => $emailError,
                    'token' => $token,
                    'user' => $user
                ], 201);
            }

            // Iniciar sesión automáticamente
            auth()->login($user);

            $successMessage = '¡Registro completado con éxito!';
            if (!$emailSent) {
                $successMessage .= ' Sin embargo, hubo un problema al enviar el correo de bienvenida. ' .
                                  'No te preocupes, puedes usar todas las funciones de la plataforma.';
            }

            // Para solicitudes de formulario, redirigir
            return redirect('/')->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Error en el registro: ' . $e->getMessage());
            Log::error('Traza: ' . $e->getTraceAsString());

            // Para solicitudes AJAX, devolver error JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Error en el registro',
                    'error' => $e->getMessage()
                ], 500);
            }

            // Para solicitudes de formulario, redirigir con errores
            return back()->withErrors([
                'error' => 'Ha ocurrido un error durante el registro. Por favor, inténtalo de nuevo.'
            ])->withInput($request->except(['password', 'password_confirmation']));
        }
    }

        // ✅ LOGIN
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            // Intentar autenticar al usuario
            if (!Auth::attempt($request->only('email', 'password'))) {
                // Si la autenticación falla
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Las credenciales no son correctas.'
                    ], 401);
                }

                return back()->withErrors([
                    'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
                ])->withInput($request->except('password'));
            }

            // Si la autenticación es exitosa
            $user = User::where('email', $request->email)->first();

            // Para solicitudes AJAX, devolver respuesta JSON
            if ($request->expectsJson()) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'message' => 'Inicio de sesión exitoso',
                    'token' => $token,
                    'user' => $user
                ], 200);
            }

            // Para solicitudes de formulario, redirigir
            return redirect()->intended('/');

            // Para solicitudes AJAX, devolver error JSON
            if ($request->expectsJson()) {
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales no son correctas.'],
                ]);
            }

            // Para solicitudes de formulario, redirigir con errores
            return back()->withErrors([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])->withInput($request->except('password'));
        } catch (\Exception $e) {
            // Para solicitudes AJAX, devolver error JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Error en el inicio de sesión',
                    'error' => $e->getMessage()
                ], 500);
            }

            // Para solicitudes de formulario, redirigir con errores
            return back()->withErrors([
                'email' => 'Ha ocurrido un error. Por favor, inténtalo de nuevo.',
            ])->withInput($request->except('password'));
        }
    }
    /**
     * Cierra la sesión del usuario
     */
    public function logout(Request $request)
    {
        // Si hay un usuario autenticado, eliminar sus tokens
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        // Cerrar sesión web si está disponible
        if (auth()->check()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Para solicitudes AJAX, devolver respuesta JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cierre de sesión exitoso'
            ]);
        }

        // Para solicitudes web, redirigir
        return redirect('/');
    }

    /**
     * Muestra el formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Muestra el formulario de registro
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Redirecciona al usuario a la página de autenticación de Google.
     */
    public function redirectToGoogle()
    {
        try {
            Log::info('Iniciando redirección a Google OAuth');

            // Verificar la configuración de Google
            $config = config('services.google');
            Log::info('Configuración Google: ', [
                'client_id' => $config['client_id'] ? 'Configurado' : 'No configurado',
                'client_secret' => $config['client_secret'] ? 'Configurado' : 'No configurado',
                'redirect' => $config['redirect']
            ]);

            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Error redireccionando a Google: ' . $e->getMessage());
            Log::error('Traza: ' . $e->getTraceAsString());

            return redirect()->route('register')->with('error', 'Ocurrió un error conectando con Google: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene la información del usuario de Google y lo autentica.
     */
    public function handleGoogleCallback()
    {
        try {
            Log::info('Recibiendo callback de Google OAuth');

            $googleUser = Socialite::driver('google')->user();
            Log::info('Usuario de Google obtenido', [
                'id' => $googleUser->id,
                'email' => $googleUser->email,
                'name' => $googleUser->name
            ]);

            // Buscar al usuario por su email o google_id
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            // Si no existe, crear nuevo usuario
            if (!$user) {
                Log::info('Creando nuevo usuario desde Google OAuth');

                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => Hash::make(Str::random(16)), // Contraseña aleatoria segura
                    'rol' => 'usuario',
                    'email_verified_at' => now(), // El email ya está verificado por Google
                ]);

                // Enviar correo de bienvenida
                try {
                    // Configurar el correo directamente
                    $this->configureMailSettings();

                    // Imprimir información sobre las configuraciones de correo
                    Log::info('Configuración SMTP verificada antes de enviar email de Google Auth', [
                        'mailer' => config('mail.default'),
                        'driver' => config('mail.mailers.smtp.transport'),
                        'host' => config('mail.mailers.smtp.host'),
                        'port' => config('mail.mailers.smtp.port')
                    ]);

                    Mail::to($user->email)->send(new WelcomeEmail($user));

                    // Verificar si hubo errores (solo para SMTP, no para log)
                    if (config('mail.default') !== 'log' && method_exists(Mail::class, 'failures') && count(Mail::failures()) > 0) {
                        throw new \Exception('Falló el envío del email: ' . json_encode(Mail::failures()));
                    }

                    // Verificar si estamos usando el driver de log
                    if (config('mail.default') === 'log') {
                        Log::info('Email guardado en los logs (usando driver LOG) para usuario de Google: ' . $user->email);
                    } else {
                        Log::info('Email de bienvenida enviado al nuevo usuario de Google');
                    }
                } catch (\Swift_TransportException $e) {
                    Log::error('Error de conexión SMTP en Google Auth: ' . $e->getMessage());
                    Log::error('Traza: ' . $e->getTraceAsString());
                    // Continuamos con el proceso aunque falle el email
                } catch (\Exception $e) {
                    Log::error('Error enviando email de bienvenida en Google Auth: ' . $e->getMessage());
                    Log::error('Traza: ' . $e->getTraceAsString());
                    // Continuamos con el proceso aunque falle el email
                }
            } else {
                Log::info('Usuario existente encontrado para Google OAuth', ['user_id' => $user->id]);

                if (!$user->google_id) {
                    // Si el usuario ya existe pero no tiene google_id, actualizamos sus datos
                    $user->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                    ]);
                    Log::info('Actualizado usuario existente con datos de Google');
                }
            }

            // Iniciar sesión
            auth()->login($user);
            Log::info('Inicio de sesión exitoso con Google', ['user_id' => $user->id]);

            return redirect('/')->with('success', '¡Inicio de sesión con Google exitoso!');
        } catch (\Exception $e) {
            Log::error('Error en la autenticación con Google: ' . $e->getMessage());
            Log::error('Traza: ' . $e->getTraceAsString());

            return redirect()->route('login')->with('error', 'Ocurrió un error durante la autenticación con Google: ' . $e->getMessage());
        }
    }

    /**
     * Configura las opciones de correo en tiempo de ejecución
     * Probamos con Mailtrap como alternativa a Gmail
     */
    private function configureMailSettings()
    {
        // Decidir qué proveedor de correo usar (Gmail, Mailtrap o Log)
        $mailMethod = 'gmail'; // Alternativas: 'gmail', 'mailtrap', 'log'

        if ($mailMethod === 'mailtrap') {
            // Opciones de Mailtrap actualizadas
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp' => [
                    'transport' => 'smtp',
                    'host' => 'sandbox.smtp.mailtrap.io',
                    'port' => 2525,
                    'encryption' => 'tls',
                    'username' => 'a3e84de5ef7ce5', // Credenciales actualizadas
                    'password' => 'e48b2852ca5acb', // Credenciales actualizadas
                    'timeout' => 60,
                    'auth_mode' => null,
                ],
                'mail.from' => [
                    'address' => 'critflix@example.com',
                    'name' => 'CritFlix'
                ]
            ]);

            Log::info('Configuración de SMTP (Mailtrap) establecida para pruebas');
        } else if ($mailMethod === 'gmail') {
            // Credenciales de Gmail
            $username = 'critflixteam@gmail.com';
            $password = 'iyja ghmq jifz cnso'; // App password de Gmail

            // Configurar SMTP (Gmail) para envío de correos
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp' => [
                    'transport' => 'smtp',
                    'host' => 'smtp.gmail.com',
                    'port' => 587,
                    'encryption' => 'tls',
                    'username' => $username,
                    'password' => $password,
                    'timeout' => 60,
                    'auth_mode' => null,
                    'verify_peer' => false,
                    'local_domain' => env('MAIL_EHLO_DOMAIN', 'critflix.com'),
                ],
                'mail.from' => [
                    'address' => $username,
                    'name' => 'CritFlix'
                ]
            ]);

            Log::info('Configuración de SMTP (Gmail) establecida');
        } else {
            // Método de registro (log) - No envía realmente los correos, solo los registra
            config([
                'mail.default' => 'log',
                'mail.mailers.log' => [
                    'transport' => 'log',
                    'channel' => 'stack',
                ],
                'mail.from' => [
                    'address' => 'critflix@example.com',
                    'name' => 'CritFlix'
                ]
            ]);

            Log::info('Configuración de correo (LOG) establecida - los correos se registrarán pero no se enviarán');
        }

        // Verificar que la configuración se aplicó correctamente
        $configInfo = [
            'driver' => config('mail.default')
        ];

        // Añadir detalles específicos según el driver
        if (config('mail.default') === 'smtp') {
            $configInfo = array_merge($configInfo, [
                'transport' => config('mail.mailers.smtp.transport'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'username' => config('mail.mailers.smtp.username'),
            ]);
        } elseif (config('mail.default') === 'log') {
            $configInfo = array_merge($configInfo, [
                'transport' => 'log',
                'channel' => config('mail.mailers.log.channel', 'stack')
            ]);
        }

        // Siempre incluir información del remitente
        $configInfo = array_merge($configInfo, [
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name')
        ]);

        Log::info('Verificando configuración de correo:', $configInfo);
    }

    /**
     * Método para probar el envío de correo directamente
     */
    public function testEmail($email)
    {
        try {
            // Validar que sea un email válido
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('El email proporcionado no es válido');
            }

            $name = "Usuario de Prueba";

            // Configurar el correo
            $this->configureMailSettings();

            // Registrar configuración para depuración
            $mailConfig = [
                'driver' => config('mail.default'),
                'host' => config('mail.default') === 'smtp' ? config('mail.mailers.smtp.host') : 'N/A',
                'port' => config('mail.default') === 'smtp' ? config('mail.mailers.smtp.port') : 'N/A',
                'username' => config('mail.default') === 'smtp' ? config('mail.mailers.smtp.username') : 'N/A',
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name')
            ];
            Log::info('Configuración de correo para prueba:', $mailConfig);

            // Crear un usuario ficticio para la prueba
            $testUser = new User([
                'name' => $name,
                'email' => $email,
            ]);

            // Registrar la intención de envío
            Log::info('Intentando enviar correo de prueba a: ' . $email);

            // Intentar enviar el correo
            Mail::to($email)->send(new WelcomeEmail($testUser));

            // Verificar si hubo errores (solo para SMTP, no para log)
            if (config('mail.default') !== 'log' && method_exists(Mail::class, 'failures') && count(Mail::failures()) > 0) {
                throw new \Exception('Falló el envío del email: ' . json_encode(Mail::failures()));
            }

            // Comprobar si estamos usando el driver de log
            $usingLogDriver = config('mail.default') === 'log';
            $message = $usingLogDriver
                ? 'El correo se ha registrado correctamente en los logs (no se envió realmente)'
                : 'Correo de prueba enviado correctamente a ' . $email;

            // Respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => $message,
                'mail_config' => $mailConfig,
                'using_log_driver' => $usingLogDriver
            ]);
        } catch (\Exception $e) {
            // Registrar el error
            Log::error('Error en prueba de correo: ' . $e->getMessage());
            Log::error('Traza: ' . $e->getTraceAsString());

            // Obtener configuración actual para diagnóstico
            $mailConfig = [
                'driver' => config('mail.default'),
                'host' => config('mail.default') === 'smtp' ? config('mail.mailers.smtp.host') : 'N/A',
                'port' => config('mail.default') === 'smtp' ? config('mail.mailers.smtp.port') : 'N/A',
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name')
            ];

            // Devolver respuesta de error
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar correo de prueba: ' . $e->getMessage(),
                'mail_config' => $mailConfig
            ], 500);
        }
    }
}
