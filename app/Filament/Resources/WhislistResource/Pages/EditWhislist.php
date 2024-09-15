<?php

namespace App\Filament\Resources\WhislistResource\Pages;

use App\Filament\Resources\WhislistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWhislist extends EditRecord
{
    protected static string $resource = WhislistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
