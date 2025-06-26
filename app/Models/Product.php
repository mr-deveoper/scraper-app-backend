<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Product Model
 * 
 * Represents a product scraped from external e-commerce platforms.
 * This model handles the storage and retrieval of product data
 * including title, price, image URL, and external identifier.
 * 
 * @property int $id
 * @property string $external_id Unique identifier from the source platform
 * @property string $title Product title/name
 * @property string $price Product price (stored as string to preserve formatting)
 * @property string $image_url URL to product image
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     * These fields can be filled using mass assignment methods like create() or fill().
     *
     * @var array<string>
     */
    protected $fillable = [
        'title', 
        'price', 
        'image_url', 
        'external_id'
    ];

    /**
     * The attributes that should be cast.
     * This ensures proper data type handling for database operations.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope a query to only include products from a specific platform.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $platform The platform name to filter by
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromPlatform($query, string $platform)
    {
        return $query->where('external_id', 'like', "%{$platform}%");
    }

    /**
     * Get the formatted price with currency symbol.
     * 
     * @return string The formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format((float) $this->price, 2);
    }

    /**
     * Get a truncated version of the title for display purposes.
     * 
     * @param int $length Maximum length of the title
     * @return string The truncated title
     */
    public function getShortTitleAttribute(int $length = 50): string
    {
        return strlen($this->title) > $length 
            ? substr($this->title, 0, $length) . '...' 
            : $this->title;
    }

    /**
     * Check if the product has a valid image URL.
     * 
     * @return bool True if the image URL is valid
     */
    public function hasValidImage(): bool
    {
        return !empty($this->image_url) && filter_var($this->image_url, FILTER_VALIDATE_URL);
    }

    /**
     * Check if the product has a valid price.
     * 
     * @return bool True if the price is valid and greater than zero
     */
    public function hasValidPrice(): bool
    {
        return !empty($this->price) && is_numeric($this->price) && (float) $this->price > 0;
    }
}
