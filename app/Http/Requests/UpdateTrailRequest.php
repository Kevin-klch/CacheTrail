<?php

namespace App\Http\Requests;

use App\Enums\TrailVisibility;
use App\Models\Trail;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
     * Publishing needs enough caches; routes that are already published may stay published.
     *
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            ...parent::after(),
            function (Validator $validator): void {
                /** @var Trail $trail */
                $trail = $this->route('trail');

                if ($this->input('visibility') === TrailVisibility::Published->value
                    && ! $trail->isPublished()
                    && ! $trail->hasEnoughCachesToPublish()) {
                    $validator->errors()->add('visibility', __('A route needs at least :count caches to be published.', ['count' => Trail::minCachesToPublish()]));
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
            ...parent::attributes(),
            'visibility' => __('Visibility'),
        ];
    }

    public function visibility(): TrailVisibility
    {
        return TrailVisibility::from($this->validated('visibility'));
    }
}
