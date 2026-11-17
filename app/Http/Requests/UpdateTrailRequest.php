<?php

namespace App\Http\Requests;

use App\Enums\TrailVisibility;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateTrailRequest extends StoreTrailRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('trail'));
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'visibility' => ['required', Rule::enum(TrailVisibility::class)],
            'remove_image' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            ...parent::attributes(),
            'visibility' => __('Visibility'),
        ];
    }

    public function visibility(): TrailVisibility
    {
        return TrailVisibility::from($this->validated('visibility'));
    }
}
