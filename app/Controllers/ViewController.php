<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ViewController extends BaseController
{
    public function admin()
    {
        return view('pages/admin/dashboard'); // Ensure this matches your directory structure
    }
    
    public function owner()
    {
        return view('pages/admin/dashboard');    
    }
    public function petugas()
    {
        return view('pages/admin/dashboard');    
    }
}
