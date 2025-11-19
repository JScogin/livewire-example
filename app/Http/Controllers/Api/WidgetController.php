<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PartialUpdateWidgetRequest;
use App\Http\Requests\Api\StoreWidgetRequest;
use App\Http\Requests\Api\UpdateWidgetRequest;
use App\Http\Resources\WidgetCollection;
use App\Http\Resources\WidgetResource;
use App\Models\Widget;
use App\Services\WidgetService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function __construct(
        private WidgetService $widgetService
    ) {
    }

    /**
     * Display a listing of widgets.
     */
    public function index(Request $request): WidgetCollection
    {
        $filters = $request->only([
            'search',
            'status',
            'min_price',
            'max_price',
            'min_quantity',
            'max_quantity',
            'sort',
            'direction',
        ]);

        $perPage = $request->has('per_page') ? (int) $request->get('per_page') : 15;
        if ($perPage <= 0) {
            $perPage = 15;
        }
        $widgets = $this->widgetService->list($filters, $perPage);

        return new WidgetCollection($widgets);
    }

    /**
     * Store a newly created widget.
     */
    public function store(StoreWidgetRequest $request): JsonResponse
    {
        try {
            $widget = $this->widgetService->create($request->validated());

            return (new WidgetResource($widget))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'message' => 'Failed to create widget',
                    'code' => 'CREATE_ERROR',
                    'status' => 500,
                ],
            ], 500);
        }
    }

    /**
     * Display the specified widget.
     */
    public function show(Widget $widget): JsonResponse
    {
        return (new WidgetResource($widget))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Update the specified widget (full update).
     */
    public function update(UpdateWidgetRequest $request, Widget $widget): JsonResponse
    {
        try {
            $updated = $this->widgetService->update($widget->id, $request->validated(), false);

            return (new WidgetResource($updated))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'message' => 'Failed to update widget',
                    'code' => 'UPDATE_ERROR',
                    'status' => 500,
                ],
            ], 500);
        }
    }

    /**
     * Partially update the specified widget.
     */
    public function partialUpdate(PartialUpdateWidgetRequest $request, Widget $widget): JsonResponse
    {
        try {
            $updated = $this->widgetService->update($widget->id, $request->validated(), true);

            return (new WidgetResource($updated))
                ->response()
                ->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'message' => 'Failed to update widget',
                    'code' => 'UPDATE_ERROR',
                    'status' => 500,
                ],
            ], 500);
        }
    }

    /**
     * Remove the specified widget (soft delete).
     */
    public function destroy(Widget $widget): JsonResponse
    {
        try {
            $this->widgetService->delete($widget->id);

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'message' => 'Failed to delete widget',
                    'code' => 'DELETE_ERROR',
                    'status' => 500,
                ],
            ], 500);
        }
    }
}

