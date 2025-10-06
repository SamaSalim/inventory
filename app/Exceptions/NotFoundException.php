<?php
namespace App\Exceptions;

use CodeIgniter\Exceptions\PageNotFoundException;


class NotFoundException extends PageNotFoundException
{
    public function __construct(
        string $message = "الصفحة غير موجودة",
        int $code = 404,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
