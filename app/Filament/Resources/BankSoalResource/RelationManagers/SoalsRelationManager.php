<?php

namespace App\Filament\Resources\BankSoalResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SoalsRelationManager extends RelationManager
{
    protected static string $relationship = 'soals';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('soal')
                    ->label('Soal')
                    ->required()
                    ->rows(3),

                Forms\Components\FileUpload::make('gambar')
                    ->label('Gambar Soal')
                    ->directory('soal-images')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048)
                    ->nullable(),

                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('a')->label('Jawaban A'),
                    Forms\Components\TextInput::make('skor_a')->numeric()->default(0)->label('Skor A'),
                    Forms\Components\TextInput::make('b')->label('Jawaban B'),
                    Forms\Components\TextInput::make('skor_b')->numeric()->default(0)->label('Skor B'),
                    Forms\Components\TextInput::make('c')->label('Jawaban C'),
                    Forms\Components\TextInput::make('skor_c')->numeric()->default(0)->label('Skor C'),
                    Forms\Components\TextInput::make('d')->label('Jawaban D'),
                    Forms\Components\TextInput::make('skor_d')->numeric()->default(0)->label('Skor D'),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('soal')
            ->columns([
                Tables\Columns\TextColumn::make('soal')->limit(60)->label('Soal'),
                Tables\Columns\TextColumn::make('a')->label('A'),
                Tables\Columns\TextColumn::make('b')->label('B'),
                Tables\Columns\TextColumn::make('c')->label('C'),
                Tables\Columns\TextColumn::make('d')->label('D'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
