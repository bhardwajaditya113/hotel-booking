<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('guests') && ! $this->filled('persion')) {
            $this->merge(['persion' => $this->input('guests')]);
        }
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Contracts\Validation\Rule>>
     */
    public function rules(): array
    {
        return [
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'room_id' => [
                'required',
                'integer',
                Rule::exists('rooms', 'id')->where(
                    fn ($q) => $q->where('property_id', (int) $this->input('property_id'))
                ),
            ],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'number_of_rooms' => ['required', 'integer', 'min:1', 'max:99'],
            'persion' => ['required', 'integer', 'min:1', 'max:99'],
            'guests' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $room = Room::query()->find((int) $this->input('room_id'));
            $guests = (int) $this->input('persion');

            if ($room && $room->total_adult !== null && $guests > (int) $room->total_adult) {
                $validator->errors()->add(
                    'persion',
                    'The number of guests exceeds this room\'s capacity.'
                );
            }
        });
    }
}
