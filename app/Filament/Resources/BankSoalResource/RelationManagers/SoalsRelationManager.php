<?php

namespace App\Filament\Resources\BankSoalResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SoalsRelationManager extends RelationManager
{
    protected static string $relationship = 'soals';


    public function form(Form $form): Form
    {
        return $form->schema([

            section::make()
                ->schema([

                    Grid::make(3)
                        ->schema([
                            Textarea::make('soal')
                                ->label('Soal')
                                ->required()
                                ->rows(7)
                                ->autosize()
                                ->columnSpan(2),

                            Grid::make(1)
                                ->schema([
                                    Select::make('tipe_soal')
                                        ->label('Tipe Soal')
                                        ->options([
                                            'multiple_choice' => 'Multiple Choice',
                                            'matching' => 'Matching',
                                        ])
                                        ->required()
                                        ->reactive(),

                                    FileUpload::make('gambar')
                                        ->label('Gambar Soal')
                                        ->image()
                                        ->directory('soal')
                                        ->nullable(),
                                ])
                                ->columnSpan(1),
                        ]),

                ]),

            Repeater::make('multiple_choice')
                ->label('Pilihan Jawaban')
                ->default([])
                ->minItems(2)
                ->maxItems(5)
                ->visible(fn($get) => $get('tipe_soal') === 'multiple_choice')
                ->schema([
                    Grid::make(12)
                        ->schema([

                            TextInput::make('opsi')
                                ->label('Opsi')
                                ->placeholder('A')
                                ->required()
                                ->maxLength(1)
                                ->columnSpan(1),


                            TextInput::make('jawaban')
                                ->label('Jawaban')
                                ->required()
                                ->columnSpan(6),

                            TextInput::make('skor')
                                ->label('Skor')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->maxValue(999)
                                ->columnSpan(2),

                            FileUpload::make('jawaban_img')
                                ->label('file')
                                ->image()
                                ->directory('jawaban')
                                ->nullable()
                                ->columnSpan(3),
                        ])

                ])
                ->columnSpanFull(),












            /* ===============================
             | MATCHING
             =============================== */
            Repeater::make('matching')
                ->label('Soal Matching')
                ->default([])
                ->minItems(1)
                ->visible(fn($get) => $get('tipe_soal') === 'matching')
                ->schema([
                    Grid::make()
                        ->columns(2)
                        ->schema([

                            // ===== KOLOM KIRI =====
                            Grid::make()
                                ->schema([
                                    TextInput::make('kiri')
                                        ->label('Pertanyaan (Kiri)')
                                        ->required(),

                                    TextInput::make('kanan')
                                        ->label('Jawaban (Kanan)')
                                        ->required(),


                                ]),

                            // ===== KOLOM KANAN =====
                            Grid::make()
                                ->schema([
                                    FileUpload::make('kiri_img')
                                        ->label('Gambar Kiri')
                                        ->image()
                                        ->directory('matching/kiri')
                                        ->nullable(),

                                    FileUpload::make('kanan_img')
                                        ->label('Gambar Kanan')
                                        ->image()
                                        ->directory('matching/kanan')
                                        ->nullable(),
                                ]),
                        ]),

                    TextInput::make('matching_skor')
                        ->label('Skor')
                        ->numeric()
                        ->default(0)
                        ->required()
                        ->columnSpanFull(),


                ])
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('soal')
            ->columns([
                TextColumn::make('soal')
                    ->label('Soal')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('kunci_jawaban')
                    ->label('Kunci Jawaban')
                    ->badge()
                    ->state(function ($record) {

                        /* ===============================
         | MULTIPLE CHOICE (BISA >1 BENAR)
         =============================== */
                        if ($record->tipe_soal === 'multiple_choice') {
                            $choices = $record->multiple_choice ?? [];

                            $jawabanBenar = collect($choices)
                                ->filter(fn($item) => ($item['skor'] ?? 0) > 0)
                                ->map(fn($item) => $item['opsi'])
                                ->values();

                            return $jawabanBenar->isNotEmpty()
                                ? $jawabanBenar->implode(', ')
                                : '-';
                        }

                        /* ===============================
         | MATCHING
         =============================== */
                        if ($record->tipe_soal === 'matching') {
                            $matching = $record->matching ?? [];

                            return collect($matching)->isNotEmpty()
                                ? collect($matching)
                                ->map(fn($item) => $item['kiri'] . ' → ' . $item['kanan'])
                                ->implode(', ')
                                : '-';
                        }

                        return '-';
                    }),

                /* ===============================
             | SKOR
             =============================== */
                TextColumn::make('skor')
                    ->label('Skor')
                    ->state(function ($record) {
                        // MULTIPLE CHOICE
                        if ($record->tipe_soal === 'multiple_choice') {
                            return collect($record->multiple_choice ?? [])
                                ->sum(fn($item) => (int) ($item['skor'] ?? 0));
                        }

                        // MATCHING
                        if ($record->tipe_soal === 'matching') {
                            return collect($record->matching ?? [])
                                ->sum(fn($item) => (int) ($item['matching_skor'] ?? 0));
                        }

                        return 0;
                    }),

            ])

            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(null), // 🔥 INI KUNCI,
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
