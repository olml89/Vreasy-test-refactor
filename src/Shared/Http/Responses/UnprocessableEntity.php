<?php

declare(strict_types=1);

namespace App\Shared\Http\Responses;

use Tempest\Http\Status;
use Tempest\Validation\Exceptions\ValidationException;

final class UnprocessableEntity extends JsonResponse
{
    use HasErrors;

    public function __construct(ValidationException $validationException)
    {
        parent::__construct(Status::UNPROCESSABLE_CONTENT);

        $this->setErrorInformation();

        foreach ($validationException->failingRules as $field => $rules) {
            foreach ($rules as $rule) {
                $this->addFieldError($field, $rule);
            }
        }
    }

    protected function genericErrorMessage(): string
    {
        return 'Validation error';
    }
}