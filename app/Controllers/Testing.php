<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Testing extends BaseController
{
    public function index()
    {
        return view('home');
    }
}
