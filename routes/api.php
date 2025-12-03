<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\HeroSectionController as AdminHeroSectionController;
use App\Http\Controllers\Api\Admin\InstagramStoryController as AdminInstagramStoryController;
use App\Http\Controllers\Api\Admin\TourPackageController as AdminTourPackageController;
use App\Http\Controllers\Api\Front\HeroSectionController as PublicHeroSectionController;
use App\Http\Controllers\Api\Front\InstagramStoryController as PublicInstagramStoryController;
use App\Http\Controllers\Api\Front\TourController as PublicTourController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function (): void {
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::middleware('auth.admin')->group(function (): void {
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::apiResource('tours', AdminTourPackageController::class);
        Route::apiResource('instagram-stories', AdminInstagramStoryController::class)
            ->parameters(['instagram-stories' => 'story']);

        Route::get('hero-section', [AdminHeroSectionController::class, 'show']);
        Route::match(['put', 'patch'], 'hero-section', [AdminHeroSectionController::class, 'update']);
    });
});

Route::prefix('public')->group(function (): void {
    Route::get('tours', [PublicTourController::class, 'index']);
    Route::get('tours/{slug}', [PublicTourController::class, 'show']);
    Route::get('instagram-stories', [PublicInstagramStoryController::class, 'index']);
    Route::get('hero-section', [PublicHeroSectionController::class, 'show']);
});
