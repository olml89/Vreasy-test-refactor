<?php

declare(strict_types=1);

namespace App\City;

use Tempest\Database\DatabaseModel;
use Tempest\Mapper\ObjectFactory;
use Tempest\Router\Responses\Created;

final readonly class ResponseFactory
{
    public function __construct(
        private ObjectFactory $objectFactory,
    ) {}

    private function present(DatabaseModel $model): array
    {
        return $this->objectFactory->with(ModelToPresenterMapper::class)->map($model, 'array');
    }

    public function created(DatabaseModel $model): Created
    {
        return new Created($this->present($model));
    }
}