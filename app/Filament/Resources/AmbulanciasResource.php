<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AmbulanciasResource\Pages;
use App\Filament\Resources\AmbulanciasResource\RelationManagers;
use App\Models\Ambulancias;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AmbulanciasResource extends Resource
{
    protected static ?string $model = Ambulancias::class;
    protected static ?string $navigationGroup = 'Desplegables';
    protected static ?string $label = 'Ambulancias';

    protected static ?string $navigationIcon = 'healthicons-o-ambulance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('unidad')
                    ->required()
                    ->maxLength(64),
                Forms\Components\TextInput::make('placa')
                    ->required()
                    ->maxLength(64),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unidad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('placa')
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
            'index' => Pages\ListAmbulancias::route('/'),
            'create' => Pages\CreateAmbulancias::route('/create'),
            'edit' => Pages\EditAmbulancias::route('/{record}/edit'),
        ];
    }
}
