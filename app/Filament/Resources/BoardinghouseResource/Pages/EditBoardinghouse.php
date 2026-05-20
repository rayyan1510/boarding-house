<?php

namespace App\Filament\Resources\BoardinghouseResource\Pages;

use App\Filament\Resources\BoardinghouseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBoardinghouse extends EditRecord
{
    protected static string $resource = BoardinghouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
