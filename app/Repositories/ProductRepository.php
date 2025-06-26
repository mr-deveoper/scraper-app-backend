<?php
namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

/**
 * Product Repository
 * 
 * Handles all database operations for the Product model.
 * This repository implements the Repository pattern and provides
 * a clean interface for product data access and manipulation.
 * 
 * Features:
 * - CRUD operations for products
 * - Data validation and error handling
 * - Performance optimization with proper queries
 * 
 * @see ProductRepositoryInterface
 * @see Product
 */
class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Store or update a product in the database.
     * 
     * Uses the external_id as the unique identifier to determine
     * whether to create a new product or update an existing one.
     * 
     * @param array $data The product data to store
     * @return Product The created or updated product
     * @throws \Exception When database operation fails
     */
    public function storeOrUpdate(array $data): Product
    {
        try {
            // Validate required fields
            $this->validateProductData($data);
            
            // Store or update the product
            $product = Product::updateOrCreate(
                ['external_id' => $data['external_id']],
                $data
            );

            return $product;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('Failed to store/update product', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get all products from the database.
     * 
     * Returns all products ordered by creation date (newest first).
     * 
     * @return \Illuminate\Support\Collection Collection of all products
     */
    public function all(): \Illuminate\Support\Collection
    {
        return Product::latest()->get();
    }

    /**
     * Validate product data before storage.
     * 
     * Ensures that all required fields are present and valid.
     * 
     * @param array $data The product data to validate
     * @throws \InvalidArgumentException When data is invalid
     */
    private function validateProductData(array $data): void
    {
        // Check for required fields
        $requiredFields = ['external_id', 'title'];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Required field '{$field}' is missing or empty");
            }
        }

        // Validate external_id format
        if (!is_string($data['external_id']) || strlen($data['external_id']) < 1) {
            throw new \InvalidArgumentException('External ID must be a non-empty string');
        }

        // Validate title format
        if (!is_string($data['title']) || strlen(trim($data['title'])) < 1) {
            throw new \InvalidArgumentException('Title must be a non-empty string');
        }

        // Validate price if provided
        if (isset($data['price']) && !empty($data['price'])) {
            if (!is_numeric($data['price']) && !is_string($data['price'])) {
                throw new \InvalidArgumentException('Price must be a numeric value or string');
            }
        }

        // Validate image_url if provided
        if (isset($data['image_url']) && !empty($data['image_url'])) {
            if (!filter_var($data['image_url'], FILTER_VALIDATE_URL)) {
                throw new \InvalidArgumentException('Image URL must be a valid URL');
            }
        }
    }

    /**
     * Find a product by its external ID.
     * 
     * @param string $externalId The external ID to search for
     * @return Product|null The found product or null if not found
     */
    public function findByExternalId(string $externalId): ?Product
    {
        return Product::where('external_id', $externalId)->first();
    }

    /**
     * Get products with pagination.
     * 
     * @param int $perPage Number of items per page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return Product::latest()->paginate($perPage);
    }

    /**
     * Search products by title.
     * 
     * @param string $searchTerm The search term
     * @return \Illuminate\Support\Collection Collection of matching products
     */
    public function searchByTitle(string $searchTerm): \Illuminate\Support\Collection
    {
        return Product::where('title', 'like', "%{$searchTerm}%")
            ->latest()
            ->get();
    }

    /**
     * Get product statistics.
     * 
     * @return array<string, mixed> Statistics about products
     */
    public function getStatistics(): array
    {
        return [
            'total_products' => Product::count(),
            'latest_product' => Product::latest()->first(),
            'oldest_product' => Product::oldest()->first(),
        ];
    }
}
