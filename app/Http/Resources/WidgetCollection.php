<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WidgetCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        // When the collection is paginated, $this->collection is the paginator
        $paginator = $this->collection instanceof \Illuminate\Pagination\AbstractPaginator 
            ? $this->collection 
            : null;

        if (!$paginator) {
            return [];
        }

        return [
            'meta' => [
                'current_page' => (int) $paginator->currentPage(),
                'per_page' => (int) $paginator->perPage(),
                'total' => (int) $paginator->total(),
                'last_page' => (int) $paginator->lastPage(),
                'from' => $paginator->firstItem() !== null ? (int) $paginator->firstItem() : null,
                'to' => $paginator->lastItem() !== null ? (int) $paginator->lastItem() : null,
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ];
    }
}

