<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Unit;

class CreateUnit extends CreateRecord
{
    protected static string $resource = UnitResource::class;

    public function mount(): void
    {
        parent::mount();

        // Check if the 'copy' parameter exists in the URL
        if (request()->has('copy')) {
            $copyUnit = Unit::find(request()->get('copy')); // Get the unit to copy

            if ($copyUnit) {
                // Fill the form fields with the data from the unit to copy
                $this->form->fill([
                    'title' => $copyUnit->title,
                    'project_id' => $copyUnit->project_id,
                    'floor' => $copyUnit->floor,
                    'slug' => $copyUnit->slug . '-copy',
                    'unit_type' => $copyUnit->unit_type,
                    'building_number' => $copyUnit->building_number,
                    'unit_number' => $copyUnit->unit_number,
                    'description' => $copyUnit->description,
                    'unit_area' => $copyUnit->unit_area,
                    'unit_price' => $copyUnit->unit_price,
                    'living_rooms' => $copyUnit->living_rooms,
                    'beadrooms' => $copyUnit->beadrooms,
                    'bathrooms' => $copyUnit->bathrooms,
                    'kitchen' => $copyUnit->kitchen,
                    'latitude' => $copyUnit->latitude,
                    'longitude' => $copyUnit->longitude,
                    'image' => $copyUnit->image,
                    'floor_plan' => $copyUnit->floor_plan,
                    'show_price' => $copyUnit->show_price,
                    'status' => $copyUnit->status,
                ]);
            }
        }
    }

}
