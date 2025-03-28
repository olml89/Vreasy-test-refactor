<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Database;

use App\Shared\Domain\StringValueObject;
use App\Shared\Infrastructure\Mapper\Casters\StringValueObjectCaster;
use App\Shared\Infrastructure\Mapper\Casters\UuidCaster;
use Ramsey\Uuid\UuidInterface;
use Tempest\Core\KernelEvent;
use Tempest\EventBus\EventHandler;
use Tempest\Mapper\CasterFactory;

final readonly class CustomCastersRegisterer
{
    public function __construct(
        private CasterFactory $casterFactory,
    ) {}

    #[EventHandler(KernelEvent::BOOTED)]
    public function _invoke(): void
    {
        $this->casterFactory->addCaster(UuidInterface::class, UuidCaster::class);
        $this->casterFactory->addCaster(StringValueObject::class, StringValueObjectCaster::fromProperty(...));
    }
}