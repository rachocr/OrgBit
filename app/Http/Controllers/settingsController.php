<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;
use Illuminate\Support\Facades\Session;

class settingsController extends Controller
{
    public function index()
    {
        // Get the organization ID from the session
        $orgId = Session::get('org_id');

        // Fetch the organization record
        $organization = Organization::find($orgId);

        // If organization is not found, redirect to login with an error message
        if (!$organization) {
            return redirect()->route('login')->withErrors(['error' => 'Organization not found.']);
        }

        // Pass the organization to the view
        return view('admin-side.settings', compact('organization'));
    }
}
