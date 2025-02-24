<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ISSListadoResource\Pages;
use App\Filament\Resources\ISSListadoResource\RelationManagers;
use App\Models\ISSListado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ISSListadoResource extends Resource
{
    protected static ?string $model = ISSListado::class;
    protected static ?string $navigationGroup = 'Desplegables';
    protected static ?string $label = 'ISSS';
    protected static ?string $pluralModelLabel = 'ISSS';
    protected $fillable = ['nombre'];
    protected static ?string $navigationIcon = 'healthicons-o-rural-post';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListISSListados::route('/'),
            'create' => Pages\CreateISSListado::route('/create'),
            'edit' => Pages\EditISSListado::route('/{record}/edit'),
        ];
    }
}
