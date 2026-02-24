<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UjianResource\Pages;
use App\Filament\Resources\UjianResource\RelationManagers;
use App\Models\BankSoal;
use App\Models\Kelase;
use App\Models\Mapel;
use App\Models\Ujian;
use Filament\Forms;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;


class UjianResource extends Resource
{
    protected static ?string $model = Ujian::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Ujian';
    protected static ?string $navigationGroup = 'Manajemen Ujian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('kelase_id')
                //     ->label('Kelas')
                //     ->relationship('kelase', 'kelas') // pastikan field 'nama' ada di tabel kelas
                //     ->required(),

                Section::make()
                    ->schema([
                        Forms\Components\Select::make('kelase')
                            ->label('Kelas')
                            ->relationship('kelase', 'kelas')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required(),

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

                        Forms\Components\Select::make('bank_soal_id')
                            ->label('Bank Soal')
                            ->searchable()
                            ->options(function (Get $get) {

                                if (!$get('mapel_id')) {
                                    return [];
                                }

                                return BankSoal::where('mapel_id', $get('mapel_id'))
                                    ->with('mapel')
                                    ->get()
                                    ->mapWithKeys(fn($record) => [
                                        $record->id =>
                                        $record->mapel->mapel
                                            . ' | ' . $record->kelas
                                            . ' | Semester ' . $record->semester,
                                    ]);
                            })
                            ->required(),

                        Forms\Components\TextInput::make('kode_ujian')
                            ->label('Kode Ujian')
                            ->disabled() // tidak bisa diubah user
                            ->dehydrated(false) // jangan kirim ke backend (biar auto generate)
                            ->default(function () {
                                return strtoupper(Str::random(6));
                            })
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generate')
                                    ->icon('heroicon-o-arrow-path')
                                    ->label('Generate')
                                    ->action(function (Forms\Set $set) {
                                        $set('kode_ujian', strtoupper(Str::random(6)));
                                    })
                            ),

                        Forms\Components\TextInput::make('unlock_code')
                            ->label('Kode Buka Ujian (6 Digit)')
                            ->helperText('Digunakan jika siswa keluar dari tab ujian')
                            ->numeric()
                            ->length(6)
                            ->required()
                            ->rule('digits:6')
                            ->password() // biar tidak kelihatan sembarang orang
                            ->revealable() // bisa dilihat kalau diklik
                            ->autocomplete(false),

                        Forms\Components\TextInput::make('durasi_menit')
                            ->label('Durasi Ujian (menit)')
                            ->numeric()
                            ->default(60)
                            ->suffix('menit')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->onColor('success')
                            ->offColor('danger')
                            ->inline(false)
                            ->default(false),


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
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        $words = explode(' ', $state);
                        // ambil 4 kata pertama untuk baris pertama
                        $firstLine = implode(' ', array_slice($words, 0, 3));
                        // sisanya untuk baris kedua
                        $secondLine = implode(' ', array_slice($words, 3));
                        // gabungkan dengan <br>
                        return $firstLine . '<br>' . $secondLine;
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('kelase.kelas')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('kode_ujian')
                    ->label('Kode Ujian')
                    ->badge()
                    ->color('success')
                    ->copyable(),
                Tables\Columns\TextColumn::make('durasi_menit')
                    ->label('Durasi')
                    ->suffix(' menit'),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime('d M Y'),
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
            RelationManagers\PesertasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUjians::route('/'),
            //'create' => Pages\CreateUjian::route('/create'),
            'edit' => Pages\EditUjian::route('/{record}/edit'),
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
