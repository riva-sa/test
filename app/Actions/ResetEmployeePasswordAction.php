<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ResetEmployeePasswordAction
{
    /**
     * Send a password reset link to the given employee.
     *
     * @return string Status constant from Password broker
     */
    public function execute(User $targetUser): string
    {
        $status = Password::broker()->sendResetLink([
            'email' => $targetUser->email,
        ]);

        Log::info('Password reset triggered', [
            'event' => 'password_reset_request',
            'target_user_email' => $targetUser->email,
            'target_user_id' => $targetUser->id,
            'triggered_by_user_id' => auth()->id(),
            'status' => $status,
        ]);

        return $status;
    }
}
