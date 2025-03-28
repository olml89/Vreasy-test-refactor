<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Console;

use App\City\Application\CreateCity;
use App\City\Domain\CityName;
use App\City\Domain\Geolocation;
use App\Shared\Infrastructure\PresenterFactory;
use Tempest\Console\Console;
use Tempest\Console\ConsoleCommand;

final readonly class CreateCityCommand
{
    public function __construct(
        private CreateCity $createCity,
        private Console $console,
        private PresenterFactory $presenterFactory,
    ) {}

    #[ConsoleCommand(
        name: 'app:city:create',
        description: 'It creates a city',
    )]
    public function __invoke(string $name, float $latitude, float $longitude): void
    {
        $city = $this->createCity->create(
            name: new CityName($name),
            geolocation: new Geolocation(latitude: $latitude, longitude: $longitude),
        );

        $this->console->success('City has been created');
        $this->console->writeln((string) $this->presenterFactory->present($city));
    }
}