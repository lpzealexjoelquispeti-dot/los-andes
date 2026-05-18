<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; background-color: #f8fafc; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 24px; border: 1px solid #e2e8f0;">
        <h2 style="color: #ef4444; margin: 0; text-transform: uppercase;">Calle Los Andes</h2>
        <p style="color: #94a3b8; font-size: 10px; font-weight: bold; text-transform: uppercase;">Perfil: Administrador de Sistema</p>
        
        <p>Se ha generado un nuevo perfil con privilegios **Administrativos** a tu nombre:</p>
        
        <div style="background-color: #f1f5f9; padding: 15px; border-radius: 12px; margin: 20px 0; border-left: 5px solid #ef4444;">
            <p style="margin: 4px 0;"><strong>Usuario:</strong> {{ $user->email }}</p>
            <p style="margin: 4px 0;"><strong>Contraseña Temporal:</strong> <span style="color: #b45309; font-weight: bold;">{{ $password }}</span></p>
        </div>

        <div style="background-color: #fff1f2; border: 1px solid #fecdd3; padding: 20px; border-radius: 16px; color: #9f1239; font-size: 13px;">
            <strong>Aviso de Seguridad:</strong> Todas las acciones ejecutadas bajo este usuario (bajas, aprobaciones y modificaciones) serán rastreadas de forma estricta por el sistema central de auditorías.
        </div>
    </div>
</body>
</html>