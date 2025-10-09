<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

         // Debug مؤقت: تأكيد حالة الجلسة
    log_message('debug', 'Filter check - isLoggedIn: ' . print_r($session->get('isLoggedIn'), true));

        
        // التحقق من تسجيل الدخول
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        // التحقق من الصلاحيات إذا تم تمرير arguments
        if ($arguments && !in_array($session->get('role'), $arguments)) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // لا حاجة لأي إجراء بعد التنفيذ
    }
}
