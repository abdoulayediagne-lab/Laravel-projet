<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpenChestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:normal,legendary'],
        ];
    }
}
