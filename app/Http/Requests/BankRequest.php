<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BankRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
            'iban' => ['required', 'string', 'max:34'],
            'tax_id' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'token' => ['nullable', 'string'],
            'account_id' => ['nullable', 'string'],
            'watch' => ['boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $bank = $this->route('bank');

            $effectiveToken = $this->input('token') ?: optional($bank)->token;
            // The account_id field is locked once set (see BankController::update),
            // so an existing account_id always wins over whatever was submitted.
            $effectiveAccountId = optional($bank)->account_id ?: $this->input('account_id');

            if ($this->boolean('watch') && (! filled($effectiveToken) || ! filled($effectiveAccountId))) {
                $validator->errors()->add('watch', 'Відстеження можна увімкнути лише якщо задано токен і обрано гаманець.');
            }
        });
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);
        $data['watch'] = $this->boolean('watch');
        $data['order'] = filled($data['order'] ?? null) ? (int) $data['order'] : 0;

        return $data;
    }
}
