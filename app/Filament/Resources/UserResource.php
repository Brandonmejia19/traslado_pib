<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Tapp\FilamentAuthenticationLog\RelationManagers\AuthenticationLogsRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Seguridad';
    protected static ?string $label = 'Usuarios';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Personal')
                    ->description('Información personal del usuario.')
                    ->icon('heroicon-o-user')
                    ->schema(
                        [
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('user')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('roles')
                                ->relationship(name: 'roles', titleAttribute: 'name')
                                ->searchable(),
                            Forms\Components\Select::make('cargo')
                                ->options([
                                    'Administrador' => 'Administrador',
                                    'Tecnico Flota' => 'Tecnico Flota',
                                    'Operador' => 'Operador',
                                    'Médico' => 'Médico',
                                    'Gestor' => 'Gestor',
                                    'APH' => 'APH',
                                ]),
                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            /* Forms\Components\TextInput::make('password')
                                 ->password()
                                 ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                                 ->maxLength(255),*/
                        ],
                    )->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user')
                    ->sortable()
                    ->placeholder('Vacío')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->placeholder('Vacío')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->placeholder('Vacío')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cargo')
                    ->placeholder('Vacío')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Creado')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Actualizado')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            AuthenticationLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
