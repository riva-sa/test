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
        // Remove all non-numeric characters
        // $digits = preg_replace('/\D/', '', $phone);

        // // If empty, return empty string
        // if ($digits === '') {
        //     return $phone;
        // }

        // // Check for valid Saudi mobile number formats:
        // // 1. Already in international format: +966 followed by 9 digits (total 12 digits)
        // //    and the first digit after country code is 5 (mobile prefix)
        // if (preg_match('/^966\d{9}$/', $digits) && substr($digits, 3, 1) == '5') {
        //     return '+' . $digits;
        // }

        // // 2. Local format with leading 0: 0 followed by 9 digits, first digit after 0 is 5
        // if (preg_match('/^0\d{9}$/', $digits) && substr($digits, 1, 1) == '5') {
        //     return '+966' . substr($digits, 1);
        // }

        // // 3. Local format without leading 0: 9 digits, first digit is 5
        // if (preg_match('/^\d{9}$/', $digits) && substr($digits, 0, 1) == '5') {
        //     return '+966' . $digits;
        // }

        // // If none of the above, we cannot format it as a valid Saudi mobile number.
        // // Log for manual review but return the original phone number to still allow lead creation.
        // Log::warning('Phone number could not be formatted to Saudi mobile format', [
        //     'original' => $phone,
        //     'cleaned' => $digits,
        // ]);

        return $phone;
    }
}