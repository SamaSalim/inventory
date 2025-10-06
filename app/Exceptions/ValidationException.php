<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    protected array $errors = [];

    public function __construct(array|string $errors, string $message = "Validation Failed", int $code = 0)
    {
        parent::__construct($message, $code);

        // إذا كانت رسالة واحدة، حولها إلى مصفوفة
        if (is_string($errors)) {
            $errors = [$errors];
        }

        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
