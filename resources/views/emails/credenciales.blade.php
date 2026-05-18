<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bienvenido al Sistema - Calle Los Andes</title>
</head>
<body style="font-family: sans-serif; background-color: #f8fafc; color: #334155; padding: 20px; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e2e8f0; padding: 40px; border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        
        {{-- Logotipo / Encabezado --}}
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="color: #007a3d; text-transform: uppercase; letter-spacing: -0.05em; font-style: italic; font-size: 28px; font-weight: 900; margin: 0;">
                Calle Los Andes
            </h2>
            <p style="color: #94a3b8; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3em; margin-top: 5px; margin-bottom: 0;">
                Gestión de Accesos e Identidad
            </p>
        </div>

        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            Hola <strong style="color: #1e293b;">{{ $user->name }}</strong>,
        </p>
        <p style="font-size: 14px; line-height: 1.6; color: #475569;">
            Tu cuenta ha sido registrada con éxito en la plataforma. A continuación, te proporcionamos tus credenciales de acceso de forma segura:
        </p>
        
        {{-- Tarjeta de Credenciales --}}
        <div style="background-color: #f1f5f9; padding: 20px; border-radius: 16px; margin: 25px 0; border-left: 5px solid #007a3d;">
            <p style="margin: 0 0 8px 0; font-size: 13px; color: #475569;"><strong>Correo Electrónico:</strong> {{ $user->email }}</p>
            <p style="margin: 0; font-size: 13px; color: #475569;"><strong>Contraseña Temporal:</strong> <span style="font-family: monospace; color: #b45309; font-size: 15px; font-weight: bold; background-color: #fef3c7; padding: 2px 6px; border-radius: 6px;">{{ $password }}</span></p>
        </div>

        {{-- ========================================================================= --}}
        {{-- LÓGICA DINÁMICA POR ROLES (SPATIE) --}}
        {{-- ========================================================================= --}}
        
        @if($user->hasRole('vendedor'))
            {{-- INSTRUCCIONES ESPECÍFICAS PARA EL VENDEDOR --}}
            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 25px; border-radius: 20px; margin-top: 30px;">
                <h4 style="margin: 0 0 12px 0; color: #166534; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">
                    🚀 Próximos pasos para activar tu Tienda:
                </h4>
                <p style="margin: 0 0 15px 0; font-size: 13px; color: #166534; line-height: 1.5;">
                    Registraste tu perfil como <strong>Vendedor</strong>. Para poder comercializar tus trajes folclóricos, primero debemos verificar tu identidad y negocio siguiendo estos pasos obligatorios:
                </p>
                <ol style="margin: 0; padding-left: 20px; font-size: 13px; color: #1e293b; line-height: 1.8;">
                    <li>Inicia sesión en la plataforma utilizando el correo y la contraseña temporal descritos arriba.</li>
                    <li>Una vez dentro, dirígete a tu panel de configuración e ingresa al formulario de <strong>Solicitud de Tienda</strong>.</li>
                    <li>Llena los datos comerciales requeridos y **sube las fotografías y evidencias físicas** de tus trajes o tienda para la aprobación del administrador.</li>
                </ol>
                <p style="margin: 15px 0 0 0; font-size: 12px; color: #16a34a; font-style: italic; font-weight: 500;">
                    * Una vez enviada la evidencia, el Administrador evaluará tu solicitud y se te notificará la activación de tu vitrina 3D.
                </p>
            </div>
        @elseif($user->hasRole('admin'))
            {{-- INSTRUCCIONES PARA EL ADMINISTRADOR --}}
            <div style="background-color: #fff1f2; border: 1px solid #fecdd3; padding: 25px; border-radius: 20px; margin-top: 30px;">
                <h4 style="margin: 0 0 8px 0; color: #9f1239; font-size: 14px; font-weight: 800; text-transform: uppercase;">
                    🛡️ Acceso de Nivel Administrativo:
                </h4>
                <p style="margin: 0; font-size: 13px; color: #4c0519; line-height: 1.5;">
                    Tienes asignados privilegios de **Administrador General**. Recuerda que todas tus operaciones dentro del panel administrativo (creaciones, modificaciones, bajas lógicas y restauraciones) están siendo monitoreadas por el sistema de auditoría transaccional.
                </p>
            </div>
        @else
            {{-- INSTRUCCIONES PARA CLIENTE COMÚN --}}
            <div style="background-color: #f0f9ff; border: 1px solid #bae6fd; padding: 25px; border-radius: 20px; margin-top: 30px;">
                <h4 style="margin: 0 0 8px 0; color: #075985; font-size: 14px; font-weight: 800; text-transform: uppercase;">
                    🎉 ¡Ya puedes explorar el catálogo!
                </h4>
                <p style="margin: 0; font-size: 13px; color: #0369a1; line-height: 1.5;">
                    Tu cuenta como **Cliente** está activa. Ya puedes ingresar al sistema para visualizar los modelos fotogramétricos en 3D, gestionar tus reservas y realizar el alquiler de tus trajes para las próximas entradas folclóricas.
                </p>
            </div>
        @endif

        {{-- ========================================================================= --}}
        
        <p style="font-size: 12px; color: #94a3b8; margin-top: 30px; text-align: center; line-height: 1.5;">
            Por motivos de seguridad, te recomendamos cambiar esta contraseña por una de tu preferencia desde tu perfil de usuario una vez que ingreses por primera vez.
        </p>
        
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        <p style="text-align: center; font-size: 11px; color: #94a3b8; margin: 0;">
            Módulo de Seguridad y Accesos - Proyecto Integrador 2026
        </p>
    </div>
</body>
</html>