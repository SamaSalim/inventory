<?php

namespace App\Controllers; 

use App\Exceptions\AuthenticationException;


class AssetsController extends BaseController
{

     public function dashboard(): string // Assets view 
    {
        // exception handling
          if (! session()->get('isLoggedIn')) {
        throw new AuthenticationException();
    }

        return view('assets/assets_dashboard');
    }

}