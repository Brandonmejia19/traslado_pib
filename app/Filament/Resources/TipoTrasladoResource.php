<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoTrasladoResource\Pages;
use App\Filament\Resources\TipoTrasladoResource\RelationManagers;
use App\Models\TipoTraslado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TipoTrasladoResource extends Resource
{
    protected static ?string $model = TipoTraslado::class;
    protected static ?string $navigationGroup = 'Desplegables';
    protected static ?string $label = 'Tipos de Traslados';
    protected static ?string $navigationIcon = 'healthicons-o-ambulance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('descripcion')
                    ->required()
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTipoTraslados::route('/'),
            'create' => Pages\CreateTipoTraslado::route('/create'),
            'edit' => Pages\EditTipoTraslado::route('/{record}/edit'),
        ];
    }
}
