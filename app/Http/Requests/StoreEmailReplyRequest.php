<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailReplyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->email->load('receivers');
        foreach ($this->email->receivers as $receiver) {
            if ($receiver['receiver_id'] == auth()->id()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sender_id' => ['integer', 'exists:users,id'],
            'reply_to' => ['nullable', 'exists:messages,id'],
            'content' => ['required'],
            'files' => ['array'],
        ];
    }
}
