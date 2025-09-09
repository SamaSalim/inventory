<?php

namespace App\Libraries;

use CodeIgniter\Debug\BaseExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

// Default exception handeling
use CodeIgniter\Exceptions\PageNotFoundException;


// استيراد الاستثناءات المخصصة
use App\Exceptions\{
    AuthenticationException,
    ValidationException,
};

class MyExceptionHandler extends BaseExceptionHandler implements ExceptionHandlerInterface
{
    protected ?string $viewPath = APPPATH . 'Views/errors/custom/';

    public function handle(
        Throwable $exception,
        RequestInterface $request,
        ResponseInterface $response,
        int $statusCode,
        int $exitCode,
    ): void {


        
        // خريطة بين الاستثناءات وملفات العرض
        $exceptionViews = [
            // NotFoundException::class       => 'not_found.php',
            PageNotFoundException::class => 'not_found.php',
            AuthenticationException::class => 'unauthorized.php',
            ValidationException::class => 'validation_error.php',
            
        ];

        // حالة خاصة للـ API
        if ($exception instanceof ApiException) {
            $response->setStatusCode($statusCode)
                     ->setJSON(['error' => $exception->getMessage()])
                     ->send();
            exit($exitCode);
        }

        // تحديد ملف العرض المناسب أو الافتراضي
        $viewFile = $this->viewPath . (
            $exceptionViews[get_class($exception)] ?? 'error_' . $statusCode . '.php'
        );

        // عرض الصفحة
        $this->render($exception, $statusCode, $viewFile);
        exit($exitCode);
    }
}
