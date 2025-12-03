<?php

namespace App\Providers;

use App\Repositories\Contracts\AdminRepositoryContract;
use App\Repositories\Contracts\HeroSectionRepositoryContract;
use App\Repositories\Contracts\InstagramStoryRepositoryContract;
use App\Repositories\Contracts\TourPackageRepositoryContract;
use App\Repositories\Eloquent\EloquentAdminRepository;
use App\Repositories\Eloquent\EloquentHeroSectionRepository;
use App\Repositories\Eloquent\EloquentInstagramStoryRepository;
use App\Repositories\Eloquent\EloquentTourPackageRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AdminRepositoryContract::class, EloquentAdminRepository::class);
        $this->app->bind(TourPackageRepositoryContract::class, EloquentTourPackageRepository::class);
        $this->app->bind(InstagramStoryRepositoryContract::class, EloquentInstagramStoryRepository::class);
        $this->app->bind(HeroSectionRepositoryContract::class, EloquentHeroSectionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
