<?php

namespace App\services;

// API postal to INSEE : https://api.gouv.fr/documentation/api_carto_codes_postaux
// API INSEE to data : https://api.gouv.fr/documentation/api-geo
// EP =================== /commune/{code}

// HTPP CLIENT
// https://symfony.com/doc/current/http_client.html

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CityService
{

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getCity(string $postalCode, string $city): array
    {
        // on check si le nom de la ville correspond a un des element de la reponse
        // de la premiére API, si non on prend le premier element du tableau
        // $resp[0]
        
        $ukn = [
            'city' => 'unknown',
            'cp' => 'unknown',
            'lat' => '0',
            'lon' => '0',
            'departement' => 'unknown',
        ];
        $cities = [
            '15000' => [
                'city' => 'Aurillac',
                'cp' => '15000',
                'lat' => '0',
                'lon' => '0',
                'departement' => 'Cantal',
            ],
            '15100' => [
                'city' => 'Saint-Flour',
                'cp' => '15100',
                'lat' => '0',
                'lon' => '0',
                'departement' => 'Cantal',
            ],
            '63000' => [
                'city' => 'Clermond-Ferrand',
                'cp' => '63000',
                'lat' => '0',
                'lon' => '0',
                'departement' => 'Puy-de-Dôme',
            ],
        ];

        return $cities[$postalCode] ?? $ukn;
    }
}