<?php

namespace App\Exceptions;

class AuthenticationException extends \RuntimeException
{
    public function __construct(
        string $message = "يجب تسجيل الدخول للوصول إلى هذه الصفحة",
        int $code = 401,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
