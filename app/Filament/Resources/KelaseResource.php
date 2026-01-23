<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelaseResource\Pages;
use App\Filament\Resources\KelaseResource\RelationManagers;
use App\Models\Kelase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class KelaseResource extends Resource
{
    protected static ?string $model = Kelase::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Kelas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kelas')
                    ->label('Kelas')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),

                Forms\Components\Hidden::make('slug'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas')
                    ->formatStateUsing(fn($state) => Str::title($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug'),
            ])
            ->defaultSort('kelas', 'asc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button(),
                Tables\Actions\DeleteAction::make()
                    ->button(),
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
            'index' => Pages\ListKelases::route('/'),
            //'create' => Pages\CreateKelase::route('/create'),
            //'edit' => Pages\EditKelase::route('/{record}/edit'),
        ];
    }

    //AKSES ROLE
    public static function canViewAny(): bool
    {
        return auth()->user()->isSuperAdmin(); // hanya super admin
    }
}
