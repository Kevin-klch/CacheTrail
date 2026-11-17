<?php

namespace App\Http\Requests;

use App\Enums\TrailSort;
use App\Enums\Transportation;
use App\Models\Trail;
use App\Support\UserLocation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Search, filter, sort and view options of the route directory.
 */
class TrailIndexRequest extends FormRequest
{
    public const array VIEWS = ['compact', 'detailed', 'map'];

    public const array RADIUS_OPTIONS_KM = [5, 10, 25, 50, 100];

    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Trail::class);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rating = ['nullable', 'numeric', 'min:1', 'max:5'];

        return [
            'q' => ['nullable', 'string', 'max:100'],
            'transportation' => ['nullable', Rule::enum(Transportation::class)],
            'min_hours' => ['nullable', 'numeric', 'min:0', 'max:72'],
            'max_hours' => ['nullable', 'numeric', 'min:0', 'max:72'],
            'min_caches' => ['nullable', 'integer', 'min:0', 'max:100'],
            'max_caches' => ['nullable', 'integer', 'min:0', 'max:100'],
            'min_km' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'max_km' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'min_difficulty' => $rating,
            'max_difficulty' => $rating,
            'min_terrain' => $rating,
            'max_terrain' => $rating,
            'radius' => ['nullable', 'integer', Rule::in(self::RADIUS_OPTIONS_KM)],
            'sort' => ['nullable', Rule::enum(TrailSort::class)],
            'view' => ['nullable', Rule::in(self::VIEWS)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'transportation' => __('Means of transport'),
            'radius' => __('Distance'),
            'sort' => __('Sort by'),
            'view' => __('View'),
        ];
    }

    /**
     * The filter values that were actually set (sort and view are not filters).
     *
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        return array_filter(
            collect($this->validated())->except(['sort', 'view'])->all(),
            fn (mixed $value): bool => $value !== null && $value !== '',
        );
    }

    public function location(): ?UserLocation
    {
        return UserLocation::fromSession($this->session());
    }

    /**
     * "Nearest" needs a location; without one the directory falls back to the newest routes.
     */
    public function sort(): TrailSort
    {
        $sort = TrailSort::tryFrom((string) $this->validated('sort')) ?? TrailSort::Newest;

        return $sort === TrailSort::Nearest && $this->location() === null ? TrailSort::Newest : $sort;
    }

    public function view(): string
    {
        return $this->validated('view') ?? 'compact';
    }
}
