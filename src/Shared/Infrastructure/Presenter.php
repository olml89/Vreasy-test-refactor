<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Domain\Entity;
use App\Shared\Infrastructure\Mapper\EntityToPresenterMapper;
use JsonSerializable;
use Stringable;
use Tempest\Mapper\ObjectFactory;

final readonly class Presenter implements JsonSerializable, Stringable
{
    public function __construct(
        private ObjectFactory $objectFactory,
        private Entity $entity,
    ) {}

    public function jsonSerialize(): array
    {
        return $this->objectFactory->with(EntityToPresenterMapper::class)->map($this->entity, 'array');
    }

    public function __toString(): string
    {
        return json_encode($this->jsonSerialize(), JSON_PRETTY_PRINT);
    }
}