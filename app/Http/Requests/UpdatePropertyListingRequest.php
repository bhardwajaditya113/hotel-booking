<?php

namespace App\Http\Requests;

use App\Models\Property;
use App\Support\PropertyListingValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return Property::query()
            ->where('user_id', auth()->id())
            ->whereKey($this->route('id'))
            ->exists();
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Contracts\Validation\Rule>>
     */
    public function rules(): array
    {
        return PropertyListingValidation::rules(false);
    }
}
