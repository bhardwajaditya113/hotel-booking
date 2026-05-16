<?php

namespace App\Http\Requests;

use App\Support\PropertyListingValidation;
use Illuminate\Foundation\Http\FormRequest;

class StorePropertyListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Contracts\Validation\Rule>>
     */
    public function rules(): array
    {
        return PropertyListingValidation::rules(true);
    }
}
