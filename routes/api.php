<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Sanctum-protected API routes (SPA/mobile). Use Accept: application/json and cookie or Bearer token.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
