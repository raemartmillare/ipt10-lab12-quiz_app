<?php 

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index()
    {
        $this->initializeSession();
        session_destroy();

        return $this->render('home');
    }
}
