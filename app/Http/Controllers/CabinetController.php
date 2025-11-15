<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class CabinetController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Cabinet/Index');
    }
}
