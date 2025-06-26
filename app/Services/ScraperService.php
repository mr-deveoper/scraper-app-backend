<?php
namespace App\Services;

use App\Services\Scrapers\ScraperInterface;

/**
 * Scraper Service
 * 
 * Main service for managing and coordinating web scrapers.
 * This service implements the Factory pattern to create appropriate
 * scrapers based on the URL and provides a unified interface for
 * scraping operations.
 * 
 * Features:
 * - Automatic scraper selection based on URL
 * - Error handling and validation
 * - Support for multiple scraping platforms
 * 
 * @see ScraperInterface
 */
class ScraperService
{
    /**
     * Collection of available scrapers.
     * 
     * @var array<ScraperInterface>
     */
    protected array $scrapers;

    /**
     * Create a new scraper service instance.
     * 
     * @param iterable<ScraperInterface> $scrapers The scrapers to register
     */
    public function __construct(iterable $scrapers)
    {
        $this->scrapers = $scrapers;
    }

    /**
     * Get the appropriate scraper for the given URL.
     * 
     * Iterates through all registered scrapers to find one that
     * supports the provided URL.
     * 
     * @param string $url The URL to scrape
     * @return ScraperInterface The appropriate scraper
     * @throws \Exception When no scraper supports the URL
     */
    public function getScraper(string $url): ScraperInterface
    {
        // Validate URL format
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Invalid URL provided: {$url}");
        }

        // Find a scraper that supports this URL
        foreach ($this->scrapers as $scraper) {
            if ($scraper->supports($url)) {
                return $scraper;
            }
        }

        // If no scraper is found, throw an exception
        throw new \Exception("No scraper found for this URL: {$url}");
    }

    /**
     * Get all registered scrapers.
     * 
     * @return array<ScraperInterface> Array of all registered scrapers
     */
    public function getAllScrapers(): array
    {
        return $this->scrapers;
    }

    /**
     * Check if a URL is supported by any scraper.
     * 
     * @param string $url The URL to check
     * @return bool True if supported, false otherwise
     */
    public function isUrlSupported(string $url): bool
    {
        try {
            foreach ($this->scrapers as $scraper) {
                if ($scraper->supports($url)) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the number of registered scrapers.
     * 
     * @return int Number of scrapers
     */
    public function getScraperCount(): int
    {
        return count($this->scrapers);
    }
}