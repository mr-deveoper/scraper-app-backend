<?php

namespace App\Services\Scrapers;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Http;
use App\Helpers\ProxyHelper;

/**
 * Amazon Scraper
 * 
 * Handles scraping of product data from Amazon.com.
 * This scraper implements the Strategy pattern and provides
 * methods to extract product information from Amazon product pages.
 * 
 * Features:
 * - Product data extraction (title, price, image)
 * - Category page parsing for product URLs
 * - Proxy support for avoiding rate limiting
 * - Error handling for blocked requests
 * 
 * @see ScraperInterface
 */
class AmazonScraper implements ScraperInterface
{
    /**
     * Check if this scraper supports the given URL.
     * 
     * Determines if the URL is from Amazon.com and can be scraped.
     * 
     * @param string $url The URL to check
     * @return bool True if this scraper can handle the URL
     */
    public function supports(string $url): bool
    {
        return str_contains($url, 'amazon.');
    }

    /**
     * Scrape a single product from the given URL.
     * 
     * Extracts product title, price, and image URL from an Amazon product page.
     * 
     * @param string $url The product URL to scrape
     * @return array The scraped product data
     * @throws \Exception When product data cannot be found or scraping fails
     */
    public function scrapeProduct(string $url): array
    {
        // Fetch and parse the product page
        $crawler = $this->fetch($url);

        // Extract product information using CSS selectors
        $titleNode = $crawler->filter('#productTitle');
        $priceNode = $crawler->filter('.a-price .a-offscreen');
        $imageNode = $crawler->filter('#landingImage');

        // Validate that all required elements are found
        if ($titleNode->count() === 0 || $priceNode->count() === 0 || $imageNode->count() === 0) {
            throw new \Exception('Product data not found. Amazon may have blocked the request or changed layout.');
        }

        // Return standardized product data
        return [
            'external_id' => $this->extractId($url),
            'title' => trim($titleNode->text()),
            'price' => trim($priceNode->first()->text()),
            'image_url' => $imageNode->attr('src'),
        ];
    }

    /**
     * Scrape all product URLs from a category page.
     * 
     * Extracts product links from Amazon search results or category pages.
     * 
     * @param string $url The category URL to scrape
     * @return array Array of product URLs found on the page
     */
    public function scrapeCategory(string $url): array
    {
        // Fetch and parse the category page
        $crawler = $this->fetch($url);

        // Extract product URLs from search results
        return array_filter($crawler->filter('.s-result-item')->each(function (Crawler $node) {
            $linkNode = $node->filter('a.a-link-normal');
            if ($linkNode->count()) {
                $href = $linkNode->attr('href');
                // Only include URLs that contain product identifiers
                if (str_contains($href, '/dp/')) {
                    return 'https://www.amazon.com' . strtok($href, '?');
                }
            }
            return null;
        }));
    }

    /**
     * Fetch and parse a web page using HTTP client.
     * 
     * Handles HTTP requests with proper headers, proxy support, and error handling.
     * 
     * @param string $url The URL to fetch
     * @return Crawler The parsed DOM crawler
     * @throws \Exception When the HTTP request fails
     */
    private function fetch(string $url): Crawler
    {
        // Get a random proxy to avoid rate limiting
        $proxy = ProxyHelper::getRandom();

        // Configure HTTP client with browser-like headers
        $client = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Connection' => 'keep-alive',
        ])->timeout(15)->withOptions([
            'verify' => false,
            'proxy' => $proxy,
        ]);

        // Make the HTTP request
        $response = $client->get($url);

        // Check if the request was successful
        if (!$response->successful()) {
            throw new \Exception("Failed to fetch URL: {$url}. Status: {$response->status()}");
        }

        // Return parsed DOM crawler
        return new Crawler($response->body());
    }

    /**
     * Extract product ID from Amazon URL.
     * 
     * Amazon product URLs contain a unique identifier in the format /dp/PRODUCT_ID.
     * This method extracts that ID or generates a fallback hash.
     * 
     * @param string $url The Amazon product URL
     * @return string The extracted product ID or URL hash
     */
    private function extractId(string $url): string
    {
        // Extract ID from /dp/ pattern in Amazon URLs
        preg_match('/\/dp\/([^\/\?]+)/', $url, $matches);
        
        // Return extracted ID or fallback to URL hash
        return $matches[1] ?? md5($url);
    }
}
