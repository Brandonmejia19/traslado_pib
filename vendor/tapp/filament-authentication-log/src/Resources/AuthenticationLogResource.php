<?php

namespace Tapp\FilamentAuthenticationLog\Resources;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;
use Tapp\FilamentAuthenticationLog\Resources\AuthenticationLogResource\Pages;

class AuthenticationLogResource extends Resource
{
    protected static ?string $model = AuthenticationLog::class;

    public static function shouldRegisterNavigation(): bool
    {
        return config('filament-authentication-log.navigation.authentication-log.register', true);
    }

    public static function getNavigationIcon(): string
    {
        return config('filament-authentication-log.navigation.authentication-log.icon');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-authentication-log.navigation.authentication-log.sort');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Seguridad');
    }

    public static function getLabel(): string
    {
        return __('Registros de Autenticación');
    }

    public static function getPluralLabel(): string
    {
        return __('Registros de Autenticación');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\MorphToSelect::make('authenticable')
                    ->types(self::authenticableResources())
                    ->required(),
                Forms\Components\TextInput::make('Ip Address'),
                Forms\Components\TextInput::make('User Agent'),
                Forms\Components\DateTimePicker::make('Login At'),
                Forms\Components\Toggle::make('Login Successful'),
                Forms\Components\DateTimePicker::make('Logout At'),
                Forms\Components\Toggle::make('Cleared By User'),
                Forms\Components\KeyValue::make('Location'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('authenticatable')
                    ->label('Usuario')
                   /* ->formatStateUsing(function ($state, $record) {
                        return $record->authenticatable?->name ?? 'Sin usuario';
                    })*/
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->authenticatable) {
                            return 'Sin usuario';
                        }

                        $url = route('filament.' . Filament::getCurrentPanel()->getId() . '.resources.users.edit', [
                            'record' => $record->authenticatable_id
                        ]);

                        return new HtmlString('<a href="' . $url . '" class="text-primary-600 hover:underline">' . $record->authenticatable->name . '</a>');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Dirección IP')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label(trans('filament-authentication-log::filament-authentication-log.column.user_agent'))
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),
                Tables\Columns\TextColumn::make('login_at')
                    ->dateTime()
                    ->placeholder('N/A')
                    ->label('Hora de Ingreso')
                    ->sortable(),
                Tables\Columns\IconColumn::make('login_successful')
                    ->label('Ingreso Exitoso')
                    ->boolean()
                    ->placeholder('N/A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('logout_at')
                    ->label('Salida')
                    ->dateTime()
                    ->placeholder('N/A')
                    ->sortable(),

                //Tables\Columns\TextColumn::make('location'),
            ])
            ->actions([
                //
            ])
            ->filters([
                Filter::make('login_successful')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('login_successful', true)),
                Filter::make('login_at')
                    ->form([
                        DatePicker::make('login_from'),
                        DatePicker::make('login_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['login_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('login_at', '>=', $date),
                            )
                            ->when(
                                $data['login_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('login_at', '<=', $date),
                            );
                    }),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuthenticationLogs::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            //
        ];
    }

    public static function authenticableResources(): array
    {
        return config('filament-authentication-log.authenticable-resources', [
            \App\Models\User::class,
        ]);
    }
}
