<?php

namespace App\Filament\Resources;

use App\Exports\NilaiExport;
use App\Filament\Resources\NilaiResource\Pages;
use App\Filament\Resources\NilaiResource\RelationManagers;
use App\Models\Mapel;
use App\Models\Nilai;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;

class NilaiResource extends Resource
{
    protected static ?string $model = Nilai::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Nilai Siswa';

    protected static ?string $navigationGroup = 'Manajemen Ujian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('peserta.nomor_absen')
                    ->label('Nomor Absen')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('peserta.nama')
                    ->label('Nama Siswa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('peserta.kelase.kelas')
                    ->label('Kelas')
                    ->alignment(Alignment::Center)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ujian.mapel.mapel')
                    ->label('Ujian')
                    ->alignment(Alignment::Center)
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('total_skor')
                    ->label('Nilai')
                    ->alignment(Alignment::Center)
                    ->badge()
                    ->color(fn($state) => $state >= 75 ? 'success' : 'danger')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('kelas')
                    ->label('Kelas')
                    ->relationship('peserta.kelase', 'kelas'),

                SelectFilter::make('mapel')
                    ->label('Mapel')
                    ->relationship('ujian.mapel', 'mapel')
                    ->visible(fn() => auth()->user()->isSuperAdmin()),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($livewire) {
                        $query = $livewire->getFilteredTableQuery();

                        return Excel::download(
                            new NilaiExport($query),
                            'nilai.xlsx'
                        );
                    }),

                Tables\Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document')
                    ->action(function ($livewire) {
                        $data = $livewire
                            ->getFilteredTableQuery()
                            ->get();

                        return response()->streamDownload(function () use ($data) {
                            echo Pdf::loadView('export.pdf.nilai', [
                                'data' => $data
                            ])->output();
                        }, 'nilai.pdf');
                    }),
            ])
            ->paginated([50])
            //->defaultPaginationPageOption(50)
            ->actions([
                //Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListNilais::route('/'),
            //'create' => Pages\CreateNilai::route('/create'),
            //'edit' => Pages\EditNilai::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }


    // public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery();

    //     if (!auth()->user()->isSuperAdmin()) {
    //         $query->whereHas('peserta.ujian', function ($q) {
    //             $q->where('mapel_id', auth()->user()->mapel_id);
    //         });
    //     }

    //     return $query;
    // }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!auth()->user()->isSuperAdmin()) {
            $query->whereHas('ujian', function ($q) {
                $q->whereIn(
                    'mapel_id',
                    auth()->user()->mapel()->pluck('mapels.id')
                );
            });
        }

        return $query;
    }
}
