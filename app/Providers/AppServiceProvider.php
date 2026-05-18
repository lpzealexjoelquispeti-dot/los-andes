<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Tienda;
use App\Models\DisenoTienda;
use App\Models\Danza;
use App\Models\Traje;
use App\Models\ImagenTraje;
use App\Models\Tesauro;
use App\Models\EventoFolclorico;
use App\Models\InventarioUnidad;
use App\Models\Alquiler;
use App\Models\SancionAlquiler;
use App\Models\Valoracion;
use App\Models\Notificacion;
use App\Observers\AuditoriaObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Módulo 1: Seguridad y Acceso
        User::observe(AuditoriaObserver::class);

        // Módulo 2: Tiendas y Branding
        Tienda::observe(AuditoriaObserver::class);
        DisenoTienda::observe(AuditoriaObserver::class);

        // Módulo 3 y 4: Catálogo, Tesauro e Imágenes
        Danza::observe(AuditoriaObserver::class);
        Traje::observe(AuditoriaObserver::class);
        ImagenTraje::observe(AuditoriaObserver::class);
        Tesauro::observe(AuditoriaObserver::class);

        // Módulo 5: Logística y Transacciones
        EventoFolclorico::observe(AuditoriaObserver::class);
        InventarioUnidad::observe(AuditoriaObserver::class);
        Alquiler::observe(AuditoriaObserver::class);
        SancionAlquiler::observe(AuditoriaObserver::class);

        // Módulo 6: Feedback y Alertas
        Valoracion::observe(AuditoriaObserver::class);
        Notificacion::observe(AuditoriaObserver::class);
        
        // NOTA: 'Auditoria' se omite intencionalmente para evitar bucles infinitos.
        view()->composer('layouts.app', function ($view) {
    if (Auth::check()) {
        $view->with('tiendaActiva', Auth::user()->tiendas()->first());
    }
});
        }
    
}