<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Auth\Access\AuthorizationException;

class SocialMediaLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['nullable', 'string', 'email', 'max:255'],
            'phone'            => ['required', 'string', 'max:50'],
            'marketing_source' => ['required', 'string', 'max:255'],
            'campaign_name'    => ['nullable', 'string', 'max:255'],
            'ad_squad'         => ['nullable', 'string', 'max:255'],
            'ad_set'           => ['nullable', 'string', 'max:255'],
            'ad_name'          => ['nullable', 'string', 'max:255'],
            'external_id'      => ['nullable', 'string', 'max:255'],
            'message'          => ['nullable', 'string'],
        ];
    }

    // Force JSON response on validation failure (not HTML redirect)
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }

    // Force JSON response on authorization failure
    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized.',
            ], 403)
        );
    }
}