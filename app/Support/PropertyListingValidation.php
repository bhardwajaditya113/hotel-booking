<?php

namespace App\Support;

final class PropertyListingValidation
{
    /**
     * @return array<string, array<int, string|\Illuminate\Contracts\Validation\Rule>>
     */
    public static function rules(bool $requireListingType): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'property_type_id' => ['required', 'integer', 'exists:property_types,id'],
            'address' => ['required', 'string', 'max:2000'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'zipcode' => ['nullable', 'string', 'max:32'],
            'description' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:64'],
            'email' => ['nullable', 'email', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'instant_book_enabled' => ['sometimes', 'boolean'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['integer', 'exists:amenities,id'],
            'house_rules' => ['nullable', 'string', 'max:8000'],
            'cancellation_preset' => ['nullable', 'in:flexible,moderate,firm,custom'],
            'cancellation_policy_text' => ['nullable', 'required_if:cancellation_preset,custom', 'string', 'max:8000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];

        if ($requireListingType) {
            $rules['listing_type'] = ['required', 'in:hotel,unique_stay'];
        }

        return $rules;
    }
}
