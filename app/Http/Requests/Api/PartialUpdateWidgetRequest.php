<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartialUpdateWidgetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $widgetId = $this->route('widget');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('widgets', 'name')->ignore($widgetId)],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:999999.99'],
            'quantity' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'status' => ['sometimes', 'nullable', 'in:active,inactive,archived'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }
}

