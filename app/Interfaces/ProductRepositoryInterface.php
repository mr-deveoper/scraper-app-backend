<?php
namespace App\Interfaces;

use App\Models\Product;

/**
 * Product Repository Interface
 * 
 * Defines the contract for product data access operations.
 * This interface follows the Repository pattern and ensures
 * consistent data access across the application.
 * 
 * @see ProductRepository
 */
interface ProductRepositoryInterface
{
    /**
     * Store or update a product in the database.
     * 
     * Uses the external_id as the unique identifier to determine
     * whether to create a new product or update an existing one.
     * 
     * @param array $data The product data to store
     * @return Product The created or updated product
     */
    public function storeOrUpdate(array $data): Product;

    /**
     * Get all products from the database.
     * 
     * Returns all products ordered by creation date (newest first).
     * 
     * @return \Illuminate\Support\Collection Collection of all products
     */
    public function all(): \Illuminate\Support\Collection;

    /**
     * Find a product by its external ID.
     * 
     * @param string $externalId The external ID to search for
     * @return Product|null The found product or null if not found
     */
    public function findByExternalId(string $externalId): ?Product;

    /**
     * Get product statistics.
     * 
     * @return array<string, mixed> Statistics about products
     */
    public function getStatistics(): array;
}
