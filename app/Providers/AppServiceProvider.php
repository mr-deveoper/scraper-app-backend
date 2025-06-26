<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Services\Scrapers\AmazonScraper;
use App\Services\Scrapers\JumiaScraper;
use App\Services\ScraperService;
use Illuminate\Support\Facades\Schema;

/**
 * Application Service Provider
 * 
 * Main service provider for the application that handles dependency injection
 * and service registration. This provider binds interfaces to their implementations
 * and registers singleton services used throughout the application.
 * 
 * Features:
 * - Repository pattern bindings
 * - Scraper service registration
 * - Service container configuration
 * 
 * @see ProductRepositoryInterface
 * @see ProductRepository
 * @see ScraperService
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * 
     * This method is called during the service container bootstrapping process.
     * It's used to bind interfaces to their concrete implementations and register
     * services that should be available throughout the application.
     * 
     * @return void
     */
    public function register(): void
    {
        // Bind the product repository interface to its implementation
        // This allows for dependency injection and easier testing
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);

        // Register the scraper service as a singleton
        // This ensures the same scraper service instance is used throughout the app
        $this->app->singleton('scraper.service', function () {
            // Create instances of all available scrapers
            $scrapers = [
                new AmazonScraper(),
                new JumiaScraper(),
            ];

            // Return a new scraper service with all scrapers
            return new ScraperService($scrapers);
        });
    }

    /**
     * Bootstrap any application services.
     * 
     * This method is called after all service providers have been registered.
     * It's typically used for any bootstrapping that requires other services
     * to be available.
     * 
     * @return void
     */
    public function boot(): void
    {
        // Any bootstrapping logic can be added here
        // For example, setting up global configurations, registering observers, etc.
        Schema::defaultStringLength(191);
    }
}
