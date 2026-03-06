<?php

namespace App\Filament\Resources\NilaiResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JawabansRelationManager extends RelationManager
{
    protected static string $relationship = 'jawabans';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('soal_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('soal_id')
            ->columns([
                // 1. Menampilkan Soal (RichEditor biasanya mengandung HTML)
                Tables\Columns\TextColumn::make('soal.soal')
                    ->label('Pertanyaan')
                    ->html() // Penting agar tag p, b, i dari RichEditor diproses
                    ->limit(50),

                // 2. Menampilkan Jawaban Siswa
                Tables\Columns\TextColumn::make('jawaban')
                    ->label('Jawaban Siswa')
                    ->badge()
                    ->color(fn($record) => $record->skor > 0 ? 'success' : 'danger')
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        // 1. Pastikan data soal ada
                        $soal = $record->soal;
                        if (!$soal || !$soal->multiple_choice) {
                            return $state;
                        }

                        // 2. Dekode state jika dia string JSON (misal: '["A"]')
                        $jawabanSiswa = is_array($state) ? $state : json_decode($state, true);

                        // Jika gagal dekode, jadikan array dari string tunggal (misal: "A" jadi ["A"])
                        if (!$jawabanSiswa) {
                            $jawabanSiswa = [$state];
                        }

                        // 3. Ambil daftar pilihan dari bank soal
                        $opsiBankSoal = collect($soal->multiple_choice);

                        // 4. Cari teks untuk setiap huruf jawaban siswa
                        $hasil = collect($jawabanSiswa)->map(function ($huruf) use ($opsiBankSoal) {
                            $dataOpsi = $opsiBankSoal->where('opsi', $huruf)->first();
                            $teksJawaban = $dataOpsi['jawaban'] ?? 'Teks tidak ditemukan';

                            return "<strong>Jawaban : {$huruf}</strong><br>{$teksJawaban}";
                        });

                        return $hasil->join('<br>');
                    }),


                Tables\Columns\TextColumn::make('soal.tipe_soal')
                    ->label('Kunci / Info Soal')
                    ->formatStateUsing(function ($record, $state) {
                        // Cek apakah relasi soal ada
                        $soal = $record->soal;

                        if (!$soal) {
                            return 'Soal tidak ditemukan';
                        }

                        if ($state === 'multiple_choice') {
                            $kunci = collect($soal->multiple_choice ?? [])
                                ->where('skor', '>', 0)
                                ->pluck('opsi')
                                ->implode(', ');
                            return "Kunci: " . ($kunci ?: '-');
                        }

                        if ($state === 'matching') {
                            return "Tipe: Menjodohkan";
                        }

                        return $state;
                    })
                    // Gunakan null-safe operator (?) untuk description
                    ->description(fn($record) => $record->soal?->tipe_soal === 'multiple_choice' ? 'Pilihan Ganda' : 'Matching'),

                // 4. Skor yang didapat
                Tables\Columns\TextColumn::make('skor')
                    ->label('Poin')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
