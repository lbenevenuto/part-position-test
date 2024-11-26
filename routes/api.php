<?php

use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\EpisodePartController;
use App\Http\Controllers\EpisodePartItemBlockBlockFieldController;
use App\Http\Controllers\PartController;

Route::post('episodes/{episode}/duplicate', [EpisodeController::class, 'duplicate']);

Route::apiResources([
    'episodes' => EpisodeController::class,
    'parts' => PartController::class,
]);

Route::post('episodes/{episode}/parts/sort', [EpisodePartController::class, 'sort']);
Route::apiResource('episodes.parts', EpisodePartController::class)->scoped();
Route::apiResource('episodes.parts.items.blocks.block-fields', EpisodePartItemBlockBlockFieldController::class)->scoped();
