<?php

use Illuminate\Support\Facades\Route;

Route::post('calendar-events', [\App\Http\Controllers\API\V1\CalEventController::class, 'index']);
Route::post('calendar-events/store', [\App\Http\Controllers\API\V1\CalEventController::class, 'storeEvent']);

Route::group(['prefix' => 'tasks'], function () {
    Route::post('/', [\App\Http\Controllers\API\V1\TaskController::class, 'index']);
    Route::post('/detail', [\App\Http\Controllers\API\V1\TaskController::class, 'getTaskDetails']);
    Route::post('store', [\App\Http\Controllers\API\V1\TaskController::class, 'store']);
    Route::post('update', [\App\Http\Controllers\API\V1\TaskController::class, 'update']);
    Route::post('update/status', [\App\Http\Controllers\API\V1\TaskController::class, 'updateTaskStatus']);
    Route::post('/update/comment', [\App\Http\Controllers\API\V1\TaskController::class, 'updateComment']);
    Route::post('pending', [\App\Http\Controllers\API\V1\TaskController::class, 'pending']);
    Route::post('daily', [\App\Http\Controllers\API\V1\TaskController::class, 'daily']);
    Route::post('users', [\App\Http\Controllers\API\V1\TaskController::class, 'getTaskUsers']);
    Route::post('users/assign', [\App\Http\Controllers\API\V1\TaskController::class, 'assignTaskToOther']);
    Route::post('users/multiple/assign', [\App\Http\Controllers\API\V1\TaskController::class, 'assignTaskToOther']);
    Route::post('delete', [\App\Http\Controllers\API\V1\TaskController::class, 'destroy']);
    Route::post('assigned-task-to-others', [\App\Http\Controllers\API\V1\TaskController::class, 'assignedTaskToOthers']);
    Route::group(['as' => 'comments.', 'prefix' => 'comments'], function () {
        Route::post('/users', [\App\Http\Controllers\API\V1\TaskCommentController::class, 'getTaskUsers'])->name('panel');
        Route::post('/get-by-officer-id', [\App\Http\Controllers\API\V1\TaskCommentController::class, 'getComments'])->name('get-by-officer-id');
        Route::post('/save', [\App\Http\Controllers\API\V1\TaskCommentController::class, 'saveComment'])->name('save');
    });
});
