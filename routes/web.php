<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketStatusController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::Resource('tickets', TicketController::class);
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
    Route::post('/tickets/{ticket}/comment', [TicketController::class, 'addComment'])->name('tickets.addComment');
    Route::post('/tickets/{ticket}/assignees', [TicketController::class, 'addAssignee'])->name('tickets.addAssignee');
    Route::delete('/tickets/{ticket}/assignees/{user}', [TicketController::class, 'removeAssignee'])->name('tickets.removeAssignee');
    Route::get('/users/autocomplete', [UserController::class, 'autocomplete'])->name('users.autocomplete');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/d/mine', [TicketController::class, 'myTickets'])->name('tickets.mine');
    Route::get('/tickets/d/tasks', [TicketController::class, 'myTasks'])->name('tickets.tasks');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::patch('/tickets/{ticket}/category', [TicketController::class, 'updateCategory'])->name('tickets.updateCategory');
});

Route::middleware(['auth', 'verified', 'can:admin-only'])->group(function () {
    Route::resource('ticket-statuses', TicketStatusController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('users', UserController::class);    
});



require __DIR__.'/auth.php';
