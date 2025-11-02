<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketStatusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::Resource('tickets', TicketController::class)->except(['update', 'destroy']);
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/d/mine', [TicketController::class, 'myTickets'])->name('tickets.mine');
    Route::get('/tickets/d/tasks', [TicketController::class, 'myTasks'])->name('tickets.tasks');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::patch('/tickets/{ticket}/category', [TicketController::class, 'updateCategory'])->name('tickets.updateCategory');

    Route::get('/users/autocomplete', [UserController::class, 'autocomplete'])->name('users.autocomplete');

    // Ticket status updates and comments
    Route::middleware(['can:update,ticket'])->group(function () {
        Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
        Route::post('/tickets/{ticket}/assignees', [TicketController::class, 'addAssignee'])->name('tickets.addAssignee');
        Route::delete('/tickets/{ticket}/assignees/{user}', [TicketController::class, 'removeAssignee'])->name('tickets.removeAssignee');
        Route::post('/tickets/{ticket}/comment', [TicketController::class, 'addComment'])->name('tickets.addComment');
    });
});

Route::middleware(['auth', 'verified', 'can:admin-only'])->group(function () {
    Route::resource('ticket-statuses', TicketStatusController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('users', UserController::class);    
    Route::resource('vendors', VendorController::class);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

require __DIR__.'/auth.php';
