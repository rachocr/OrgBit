<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\Member;
use App\Models\Student;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    /**
     * Display a listing of the members for the current organization.
     */
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
    
        // Fetch member applications (Non-Members: position_id = 1)
        $applications = Member::where('org_id', $orgId)
            ->where('position_id', 1) // Non-Members
            ->with('student') // Eager load the student relationship
            ->get();
    
        // Fetch list of members (Members, Committee, Executive: position_id != 1)
        $members = Member::where('org_id', $orgId)
            ->where('position_id', '!=', 1) // Exclude Non-Members
            ->with('student') // Eager load the student relationship
            ->get();
    
        // Pass the organization, applications, and members to the view
        return view('admin-side.members', compact('organization', 'applications', 'members'));
    }

    public function approve($id)
    {
        $application = Member::find($id);
        if ($application) {
            $application->update(['position_id' => 2]);
            return redirect()->back()->with('success', 'Application approved.');
        }
        return redirect()->back()->withErrors(['error' => 'Application not found.']);
    }
    
    public function reject($id)
    {
        $application = Member::find($id);
        if ($application) {
            $application->delete();
            return redirect()->back()->with('success', 'Application rejected.');
        }
        return redirect()->back()->withErrors(['error' => 'Application not found.']);
    }

    public function updatePosition(Request $request, $id)
    {
        $member = Member::find($id);
        if ($member) {
            $member->update(['position_id' => $request->position_id]);
            return response()->json(['success' => 'Position updated successfully.']);
        }
        return response()->json(['error' => 'Member not found.'], 404);
    }
    public function remove($id)
    {
        $member = Member::find($id);
        if ($member) {
            $member->delete();
            return response()->json(['success' => 'Member removed successfully.']);
        }
        return response()->json(['error' => 'Member not found.'], 404);
    }
}