<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScoreRequest extends FormRequest
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
            'score'           => ['required', 'integer', 'min:0', 'max:1000000'],
            'coins_collected' => ['required', 'integer', 'min:0', 'max:100000'],
            'difficulty'      => ['required', 'in:normal,hard'],
            'duration'        => ['required', 'integer', 'min:0', 'max:36000'],
            'character_id'    => ['nullable', 'integer', 'exists:characters,id'],
        ];
    }
}
