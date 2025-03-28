<?php

declare(strict_types=1);

use App\City\Infrastructure\Services\GeoapifyConfig;

use function Tempest\env;

return new GeoapifyConfig(
    apiKey: env('GEOAPIFY_API_KEY'),
);