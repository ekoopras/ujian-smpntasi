<?php

namespace App\Filament\Pages;

use App\Models\Peserta;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class PesertaUjian extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $title = 'Peserta';
    protected static string $view = 'filament.pages.peserta-ujian';

    public function table(Table $table): Table
    {
        return $table
            ->query(Peserta::query())
            ->poll('10s') // Auto-refresh tabel setiap 10 detik
            ->defaultSort('is_locked', 'desc') // Yang terkunci otomatis paling atas
            ->columns([
                TextColumn::make('nama')
                    ->sortable(),

                TextColumn::make('nomor_absen')
                    ->sortable(),

                TextColumn::make('kelase.kelas')
                    ->sortable(),

                TextColumn::make('ujian.mapel.mapel')
                    ->sortable(),

                ToggleColumn::make('is_locked')
                    ->label('Status Kunci')
                    ->onColor('danger') // Warna saat terkunci
                    ->offColor('success') // Warna saat aktif (terbuka)
                    ->onIcon('heroicon-m-lock-closed')
                    ->offIcon('heroicon-m-lock-open')
                    // Fungsi ini dijalankan otomatis oleh Filament via AJAX
                    ->afterStateUpdated(function ($record, $state) {
                        if (!$state) {
                            Notification::make()
                                ->title("Akses {$record->nama} Dibuka")
                                ->success()
                                ->send();
                        }
                    }),

                TextColumn::make('tab_violation')
                    ->label('Pelanggaran')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'warning' : 'gray'),

                TextColumn::make('updated_at')
                    ->label('Terdeteksi')
                    ->dateTime()
                    ->since()
                    ->color('gray'),
            ])
            ->filters([
                // 1. Filter Kelas (Menggunakan Relasi)
                SelectFilter::make('kelase_id') // Gunakan nama foreign key di tabel pesertas
                    ->label('Pilih Kelas')
                    ->relationship('kelase', 'kelas') // 'kelase' adalah nama relasi, 'kelas' adalah kolom nama kelasnya
                    ->searchable()
                    ->preload(),

                // 2. Filter Nomor Absen (Manual Input)
                Filter::make('nomor_absen')
                    ->form([
                        TextInput::make('nomor_absen')
                            ->label('Cari No. Absen')
                            ->numeric()
                            ->placeholder('Contoh: 01'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['nomor_absen'],
                            fn(Builder $query, $absen): Builder => $query->where('nomor_absen', $absen),
                        );
                    })
            ])
            // Layout agar filter tampil berjejer di atas tabel
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(2)

            ->headerActions([
                Action::make('unlock_all')
                    ->label('Buka Semua Kunci')
                    ->color('gray')
                    ->icon('heroicon-m-lock-open')
                    ->requiresConfirmation()
                    ->action(fn() => Peserta::where('is_locked', true)->update(['is_locked' => false])),
            ])

            ->actions([
                // Tombol aksi buka kunci
                Action::make('unlock')
                    ->label('Buka Kunci')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->visible(fn(Peserta $record): bool => $record->is_locked)
                    ->action(function (Peserta $record) {
                        $record->update(['is_locked' => false]);

                        Notification::make()
                            ->title('Berhasil')
                            ->body("Ujian {$record->nama} telah dibuka.")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isSuperAdmin(); // hanya super admin
    }
}
