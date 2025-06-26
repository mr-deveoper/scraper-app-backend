<?php

namespace App\Exceptions;

use Exception;

/**
 * Unsupported Source Exception
 * 
 * Custom exception thrown when a scraper is requested for an unsupported URL.
 * This exception is used to indicate that no scraper is available to handle
 * a particular e-commerce platform or URL format.
 * 
 * Usage:
 * - Thrown by ScraperService when no scraper supports a given URL
 * - Can be caught and handled gracefully in controllers or commands
 * - Provides clear error messages for debugging
 * 
 * @see ScraperService
 */
class UnsupportedSourceException extends Exception
{
    /**
     * Create a new unsupported source exception instance.
     * 
     * @param string $message The error message
     * @param int $code The error code
     * @param Exception|null $previous The previous exception
     */
    public function __construct(string $message = '', int $code = 0, Exception $previous = null)
    {
        // Set a default message if none provided
        if (empty($message)) {
            $message = 'The provided URL is not supported by any available scraper.';
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the exception context for logging.
     * 
     * @return array<string, mixed> Context information for logging
     */
    public function getContext(): array
    {
        return [
            'exception_class' => static::class,
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }
}
