<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getPersonnes(): array
    {
        $response = $this->client->request(
            'GET',
            'https://randomuser.me/api/?nat=fr&results=10'
        );

        return $response->toArray();
    }

    public function getImagesFromUnsplashForBakery($count): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.unsplash.com/search/photos/?query=bakery&client_id=-ogJQxHldOFEjW5V8qQvsh2QawgmL3q0Eelrc6Yhc1M&count='.$count
        );

        return $response->toArray();
    }
}
