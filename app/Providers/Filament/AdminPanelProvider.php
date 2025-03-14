<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Resources\CuponResource;
use App\Filament\Resources\BosemResource;
use App\Filament\Resources\ElementosambResource;
use App\Filament\Resources\HerramientasambResource;
use App\Filament\Resources\AmbulanciaResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\ListaChequeoResource;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use Vormkracht10\TwoFactorAuth\TwoFactorAuthPlugin;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use Vormkracht10\TwoFactorAuth\Http\Livewire\Auth\Login;
use Rmsramos\Activitylog\ActivitylogPlugin;
use Tapp\FilamentAuthenticationLog\FilamentAuthenticationLogPlugin;
use Cmsmaxinc\FilamentErrorPages\FilamentErrorPagesPlugin;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->sidebarWidth('15rem')
            ->sidebarCollapsibleOnDesktop()
            ->id('admin')
            ->path('admin')
            ->login()
            ->databaseNotifications()
            //->darkMode(false)
            ->colors([
                'primary' => Color::hex('#206bc4'),
                'danger' => Color::Red,
                'gray' => Color::Zinc,
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'orange' => Color::Orange,
                'sidebar' => Color::hex('#fff'),
            ])
            ->plugins([
                FilamentBackgroundsPlugin::make()->imageProvider(
                    MyImages::make()
                        ->directory('images/backgrounds')
                ),
                /*FilamentEditProfilePlugin::make()
                    ->slug('mi-perfil')
                    ->setTitle('Mi Perfil')
                    ->setNavigationLabel('Perfil')
                    ->setIcon('heroicon-o-user')
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars', // image will be stored in 'storage/app/public/avatars
                        rules: 'mimes:jpeg,png|max:3024' //only accept jpeg and png files with a maximum size of 1MB
                    ),*/
                //  FilamentErrorPagesPlugin::make(),
                EasyFooterPlugin::make()
                ->withFooterPosition('footer')    ->withBorder(),


                FilamentAuthenticationLogPlugin::make(),
                FilamentSpatieRolesPermissionsPlugin::make(),
                ActivitylogPlugin::make()->navigationGroup('Mantenimiento')->label('Registro')
                    ->pluralLabel('Registros')
                /*->authorize(
                    fn() => auth()->user()->cargo === 'Administrador'
                ),*/

                /*       TwoFactorAuthPlugin::make()

                       ->forced(),*/
            ])
            ->brandLogo(asset('images/logo222.svg'))
            ->favicon(asset('images/logocheques.svg'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class
            ])
           // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
