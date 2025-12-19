<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $postal = $this->input('shipping_postal_code', $this->input('postal_code'));
        $postal = is_string($postal) ? str_replace('-', '', $postal) : $postal;

        $this->merge([
            'shipping_postal_code' => $postal,
            'shipping_address'     => $this->input('shipping_address', $this->input('address')),
            'shipping_building'    => $this->input('shipping_building', $this->input('building')),
        ]);
    }

    public function rules(): array
    {
        return [
            'shipping_postal_code' => ['required', 'regex:/^\d{7}$/'],
            'shipping_address'     => ['required', 'string', 'max:255'],
            'shipping_building'    => ['nullable', 'string', 'max:255'],
        ];
    }
}