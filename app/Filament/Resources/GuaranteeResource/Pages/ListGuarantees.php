<?php

namespace App\Filament\Resources\GuaranteeResource\Pages;

use App\Filament\Resources\GuaranteeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGuarantees extends ListRecords
{
    protected static string $resource = GuaranteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
