<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string', 'min:5'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'priority_id' => ['nullable', 'exists:priorities,id'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ];
    }
}
