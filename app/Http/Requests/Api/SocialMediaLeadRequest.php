<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SocialMediaLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return true;
    // }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'marketing_source' => [
                'required', 
                Rule::in(['TikTok', 'Snapchat', 'Facebook', 'Instagram', 'Google', 'Twitter', 'LinkedIn', 'WhatsApp'])
            ],
            'campaign_name' => ['nullable', 'string', 'max:255'],
            'ad_squad' => ['nullable', 'string', 'max:255'],
            'ad_set' => ['nullable', 'string', 'max:255'],
            'ad_name' => ['nullable', 'string', 'max:255'],
            'external_id' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
        ];
    }
}
