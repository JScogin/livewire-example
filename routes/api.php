<?php

use App\Http\Controllers\Api\WidgetController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    Route::apiResource('widgets', WidgetController::class);
    Route::patch('widgets/{widget}', [WidgetController::class, 'partialUpdate'])->name('widgets.partial-update');
});

