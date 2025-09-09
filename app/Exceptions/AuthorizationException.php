<?php

namespace App\Exceptions;

use CodeIgniter\Exceptions\PageNotFoundException;

class AuthorizationException extends PageNotFoundException
{
    protected $code = 403; // رمز الخطأ 403 Forbidden
    protected $message = 'أنت غير مصرح لك بالوصول إلى هذه الصفحة';
}
