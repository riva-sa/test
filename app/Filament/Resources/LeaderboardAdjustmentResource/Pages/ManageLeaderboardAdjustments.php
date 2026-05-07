<?php

namespace App\Filament\Resources\LeaderboardAdjustmentResource\Pages;

use App\Filament\Resources\LeaderboardAdjustmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLeaderboardAdjustments extends ManageRecords
{
    protected static string $resource = LeaderboardAdjustmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
