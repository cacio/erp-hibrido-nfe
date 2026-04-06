<?php

namespace App\Controllers;

use App\Core\Controller;

class ChangeLogController extends Controller
{
    public function index()
    {
        $this->render('changelog/index');
    }
}