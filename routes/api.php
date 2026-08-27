<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NPC API Routes — Laravel 13
|--------------------------------------------------------------------------
*/

/* ═══════════════════════════════════════════════
   PUBLIC — Authentication
═══════════════════════════════════════════════ */
Route::prefix('auth')->group(function () {
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

/* ═══════════════════════════════════════════════
   PUBLIC — Services (by slug)
═══════════════════════════════════════════════ */
Route::get('/services',         [ServiceController::class, 'index']);
Route::get('/services/{service:slug}', [ServiceController::class, 'show']);

/* ═══════════════════════════════════════════════
   PUBLIC — Works (by slug)
═══════════════════════════════════════════════ */
Route::get('/works',            [WorkController::class, 'index']);
Route::get('/works/{work:slug}', [WorkController::class, 'show']);

/* ═══════════════════════════════════════════════
   PUBLIC — Tags listing (for filter/display)
═══════════════════════════════════════════════ */
Route::get('/tags', [TagController::class, 'index']);

/* ═══════════════════════════════════════════════
   AUTHENTICATED — Requires valid Sanctum token
═══════════════════════════════════════════════ */
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // User portal
    Route::get('/user/dashboard', function () {
        return response()->json([
            'message' => 'Selamat datang di Client Portal NPC',
            'section' => 'user',
            'user'    => auth()->user()->only(['id', 'name', 'email', 'role']),
        ]);
    });

    /* ── ADMIN ONLY ─────────────────────────────── */
    Route::middleware(AdminMiddleware::class)->prefix('admin')->group(function () {

        Route::get('/dashboard', function () {
            return response()->json([
                'message' => 'Selamat datang di Admin Dashboard NPC',
                'section' => 'admin',
                'user'    => auth()->user()->only(['id', 'name', 'email', 'role']),
            ]);
        });

        // Users CRUD
        Route::apiResource('users', UserController::class);

        // Services CRUD (admin sees all including unpublished)
        Route::get('services',                 [ServiceController::class, 'adminIndex']);
        Route::post('services',                [ServiceController::class, 'store']);
        Route::put('services/{service}',       [ServiceController::class, 'update']);
        Route::patch('services/{service}',     [ServiceController::class, 'update']);
        Route::delete('services/{service}',    [ServiceController::class, 'destroy']);

        // Works CRUD (admin sees all) — supports multipart/form-data for image upload
        Route::get('works',                               [WorkController::class, 'adminIndex']);
        Route::post('works',                              [WorkController::class, 'store']);
        Route::put('works/{work}',                        [WorkController::class, 'update']);
        Route::patch('works/{work}',                      [WorkController::class, 'update']);
        Route::delete('works/{work}',                     [WorkController::class, 'destroy']);
        Route::delete('works/{work}/media/{media}',       [WorkController::class, 'destroyMedia']);

        // Tags CRUD
        Route::get('tags',                     [TagController::class, 'index']);
        Route::post('tags',                    [TagController::class, 'store']);
        Route::delete('tags/{tag}',            [TagController::class, 'destroy']);
    });
});
