<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Responses;

use Tempest\Validation\Rule;
use Throwable;

use function Tempest\Support\arr;

/**
 * @mixin JsonResponse
 * @mixin ErrorJsonResponse
 */
trait HasErrors
{
    private readonly Throwable $throwable;

    public function getGenericErrorMessage(): ?string
    {
        return $this->throwable->getMessage();
    }

    public function setErrorInformation(bool $isDebug = false): self
    {
        $this->body = [
            ...($this->body ?? []),
            ...$this->getContextualInformationFromException($isDebug)
        ];

        return $this;
    }

    private function getContextualInformationFromException(bool $isDebug): array
    {
        return !$isDebug ? ['message' => $this->getGenericErrorMessage()] :
            [
                'message' => $this->throwable->getMessage(),
                'code' => $this->throwable->getCode(),
                'file' => $this->throwable->getFile(),
                'line' => $this->throwable->getLine(),
                'trace' => arr($this->throwable->getTrace())
                    ->filter(fn (array $trace): bool => array_key_exists('file', $trace))
                    ->map(fn (array $trace): array => array_diff_key($trace, array_flip(['args'])))
                    ->values()
                    ->toArray(),
            ];
    }

    public function addFieldError(string $field, Rule $failedRule): self
    {
        $this->body['errors'][$field] ??= [];
        $this->body['errors'][$field][] = $failedRule->message();

        return $this;
    }
}