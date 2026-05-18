<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contraseña Restablecida</title>
</head>
<body style="font-family: sans-serif; background-color: #f8fafc; color: #334155; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e2e8f0; padding: 40px; border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        
        {{-- Encabezado de Marca --}}
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="color: #007a3d; text-transform: uppercase; letter-spacing: -0.05em; font-style: italic; font-size: 28px; font-weight: 900; margin: 0;">
                Calle Los Andes
            </h2>
            <p style="color: #94a3b8; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3em; margin-top: 5px; margin-bottom: 0;">
                Seguridad y Control de Accesos
            </p>
        </div>

        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            Hola <strong style="color: #1e293b;">{{ $user->name }}</strong>,
        </p>
        
        {{-- Banner de Advertencia de Seguridad --}}
        <div style="background-color: #fffbeb; border: 1px solid #fef3c7; pading: 15px; padding: 15px 20px; border-radius: 16px; margin: 20px 0; color: #b45309; font-size: 13px; line-height: 1.5;">
            <strong>⚠️ Notificación de Seguridad:</strong> Te informamos que tu contraseña ha sido restablecida por el Administrador General del sistema. Tus credenciales anteriores han sido revocadas e invalidadas inmediatamente por motivos de resguardo.
        </div>

        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            A partir de este momento, tus nuevos datos de autenticación temporales son los siguientes:
        </p>
        
        {{-- Bloque de Credenciales --}}
        <div style="background-color: #f1f5f9; padding: 20px; border-radius: 16px; margin: 25px 0; border-left: 5px solid #1e293b;">
            <p style="margin: 0 0 8px 0; font-size: 13px; color: #475569;"><strong>Cuenta de Correo:</strong> {{ $user->email }}</p>
            <p style="margin: 0; font-size: 13px; color: #475569;"><strong>Nueva Clave Temporal:</strong> <span style="font-family: monospace; color: #b45309; font-size: 15px; font-weight: bold; background-color: #fef3c7; padding: 2px 6px; border-radius: 6px;">{{ $password }}</span></p>
        </div>

        {{-- Recordatorio rápido de flujos según Rol --}}
        <h4 style="color: #1e293b; font-size: 13px; margin-[20px]: 0 0 8px 0; text-transform: uppercase; tracking: 0.05em;">🔄 ¿Qué debes hacer ahora?</h4>
        <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #475569; line-height: 1.7;">
            <li>Ingresa a la plataforma usando tu clave temporal.</li>
            <li>Ve directo a las opciones de tu perfil y **actualiza la contraseña** por una combinación personal y segura.</li>
            @if($user->hasRole('vendedor'))
                <li style="color: #007a3d; font-weight: bold;">Podrás continuar con la carga de evidencias físicas y gestión de tu tienda una vez reestablecido tu acceso.</li>
            @endif
        </ul>
        
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        <p style="text-align: center; font-size: 11px; color: #94a3b8; margin: 0;">
            Este es un correo automático de control de identidad. No lo respondas. <br>
            Módulo de Integridad - Proyecto Integrador 2026
        </p>
    </div>
</body>
</html>