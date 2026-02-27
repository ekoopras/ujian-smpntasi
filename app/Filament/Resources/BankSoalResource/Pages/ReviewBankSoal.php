<?php

namespace App\Filament\Resources\BankSoalResource\Pages;

use App\Filament\Resources\BankSoalResource;
use App\Models\BankSoal;
use Filament\Resources\Pages\Page;

class ReviewBankSoal extends Page
{
    protected static string $resource = BankSoalResource::class;

    protected static string $view = 'filament.resources.bank-soal-resource.pages.review-bank-soal';

    protected static ?string $title = 'Review Seluruh Soal';

    public $bankSoal;

    public function mount($record)
    {
        // Mengambil data Bank Soal beserta relasi soalnya
        $this->bankSoal = BankSoal::with('soals')->findOrFail($record);
    }
}
