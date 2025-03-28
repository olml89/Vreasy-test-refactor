<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Responses;

use Tempest\Http\Status;
use Tempest\Validation\Exceptions\ValidationException;

final class UnprocessableEntity extends JsonResponse implements ErrorJsonResponse
{
    use HasErrors;

    public function __construct(ValidationException $validationException)
    {
        parent::__construct(Status::UNPROCESSABLE_CONTENT);
        $this->throwable = $validationException;

        $this->setErrorInformation();

        foreach ($validationException->failingRules as $field => $rules) {
            foreach ($rules as $rule) {
                $this->addFieldError($field, $rule);
            }
        }
    }

    public function getGenericErrorMessage(): string
    {
        return 'Validation error';
    }
}