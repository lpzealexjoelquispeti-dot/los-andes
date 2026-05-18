<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; background-color: #f8fafc; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 24px; border: 1px solid #e2e8f0;">
        <h2 style="color: #007a3d; margin: 0; text-transform: uppercase;">Calle Los Andes</h2>
        <p style="color: #94a3b8; font-size: 10px; font-weight: bold; text-transform: uppercase;">Perfil: Cliente Catálogo</p>
        
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        <p>¡Ya eres parte de nuestra comunidad! Tus credenciales de acceso ya están listas:</p>
        
        <div style="background-color: #f1f5f9; padding: 15px; border-radius: 12px; margin: 20px 0; border-left: 5px solid #005c2e;">
            <p style="margin: 4px 0;"><strong>Usuario:</strong> {{ $user->email }}</p>
            <p style="margin: 4px 0;"><strong>Contraseña Temporal:</strong> <span style="color: #b45309; font-weight: bold;">{{ $password }}</span></p>
        </div>

        <div style="background-color: #f0f9ff; border: 1px solid #bae6fd; padding: 20px; border-radius: 16px; color: #0369a1;">
            <p style="margin: 0; font-size: 13px;">Ya tienes el acceso habilitado para explorar el catálogo de trajes folclóricos, verificar stock por tiendas y gestionar tus reservas para las festividades.</p>
        </div>
    </div>
</body>
</html>