<?php

namespace App\Services;

use App\Jobs\ProcessWidgetJob;
use App\Jobs\SendWidgetFollowUpEmailJob;
use App\Models\Widget;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

class WidgetService
{
    /**
     * Get a paginated list of widgets with optional filtering.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Widget::query();

        // Search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Status filter
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Price range filters
        $minPrice = $filters['min_price'] ?? null;
        $maxPrice = $filters['max_price'] ?? null;
        if ($minPrice !== null || $maxPrice !== null) {
            $query->priceRange(
                $minPrice !== null ? (float) $minPrice : null,
                $maxPrice !== null ? (float) $maxPrice : null
            );
        }

        // Quantity range filters
        if (isset($filters['min_quantity'])) {
            $query->where('quantity', '>=', (int) $filters['min_quantity']);
        }

        if (isset($filters['max_quantity'])) {
            $query->where('quantity', '<=', (int) $filters['max_quantity']);
        }

        // Sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';

        $allowedSortFields = ['name', 'price', 'quantity', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Find a widget by ID.
     *
     * @param int $id
     * @return Widget
     * @throws ModelNotFoundException
     */
    public function find(int $id): Widget
    {
        $widget = Widget::find($id);

        if (!$widget) {
            throw new ModelNotFoundException('Widget not found');
        }

        return $widget;
    }

    /**
     * Create a new widget.
     *
     * @param array<string, mixed> $data
     * @return Widget
     */
    public function create(array $data): Widget
    {
        $widget = Widget::create($data);

        // Dispatch processing job
        ProcessWidgetJob::dispatch($widget);

        // Dispatch follow-up email job with 24-hour delay
        SendWidgetFollowUpEmailJob::dispatch($widget)
            ->delay(now()->addHours(24));

        return $widget;
    }

    /**
     * Update a widget.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @param bool $partial
     * @return Widget
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data, bool $partial = false): Widget
    {
        $widget = $this->find($id);

        if ($partial) {
            // Only update provided fields
            $widget->fill($data);
        } else {
            // Update all fields (PUT request)
            $widget->fill($data);
        }

        $widget->save();

        // Dispatch processing job to recalculate statistics
        ProcessWidgetJob::dispatch($widget->fresh());

        return $widget->fresh();
    }

    /**
     * Delete (soft delete) a widget.
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $widget = $this->find($id);

        return $widget->delete();
    }

    /**
     * Search widgets by query string.
     *
     * @param string $query
     * @param array<string> $fields
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(string $query, array $fields = ['name', 'description'])
    {
        return Widget::query()->search($query)->get();
    }
}

