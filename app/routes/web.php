<?php
use Illuminate\Support\Facades\Route;

// BrowserRouter direct loads must receive the SPA shell. API routes remain owned
// by routes/api.php, so this deliberately narrow fallback does not hide API 404s.
Route::get('/{path?}', fn () => response()->file(public_path('build/index.html')))
    ->where('path', '^(?!api(?:/|$)|build(?:/|$)).*');
