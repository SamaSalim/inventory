<?php

namespace App\Controllers; 

use App\Exceptions\AuthenticationException;


class assets extends BaseController
{

     public function dashboard(): string // Assets view 
    {
        // exception handling
          if (! session()->get('isLoggedIn')) {
        throw new AuthenticationException();
    }

        return view('assets_dashboard');
    }

}