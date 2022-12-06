<?php

namespace App\services;

//use App\services\Contracts\HttpClient;


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
     * App\services\Contracts\HttpClient;
     */
    private HttpClientInterface $client;


    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;

    }

    public function normalizer(string $input): string
    {
        // Aurillac => aurillac => auRillac => aurillaC
        // Saint-Flour => Saint Flour => saint flour => saint,flour => saint-flour
        // todo : enlever les accent, passer tout en strtolower, remove [a-z0-9] => ' '
        return $input;
    }

    public function getCity(string $postalCode, string $city): array
    {
        $response = $this->client->request(
            'GET',
            'https://apicarto.ign.fr/api/codes-postaux/communes/'.$postalCode
        );
        //  'https://geo.api.gouv.fr/communes?codePostal=$postalCode'

        //$statusCode = $response->getStatusCode();
        // $statusCode = 200
        //$contentType = $response->getHeaders()['content-type'][0];
        // $contentType = 'application/json'
        //$content = $response->getContent();
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
        $contentData = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]

        if (!is_array($contentData)) {
            throw new \RuntimeException('bad format data');
        }


        $cityFound = null;
        $normalizedCity = $this->normalizer($city);
        // todo un foreach $contentData et check si on a la ville, si on trouve alors on stock dans $cityFound
        // foreach
        // ensuite
        if ($cityFound === null) {
            $cityFound = $contentData[0] ?? null;
        }

        if ($cityFound === null) {
            throw new \RuntimeException('no data found');
        }

        $codeInsee = $cityFound['codeCommune'];

        /// todo 2eme call API

        $urlApiCommune = 'https://geo.api.gouv.fr/communes/'.$codeInsee
            .'?fields=nom,code,codesPostaux,siren,codeEpci,codeDepartement,codeRegion,population,departement&format=json&geometry=centre';


        // todo si jamais la reponse est bonne alors on return la reponse de l'api

        // todo si non alors on return UKN

        return [
            'city' => 'unknown',
            'cp' => 'unknown',
            'lat' => '0',
            'lon' => '0',
            'departement' => 'unknown',
        ];
    }


}