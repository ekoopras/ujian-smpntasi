<?php

namespace App\Filament\Resources\KelaseResource\Pages;

use App\Filament\Resources\KelaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKelase extends EditRecord
{
    protected static string $resource = KelaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
