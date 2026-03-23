<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => ['sometimes', 'string', 'min:3', 'max:255'],
            'description' => ['sometimes', 'string', 'min:5'],
            'category_id' => ['sometimes', 'nullable', 'exists:categories,id'],
            'priority_id' => ['sometimes', 'nullable', 'exists:priorities,id'],
            'status_id' => ['sometimes', 'exists:statuses,id'],
            'assigned_to' => ['sometimes', 'nullable', 'exists:users,id'],
        ];
    }
}
