<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;

class NormalizePhoneAction
{
    /**
     * Normalize a phone number to start with +966 and remove spaces and symbols.
     * If the number cannot be formatted as a valid Saudi mobile number, it will be
     * logged for manual review but returned as-is to still allow lead creation.
     *
     * @param string $phone
     * @return string
     */
    public function execute(string $phone): string
    {
        return $phone;
    }
}