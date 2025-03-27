<?php

declare(strict_types=1);

namespace App\Shared\Http\Responses;

use Tempest\Validation\Rule;
use Throwable;

use function Tempest\Support\arr;

/**
 * @mixin JsonResponse
 */
trait HasErrors
{
    protected function genericErrorMessage(): string
    {
        return $this->status->description();
    }

    public function setErrorInformation(?Throwable $e = null, bool $isDebug = false): self
    {
        $this->body = [
            ...($this->body ?? []),
            ...$this->getContextualInformationFromException($e, $isDebug)
        ];

        return $this;
    }

    private function getContextualInformationFromException(?Throwable $e, bool $isDebug): array
    {
        return !$isDebug || is_null($e) ? ['message' => $this->genericErrorMessage()] : [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => arr($e->getTrace())
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