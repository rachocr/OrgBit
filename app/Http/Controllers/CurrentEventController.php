<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\EventAttendance;
use App\Models\EventRegistrations;
use App\Models\Events;
use App\Models\Organization;
use App\Models\Member;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class CurrentEventController extends Controller
{
    // Display the current event page
    public function index()
{
    // Get organization ID from session
    $org_id = session('org_id');
    if (!$org_id) {
        return redirect()->route('login')->withErrors(['error' => 'Please log in first.']);
    }

    // Retrieve organization record
    $organization = Organization::find($org_id);
    if (!$organization) {
        return redirect()->route('login')->withErrors(['error' => 'Organization not found.']);
    }

    // Fetch the current event
    $event = Events::where('org_id', $org_id)->orderBy('event_start_date', 'desc')->first();

    // Handle case where no event is found
    if (!$event) {
        return redirect()->route('admin.dashboard')->withErrors(['error' => 'No events found for this organization.']);
    }

    // Fetch registrations for the current event
    $registrations = DB::table('event_registrations')
        ->join('members', 'event_registrations.member_id', '=', 'members.member_id')
        ->join('students', 'members.student_id', '=', 'students.student_id')
        ->leftJoin('courses', 'students.course_id', '=', 'courses.course_id')
        ->leftJoin('college', 'students.college_id', '=', 'college.college_id')
        ->where('event_registrations.event_id', $event->event_id)
        ->select(
            'students.student_name',
            'students.student_year as year',
            'courses.course_name as course',
            'college.college_name as college',
            'members.member_id' // Add member_id to the select
        )
        ->get();

    // Fetch attendees who have 'ATTENDED' status
    $attendees = DB::table('event_attendance')
        ->join('members', 'event_attendance.member_id', '=', 'members.member_id')
        ->join('students', 'members.student_id', '=', 'students.student_id')
        ->leftJoin('courses', 'students.course_id', '=', 'courses.course_id')
        ->leftJoin('college', 'students.college_id', '=', 'college.college_id')
        ->where('event_attendance.status', 'ATTENDED')
        ->where('event_attendance.event_id', $event->event_id) // Ensure it's the same event
        ->select(
            'students.student_name',
            'students.student_year as year',
            'courses.course_name as course',
            'college.college_name as college',
            'members.member_id' // Add member_id to the select
        )
        ->get();

    // Filter out registrants who are already in the attendees list
    $filteredRegistrations = $registrations->reject(function ($registration) use ($attendees) {
        return $attendees->contains('member_id', $registration->member_id);
    });

    // Pass data to view
    return view('admin-side.current_event', compact('organization', 'attendees', 'event', 'filteredRegistrations'));
}

    // Generate QR codes and send emails
    public function generateQRAttendance(Request $request) {
        Log::info('generateQRAttendance method called.');
    
        // Validate the request
        $request->validate([
            'event_id' => 'required|exists:events,event_id'
        ]);
    
        $eventId = $request->input('event_id');
        Log::info('Event ID:', ['event_id' => $eventId]);
    
        // Fetch all registrants for the event
        $registrants = EventRegistrations::where('event_id', $eventId)
            ->with('member.student')
            ->get();
    
        if ($registrants->isEmpty()) {
            Log::warning('No registrants found for event:', ['event_id' => $eventId]);
            return response()->json(['success' => false, 'message' => 'No registrants found for this event.']);
        }
    
        Log::info('Registrants found:', ['count' => $registrants->count()]);
    
        // Loop through each registrant and generate a unique QR code
        foreach ($registrants as $registrant) {
            try {
                Log::info('Processing registrant:', ['member_id' => $registrant->member_id]);
    
                // Generate unique data for the QR code
                $qrData = $this->generateUniqueQRCode($registrant->member_id, $eventId);
                Log::info('Generated QR code data:', ['qr_data' => $qrData]);
    
                // Generate the QR code image as PNG
                $qrCodeImage = QrCode::format('png')->size(600)->generate($qrData);
                Log::info('QR code image generated.');
    
                // Create a unique filename for the QR code image
                $fileName = 'qrcode_' . time() . '_' . $registrant->member->student->student_id . '.png';
                Log::info('QR code file name:', ['file_name' => $fileName]);
    
                // Save the image to storage
                Storage::disk('public')->put('qrcodes/' . $fileName, $qrCodeImage);
                Log::info('QR code saved to storage.');
    
                // Save attendance record to database with status set to "NOT ATTENDED"
                try {
                    EventAttendance::create([
                        'event_id'  => $eventId,
                        'member_id' => $registrant->member_id,
                        'qr_code'   => $qrData,
                        'status'    => 'NOT ATTENDED',
                    ]);
                    Log::info('Attendance record created.');
                } catch (QueryException $e) {
                    // Handle duplicate entry error
                    if ($e->errorInfo[1] === 1062) { // MySQL error code for duplicate entry
                        Log::info('Attendance record already exists for member:', ['member_id' => $registrant->member_id]);
                        continue; // Skip this registrant
                    }
                    throw $e; // Re-throw the exception if it's not a duplicate entry error
                }
    
                // Fetch the registrant's email
                $studentEmail = $registrant->member->student->student_email; // Use student_email
                Log::info('Registrant email:', ['email' => $studentEmail]);
    
                if (!$studentEmail) {
                    Log::error('Registrant email is null:', ['member_id' => $registrant->member_id]);
                    continue; // Skip this registrant if email is null
                }
    
                // Send the email with the QR code attached
                $emailData = [
                    'eventName' => $registrant->event->event_name,
                    'eventStartDate' => $registrant->event->event_start_date,
                    'eventEndDate' => $registrant->event->event_end_date,
                    'eventLocation' => $registrant->event->event_location,
                    'fileName' => $fileName,
                ];
    
                Mail::send('emails.qr_code', $emailData, function ($message) use ($studentEmail, $fileName) {
                    $message->to($studentEmail)
                            ->subject('Your Event QR Code')
                            ->attach(storage_path('app/public/qrcodes/' . $fileName), [
                                'as' => 'qrcode.png',
                                'mime' => 'image/png',
                            ]);
                });
                Log::info('Email sent to registrant.');
            } catch (\Exception $e) {
                Log::error('Error processing registrant:', [
                    'member_id' => $registrant->member_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    
        Log::info('QR codes generated and emails sent successfully.');
        return response()->json(['success' => true, 'message' => 'QR codes generated and emails sent successfully!']);
    }

    /**
     * Generate a unique QR code string.
     *
     * @param int $memberId
     * @param int $eventId
     * @return string
     */
    private function generateUniqueQRCode($memberId, $eventId)
    {
        // Generate a random string (e.g., 8 characters long)
        $randomString = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        // Combine member ID, event ID, and random string
        $uniqueCode = "{$memberId}-{$eventId}-{$randomString}";

        return $uniqueCode;
    }

    // Scan and validate QR codes
    public function scanValidateQR(Request $request)
    {
        // Validate the request
        $request->validate([
            'qr_data' => 'required|string'
        ]);
    
        $scannedData = $request->input('qr_data');
        Log::info('Scanned QR Code Data:', ['qr_data' => $scannedData]);
    
        // Find the attendance record using the scanned QR code data
        $attendance = EventAttendance::where('qr_code', $scannedData)->first();
    
        if (!$attendance) {
            Log::warning('QR Code not found:', ['qr_data' => $scannedData]);
            return response()->json(['message' => 'QR Code not found.'], 404);
        }
    
        Log::info('Attendance Record Found:', ['attendance' => $attendance]);
    
        // Check if already validated (case-insensitive comparison)
        if (strtoupper($attendance->status) === 'ATTENDED') {
            Log::info('QR Code already validated:', ['attendance' => $attendance]);
            return response()->json(['message' => 'QR Code has already been validated.'], 200);
        }
    
        // Update the status to "ATTENDED" and set the scanned_at timestamp
        $attendance->status = 'ATTENDED'; // Ensure status is saved in uppercase
        $attendance->scanned_at = now();
        $attendance->save();
        
        Log::info('QR Code validated and marked as attended:', ['attendance' => $attendance]);
    
        return response()->json(['message' => 'QR Code validated. Attendee confirmed.'], 200);
    }

    // Disseminate Evaluation Link
    public function disseminateEvaluationLink(Request $request) {
        // Validate the request
        $request->validate([
            'event_id' => 'required|exists:events,event_id'
        ]);
    
        $eventId = $request->input('event_id');
        Log::info('Disseminate Evaluation Link:', ['event_id' => $eventId]);
    
        // Fetch the event to get the evaluation link
        $event = Events::find($eventId);
    
        if (!$event) {
            Log::warning('Event not found:', ['event_id' => $eventId]);
            return response()->json(['success' => false, 'message' => 'Event not found.'], 404);
        }
    
        // Fetch all attendees with status 'ATTENDED' for the event
        $attendees = EventAttendance::where('event_id', $eventId)
            ->where('status', 'ATTENDED')
            ->with('member.student') // Ensure the relationships are defined
            ->get();
    
        if ($attendees->isEmpty()) {
            Log::warning('No attendees found for event:', ['event_id' => $eventId]);
            return response()->json(['success' => false, 'message' => 'No attendees found for this event.'], 404);
        }
    
        Log::info('Attendees found:', ['count' => $attendees->count()]);
    
        // Loop through each attendee and send the evaluation link email
        foreach ($attendees as $attendee) {
            try {
                // Ensure the member and student relationships are loaded
                if (!$attendee->member || !$attendee->member->student) {
                    Log::warning('Member or student not found for attendee:', ['attendee' => $attendee]);
                    continue; // Skip if member or student is not available
                }
    
                $studentEmail = $attendee->member->student->student_email; // Get the student's email
    
                if (!$studentEmail) {
                    Log::warning('Student email not found:', ['attendee' => $attendee]);
                    continue; // Skip if email is not available
                }
    
                // Prepare email data
                $emailData = [
                    'eventName' => $event->event_name,
                    'evaluationLink' => $event->event_evaluation_link, // Use the event_evaluation_link from the events table
                ];
    
                // Send the email
                Mail::send('emails.evaluation_link', $emailData, function ($message) use ($studentEmail) {
                    $message->to($studentEmail)
                            ->subject('Event Evaluation Link');
                });
    
                Log::info('Evaluation link email sent:', ['email' => $studentEmail]);
            } catch (\Exception $e) {
                Log::error('Error sending evaluation link email:', [
                    'email' => $studentEmail,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    
        return response()->json(['success' => true, 'message' => 'Evaluation links have been sent successfully!']);
    }
}