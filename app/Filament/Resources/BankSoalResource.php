<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankSoalResource\Pages;
use App\Filament\Resources\BankSoalResource\RelationManagers;
use App\Models\BankSoal;
use App\Models\Mapel;
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

                                $user = auth()->user();

                                if ($user->isSuperAdmin()) {
                                    return \App\Models\Mapel::pluck('mapel', 'id');
                                }

                                return $user->mapel()
                                    ->pluck('mapel', 'mapels.id'); // penting!
                            })
                            ->default(function () {

                                $user = auth()->user();

                                if (!$user->isSuperAdmin()) {
                                    return $user->mapel()
                                        ->select('mapels.id')
                                        ->value('mapels.id');
                                }

                                return null;
                            })
                            ->required(),
                        Forms\Components\Select::make('kelas')
                            ->options([
                                'Kelas-7' => 'Kelas 7',
                                'Kelas-8' => 'Kelas 8',
                                'Kelas-9' => 'Kelas 9',
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
                Tables\Actions\EditAction::make()->label('Add Soal')->button(),
                Tables\Actions\Action::make('review_all')
                    ->label('View Soal')
                    ->button()
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('success')
                    ->url(fn(BankSoal $record): string => static::getUrl('review', ['record' => $record->id])),
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
            'review' => Pages\ReviewBankSoal::route('/{record}/review'),
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
