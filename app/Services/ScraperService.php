<?php
namespace App\Services;

use GuzzleHttp\Client;
use App\Interfaces\ProductRepositoryInterface;

class ScraperService
{
    protected ProductRepositoryInterface $productRepo;

    public function __construct(ProductRepositoryInterface $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function scrape(string $url): void
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64)...',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)...',
        ];
        $ua = $userAgents[array_rand($userAgents)];

        $client = new Client([
            'headers' => ['User-Agent' => $ua],
        ]);

        $res = $client->get($url);
        $html = (string) $res->getBody();

        // TO-DO: Use DomCrawler or regex here
        $title = 'Parsed Title';
        $price = 'Parsed Price';
        $image = 'Parsed Image URL';

        $this->productRepo->create([
            'title' => $title,
            'price' => $price,
            'image_url' => $image,
        ]);
    }
}
