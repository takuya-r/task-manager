<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome-custom');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
});

Route::middleware([
    EnsureFrontendRequestsAreStateful::class,
    'auth:sanctum',
    'web',
])->group(function () {
    Route::patch('/api/tasks/{task}/status', [TaskApiController::class, 'updateStatus'])
        ->name('api.tasks.updateStatus');
    Route::delete('/api/tasks/{task}', [TaskApiController::class, 'destroy'])
        ->name('api.tasks.destroy');
    Route::get('/api/tasks', [TaskApiController::class, 'filterByTag'])
        ->name('api.tasks');
});

require __DIR__.'/auth.php';
