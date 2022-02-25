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
}
