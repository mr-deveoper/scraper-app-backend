<?php
namespace App\Interfaces;

use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function all(): Collection;
    public function create(array $data): mixed;
}
