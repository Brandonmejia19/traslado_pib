<?php

namespace Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Request;

/**
 * @property Form $form
 */
class Login extends SimplePage
{
    use InteractsWithFormActions;
    use WithRateLimiting;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::pages.auth.login';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();
        ///LOGICA DE AUTENTICACIÓN
        try {
            $user = \App\Models\User::where('user', $data['user'])->first();

            if (!$user) {
                $this->throwFailureValidationException();
            }

            $connection = new \LdapRecord\Connection([
                'hosts' => ['10.10.43.6'],
                'username' => $data['user'] . '@sem132.local',
                'password' => $data['password'],
            ]);

            if (!$connection->auth()->attempt($data['user'] . '@sem132.local', $data['password'])) {
                $this->throwFailureValidationException();
            }

            if (
                ($user instanceof FilamentUser) &&
                (!$user->canAccessPanel(Filament::getCurrentPanel()))
            ) {
                Filament::auth()->logout();
                $this->throwFailureValidationException();
            }

            // Captura la IP del usuario autenticado
            $ip = Request::ip();
            if (config('app.behind_cdn')) {
                $ip = Request::server(config('app.behind_cdn_http_header_field', 'HTTP_X_FORWARDED_FOR')) ?? $ip;
            }

            // Extraer el último segmento de la IP y determinar el rol
            $segments = explode('.', $ip);
            $lastSegment = end($segments);
            $lastSegment = end($segments); // Obtiene el último segmento

            // Obtiene los últimos 2 dígitos del segmento
            $lastTwoDigits = substr($lastSegment, -2);
            // Determinar el rol basado en la IP
            // Verificar si el usuario ya tiene el rol "Médico APH" o "Médico"
            if (!$user->hasRole(['Médico APH', 'Médico','Administrador'])) {
                if ($lastSegment == 36) {
                    $roleName = 'Administrador';
                } elseif ($lastSegment >= 221 && $lastSegment <= 222) {
                    $roleName = 'Médico APH';
                } elseif (($lastSegment >= 218 && $lastSegment <= 220) || ($lastSegment >= 224 && $lastSegment <= 226) || ($lastSegment >= 231 && $lastSegment <= 232)) {
                    $roleName = 'Gestor';
                } elseif (($lastSegment >= 215 && $lastSegment <= 217) || ($lastSegment == 223) || ($lastSegment >= 227 && $lastSegment <= 229) || ($lastSegment >= 164 && $lastSegment <= 163)) {
                    $roleName = 'Médico';
                } else {
                    $roleName = 'Operador';
                }

                // Buscar o crear el rol en la base de datos
                $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName]);

                // Asignar el rol al usuario (solo si no es Médico APH o Médico)
                $user->syncRoles([$role]);

                // Guardar los datos adicionales en el usuario
                $user->puesto = $lastTwoDigits;
                $user->cargo = $roleName;
                $user->save();
            }


            // Autenticar en Filament
            Filament::auth()->login($user, $data['remember'] ?? false);
            session()->regenerate();

            return app(LoginResponse::class);
        } catch (\LdapRecord\Auth\BindException $e) {
            $this->throwFailureValidationException();
        }
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.user' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getUserFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getUserFormComponent(): Component
    {
        return TextInput::make('user')
            ->label(__('Usuario'))
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/login.form.password.label'))
            ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label(__('filament-panels::pages/auth/login.form.remember.label'));
    }

    public function registerAction(): Action
    {
        return Action::make('register')
            ->link()
            ->label(__('filament-panels::pages/auth/login.actions.register.label'))
            ->url(filament()->getRegistrationUrl());
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament-panels::pages/auth/login.title');
    }

    public function getHeading(): string|Htmlable
    {
        return __('filament-panels::pages/auth/login.heading');
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
        ];
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(__('filament-panels::pages/auth/login.form.actions.authenticate.label'))
            ->submit('authenticate');
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'user' => $data['user'],
            'password' => $data['password'],
        ];
    }
}
