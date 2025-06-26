<?php
namespace App\Helpers;

/**
 * Proxy Helper
 * 
 * Utility class for managing proxy servers used in web scraping.
 * This helper provides methods to retrieve and manage proxy configurations
 * to avoid rate limiting and IP blocking during scraping operations.
 * 
 * Features:
 * - Random proxy selection
 * - Proxy file management
 * - Error handling for missing proxy files
 * 
 * Note: This helper expects a proxies.txt file in the storage/app directory
 * containing one proxy per line in the format: protocol://host:port
 */
class ProxyHelper
{
    /**
     * Get a random proxy from the proxy list.
     * 
     * Reads the proxy list from storage/app/proxies.txt and returns
     * a randomly selected proxy. If no proxies are available or the
     * file doesn't exist, returns null.
     * 
     * @return string|null The proxy string in format protocol://host:port or null if none available
     */
    public static function getRandom(): ?string
    {
        try {
            // Define the path to the proxy file
            $proxyFile = storage_path('app/proxies.txt');

            // Check if the proxy file exists
            if (!file_exists($proxyFile)) {
                \Illuminate\Support\Facades\Log::warning('Proxy file not found', [
                    'file_path' => $proxyFile
                ]);
                return null;
            }

            // Read all proxy lines from the file
            $proxies = file($proxyFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            // Check if any proxies were found
            if (empty($proxies)) {
                \Illuminate\Support\Facades\Log::warning('No proxies found in proxy file', [
                    'file_path' => $proxyFile
                ]);
                return null;
            }

            // Select a random proxy from the list
            $randomProxy = $proxies[array_rand($proxies)];

            // Validate the proxy format
            if (!self::isValidProxy($randomProxy)) {
                \Illuminate\Support\Facades\Log::warning('Invalid proxy format found', [
                    'proxy' => $randomProxy
                ]);
                return null;
            }

            \Illuminate\Support\Facades\Log::debug('Selected proxy', [
                'proxy' => $randomProxy
            ]);

            return $randomProxy;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error reading proxy file', [
                'error' => $e->getMessage(),
                'file_path' => $proxyFile ?? 'unknown'
            ]);
            return null;
        }
    }

    /**
     * Get all available proxies.
     * 
     * Returns an array of all proxies from the proxy file.
     * 
     * @return array<string> Array of proxy strings
     */
    public static function getAll(): array
    {
        try {
            $proxyFile = storage_path('app/proxies.txt');

            if (!file_exists($proxyFile)) {
                return [];
            }

            $proxies = file($proxyFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            // Filter out invalid proxies
            return array_filter($proxies, [self::class, 'isValidProxy']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error reading all proxies', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get the number of available proxies.
     * 
     * @return int Number of available proxies
     */
    public static function getCount(): int
    {
        return count(self::getAll());
    }

    /**
     * Check if a proxy string is valid.
     * 
     * Validates that the proxy string follows the expected format.
     * 
     * @param string $proxy The proxy string to validate
     * @return bool True if the proxy is valid
     */
    private static function isValidProxy(string $proxy): bool
    {
        // Remove whitespace
        $proxy = trim($proxy);

        // Check if proxy is not empty
        if (empty($proxy)) {
            return false;
        }

        // Basic format validation: should contain protocol://host:port
        if (!preg_match('/^https?:\/\/[^:]+:\d+$/', $proxy)) {
            return false;
        }

        return true;
    }

    /**
     * Check if proxy support is available.
     * 
     * @return bool True if proxies are available
     */
    public static function isAvailable(): bool
    {
        return self::getCount() > 0;
    }
}
