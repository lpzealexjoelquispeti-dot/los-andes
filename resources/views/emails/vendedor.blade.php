<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; background-color: #f8fafc; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 24px; border: 1px solid #e2e8f0;">
        <h2 style="color: #007a3d; margin: 0; text-transform: uppercase; italic">Calle Los Andes</h2>
        <p style="color: #94a3b8; font-size: 10px; font-weight: bold; text-transform: uppercase; tracking: 0.2em;">Perfil: Vendedor Comercial</p>
        
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        <p>Tu cuenta como **Vendedor** ha sido dada de alta. Para activar tu escaparate y vender tus trajes, debes enviar tu solicitud de validación:</p>
        
        <div style="background-color: #f1f5f9; padding: 15px; border-radius: 12px; margin: 20px 0; border-left: 5px solid #007a3d;">
            <p style="margin: 4px 0;"><strong>Usuario:</strong> {{ $user->email }}</p>
            <p style="margin: 4px 0;"><strong>Contraseña Temporal:</strong> <span style="color: #b45309; font-weight: bold;">{{ $password }}</span></p>
        </div>

        <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 20px; border-radius: 16px;">
            <h4 style="margin: 0 0 10px 0; color: #166534;">📋 Pasos obligatorios para la Solicitud:</h4>
            <ol style="margin: 0; padding-left: 20px; font-size: 13px; color: #1e293b; line-height: 1.6;">
                <li>Inicia sesión con las credenciales temporales de arriba.</li>
                <li>Ingresa al módulo **"Solicitud de Tienda"** en tu panel secundario.</li>
                <li>Completa el formulario de datos y **sube las fotos de tu tienda y evidencias físicas** para que el Administrador evalúe tu caso.</li>
            </ol>
        </div>
    </div>
</body>
</html>