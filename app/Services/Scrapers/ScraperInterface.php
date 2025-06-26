<?php

namespace App\Services\Scrapers;

/**
 * Scraper Interface
 * 
 * Defines the contract for all web scrapers in the application.
 * This interface follows the Strategy pattern and ensures that
 * all scrapers implement the same methods for consistency.
 * 
 * Each scraper should be responsible for scraping a specific
 * e-commerce platform and converting the scraped data into
 * a standardized format.
 * 
 * @see AmazonScraper
 * @see JumiaScraper
 */
interface ScraperInterface
{
    /**
     * Check if this scraper supports the given URL.
     * 
     * This method should analyze the URL and determine if the scraper
     * can handle scraping from this particular e-commerce platform.
     * 
     * @param string $url The URL to check for compatibility
     * @return bool True if this scraper can handle the URL, false otherwise
     */
    public function supports(string $url): bool;

    /**
     * Scrape a single product from the given URL.
     * 
     * This method should extract all relevant product information
     * from a product detail page and return it in a standardized format.
     * 
     * @param string $url The product URL to scrape
     * @return array The scraped product data with the following structure:
     *               - external_id: string (unique identifier from the platform)
     *               - title: string (product title/name)
     *               - price: string (product price)
     *               - image_url: string (URL to product image)
     * @throws \Exception When scraping fails due to network issues, 
     *                    blocked requests, or changed page structure
     */
    public function scrapeProduct(string $url): array;

    /**
     * Scrape all product URLs from a category page.
     * 
     * This method should extract all product links from a category
     * or search results page and return them as an array of URLs.
     * 
     * @param string $url The category URL to scrape
     * @return array Array of product URLs found on the page
     * @throws \Exception When scraping fails due to network issues,
     *                    blocked requests, or changed page structure
     */
    public function scrapeCategory(string $url): array;
}
