<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Domain\Entity;
use Tempest\Mapper\ObjectFactory;

final readonly class PresenterFactory
{
    public function __construct(
        private ObjectFactory $objectFactory,
    ) {}

    public function present(Entity $entity): Presenter
    {
        return new Presenter($this->objectFactory, $entity);
    }
}