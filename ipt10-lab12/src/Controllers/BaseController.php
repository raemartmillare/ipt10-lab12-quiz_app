<?php

namespace App\Controllers;

use App\Traits\Renderable;

class BaseController
{
    use Renderable;

    protected $session_started = false;

    protected function initializeSession()
    {
        if (!$this->session_started && session_status() == PHP_SESSION_NONE) {
            session_start();
            $this->session_started = true;
        }
    }
}