<?php
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
Route::get('/tickets', [TicketController::class, 'index']);
Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->whereNumber('ticket');
Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->whereNumber('ticket');
Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->whereNumber('ticket');
Route::get('/customers', [CustomerController::class, 'index']);
Route::post('/customers/{customer}/projects', [ProjectController::class, 'store'])->whereNumber('customer');
Route::patch('/tickets/{ticket}/status', [TicketController::class, 'transition'])->whereNumber('ticket');
Route::get('/lab/fail', [TicketController::class, 'controlledFailure']);
