<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:20|max:100',
            'description' => 'required|string|min:20',
            'content' => 'required|string|min:20',
            'cover_url' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'file',
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:2048',
            ],
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
