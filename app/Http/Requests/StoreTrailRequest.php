<?php

namespace App\Http\Requests;

use App\Enums\Transportation;
use App\Models\Trail;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTrailRequest extends FormRequest
{
    public const int MAX_IMAGE_KILOBYTES = 5120;

    public function authorize(): bool
    {
        return $this->user()->can('create', Trail::class);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:10000'],
            'duration_hours' => ['required', 'integer', 'min:0', 'max:72'],
            'duration_minutes' => ['required', 'integer', 'min:0', 'max:59'],
            'transportations' => ['required', 'array', 'min:1'],
            'transportations.*' => ['required', 'distinct', Rule::enum(Transportation::class)],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::MAX_IMAGE_KILOBYTES],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $validator->errors()->hasAny(['duration_hours', 'duration_minutes']) && $this->durationInMinutes() < 5) {
                    $validator->errors()->add('duration_hours', __('The estimated duration must be at least 5 minutes.'));
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('Route name'),
            'description' => __('Description'),
            'duration_hours' => __('Hours'),
            'duration_minutes' => __('Minutes'),
            'transportations' => __('Means of transport'),
            'transportations.*' => __('Means of transport'),
            'image' => __('Cover image'),
        ];
    }

    public function durationInMinutes(): int
    {
        return (int) $this->input('duration_hours') * 60 + (int) $this->input('duration_minutes');
    }

    /**
     * @return array{name: string, description: string, estimated_duration_minutes: int, transportations: list<Transportation>}
     */
    public function trailAttributes(): array
    {
        return [
            'name' => $this->validated('name'),
            'description' => $this->validated('description'),
            'estimated_duration_minutes' => $this->durationInMinutes(),
            'transportations' => $this->selectedTransportations(),
        ];
    }

    /**
     * The selected means of transport in their canonical order.
     *
     * @return list<Transportation>
     */
    public function selectedTransportations(): array
    {
        $selected = $this->validated('transportations');

        return array_values(array_filter(
            Transportation::cases(),
            fn (Transportation $mode): bool => in_array($mode->value, $selected, true),
        ));
    }
}
