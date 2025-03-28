<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Services;

use App\City\Domain\CityName;
use App\City\Domain\Geolocation;
use App\City\Domain\GeolocationValidator;
use App\City\Domain\InvalidGeolocationException;
use App\City\Infrastructure\Services\GeoapiData\Feature;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final readonly class GeoapifyGeolocationValidator implements GeolocationValidator
{
    private const string API_URL = 'https://api.geoapify.com/v1/geocode/search?text=%s&apiKey=%s';

    public function __construct(
        private GeoapifyConfig $geoapifyConfig,
        private Client $httpClient,
    ) {}

    /**
     * @throws InvalidGeolocationException
     */
    public function validate(Geolocation $geolocation, CityName $cityName): void
    {
        try {
            $response = $this->httpClient->get(sprintf(self::API_URL, $cityName, $this->geoapifyConfig->apiKey));
            $body = json_decode((string)$response->getBody() ?: [], associative: true);
            $features = Feature::collect($body);

            foreach ($features as $feature) {
                $featureGeolocation = new Geolocation($feature->lat, $feature->lon);

                if ($featureGeolocation->equals($geolocation)) {
                    return;
                }
            }

            throw new InvalidGeolocationException($geolocation, $cityName);
        }
        catch (GuzzleException) {
            throw new InvalidGeolocationException($geolocation, $cityName);
        }
    }
}