<?php
namespace App\Http\Controllers\Api;

use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Product API Controller
 * 
 * Handles HTTP requests for product-related operations.
 * This controller provides RESTful endpoints for managing products
 * and follows Laravel's API resource conventions.
 * 
 * Features:
 * - Product listing and retrieval
 * - Error handling and validation
 * - JSON response formatting
 * 
 * @see ProductRepositoryInterface
 */
class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @param ProductRepositoryInterface $repo The product repository
     */
    public function __construct(private ProductRepositoryInterface $repo) {}

    /**
     * Display a listing of products.
     * 
     * Returns all products in JSON format with optional filtering.
     * 
     * @param Request $request The HTTP request
     * @return JsonResponse JSON response containing products
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get all products from the repository
            $products = $this->repo->all();

            // Return successful response
            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products,
                    'count' => $products->count(),
                ],
                'message' => 'Products retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('Failed to retrieve products', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}