<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class SupplierProfileController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Profile/Supplier');
    }
}
