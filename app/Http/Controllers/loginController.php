<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Organization;
use App\Models\Member; // Import the Member model

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);

        // Check if the email exists in the Organization table
        $organization = Organization::where('org_email', $request->email)->first();

        if ($organization) {
            // Organization email found, check the password
            if ($request->password === $organization->org_password) {
                // If organization credentials are valid, set session and redirect
                session([
                    'org_id'    => $organization->org_id,
                    'org_email' => $organization->org_email,
                    'org_name'  => $organization->org_name,
                ]);

                // Debugging: Log session data
                \Log::info('Session Data: ' . print_r(session()->all(), true));
                \Log::info('Organization Data: ' . print_r($organization, true));

                return redirect()->route('admin.dashboard');
            } else {
                // Invalid organization password
                return redirect()->route('login')->withErrors(['login_error' => 'Invalid organization password.']);
            }
        } else {
            // If not found in Organization, assume it's a student email
            $student = Student::where('student_email', $request->email)->first();

            if ($student && $request->password === $student->student_password) {
                // Set session data for the student
                session([
                    'student_id'    => $student->student_id,
                    'student_email' => $student->student_email,
                    'student_name'  => $student->student_name,
                ]);

                // Check if the student exists in the members table
                $member = Member::where('student_id', $student->student_id)->first();

                // If the student is not in the members table, add them with null org_id and position_id
                if (!$member) {
                    Member::create([
                        'student_id' => $student->student_id,
                        'org_id'     => null, // Null org_id
                        'position_id' => null, // Null position_id
                        'joined_at'   => now(), // Current timestamp
                    ]);
                }

                return redirect()->route('student-side.home');
            } else {
                return redirect()->route('login')->withErrors(['login_error' => 'Invalid email or password.']);
            }
        }
    }

    /**
     * Authenticate the user and load the home page.
     */
    public function authenticate()
    {
        // Check if an organization is logged in (admin side)
        if (session()->has('org_email')) {
            return redirect()->route('admin.dashboard');
        }

        // Check if a student is logged in
        if (!session()->has('student_email')) {
            return redirect()->route('login')->withErrors(['student_email' => 'Please log in first.']);
        }

        // Fetch the student from the database using the session email
        $student = Student::where('student_email', session('student_email'))->first();

        // Check if the student is found
        if (!$student) {
            return redirect()->route('login')->withErrors(['student_email' => 'Student not found.']);
        }

        // Pass the student to the view
        return view('student-side.home', compact('student'));
    }

    /**
     * Handle the logout request.
     */
    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}