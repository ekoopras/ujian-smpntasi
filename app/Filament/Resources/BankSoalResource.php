<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankSoalResource\Pages;
use App\Filament\Resources\BankSoalResource\RelationManagers;
use App\Models\BankSoal;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BankSoalResource extends Resource
{
    protected static ?string $model = BankSoal::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Bank Soal';
    protected static ?string $navigationGroup = 'Manajemen Ujian';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make()
                    ->schema([
                        Forms\Components\Select::make('mapel_id')
                            ->label('Mapel')
                            ->options(function () {
                                // Super admin bisa pilih semua mapel
                                if (auth()->user()->isSuperAdmin()) {
                                    return \App\Models\Mapel::all()->pluck('mapel', 'id');
                                }
                                // Guru otomatis hanya mapel mereka sendiri
                                return \App\Models\Mapel::where('id', auth()->user()->mapel_id)
                                    ->pluck('mapel', 'id');
                            })
                            ->default(function () {
                                // Set default mapel guru
                                if (!auth()->user()->isSuperAdmin()) {
                                    return auth()->user()->mapel_id;
                                }
                                return null; // super admin default kosong
                            })
                            ->required(),
                        Forms\Components\Select::make('kelas')
                            ->options([
                                'Kelas-1' => 'Kelas 1',
                                'Kelas-2' => 'Kelas 2',
                                'Kelas-3' => 'Kelas 3',
                            ])
                            ->required(),

                        Forms\Components\Select::make('semester')
                            ->options([
                                '1' => 'Semester 1',
                                '2' => 'Semester 2',
                            ])
                            ->required(),
                    ])->columns(3),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mapel.mapel')
                    ->label('Mapel')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelas'),
                Tables\Columns\TextColumn::make('semester'),
                Tables\Columns\TextColumn::make('soals_count')
                    ->label('Jumlah Soal')
                    ->counts('soals') // otomatis hitung relasi
                    ->sortable()
                    ->badge()
                    ->color(fn(int $state): string => $state > 0 ? 'success' : 'secondary'),
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
                Tables\Actions\EditAction::make()->button(),
                Tables\Actions\DeleteAction::make()->button(),



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
            RelationManagers\SoalsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankSoals::route('/'),
            //'create' => Pages\CreateBankSoal::route('/create'),
            'edit' => Pages\EditBankSoal::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->isSuperAdmin()) {
            return $query; // super admin lihat semua
        }

        //return $query->where('mapel_id', auth()->user()->mapel_id); // guru hanya mapel sendiri

        return $query->whereIn(
            'mapel_id',
            auth()->user()->mapel()->pluck('mapels.id')
        );
    }
}
