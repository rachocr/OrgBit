<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;

class OrganizationController extends Controller
{
    public function index()
    {
        // Fetch all organizations from the database
        $organizations = Organization::all();

        // Pass organizations data to the Blade view
        return view('student-side.organizations', compact('organizations'));
    }
}