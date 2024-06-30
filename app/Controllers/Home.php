<?php

namespace App\Controllers;

use App\Models\GempaModel; // Tambahkan model

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }
}
