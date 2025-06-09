<?php
namespace App\Controllers;

use Core\Controller;

class LandingController extends Controller
{
    public function index()
    {
        $this->view('landing/index', [
            'title' => 'Selamat Datang di CMS Sederhana'
        ]);
    }
} 