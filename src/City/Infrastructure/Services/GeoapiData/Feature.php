<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Services\GeoapiData;

use function Tempest\Support\arr;

final readonly class Feature
{
    public function __construct(
        public float $lat,
        public float $lon,
    ) {}

    /**
     * @return self[]
     */
    public static function collect(array $data): array
    {
        return arr($data['features'] ?? [])
            ->map(fn(array $featureData): Feature => self::fromFeatureData($featureData))
            ->filter()
            ->toArray();
    }

    private static function fromFeatureData(array $featureData): ?Feature
    {
        $lat = $featureData['properties']['lat'] ?? null;
        $lon = $featureData['properties']['lon'] ?? null;

        if (is_null($lat) || is_null($lon)) {
            return null;
        }

        return new Feature($lat, $lon);
    }
}