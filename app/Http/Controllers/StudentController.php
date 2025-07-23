<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Post;
use App\Models\Organization;
use App\Models\Member;
use Illuminate\Support\Facades\Session;
use App\Models\Events;
use Illuminate\Support\Facades\Log;
use App\Models\EventRegistrations;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function home()
    {
        if (!session()->has('student_email')) {
            return redirect()->route('login')->withErrors(['student_email' => 'Please log in first.']);
        }

        $student = Student::where('student_email', session('student_email'))
                          ->with('organizations', 'course', 'college')
                          ->first();

        if (!$student) {
            return redirect()->route('login')->withErrors(['student_email' => 'Student not found.']);
        }

        $posts = Post::with('event', 'media', 'organization')
                     ->orderBy('post_date_time', 'desc')
                     ->take(10)
                     ->get();

        $organizations = Organization::take(6)->get();

        return view('student-side.home', compact('student', 'posts', 'organizations'));
    }

    public function fetchMorePosts(Request $request)
    {
        $offset = $request->input('offset', 0);
        $filter = $request->input('filter', 'all');
    
        $query = Post::with('event', 'media', 'organization')
                     ->orderBy('post_date_time', 'desc')
                     ->skip($offset)
                     ->take(10);
    
        if ($filter === 'posts') {
            $query->whereNull('media_id');
        } elseif ($filter === 'images') {
            $query->whereHas('media', function ($q) {
                $q->where('file_type', 'like', 'image%');
            });
        }
    
        $posts = $query->get();
    
        // Format the post_date_time using diffForHumans()
        $posts->transform(function ($post) {
            $post->formatted_date = $post->post_date_time->diffForHumans();
            return $post;
        });
    
        return response()->json($posts);
    }

    public function joinOrganization($orgId)
{
    $student = Student::where('student_email', session('student_email'))->first();

    if (!$student) {
        return response()->json([
            'success' => false,
            'message' => 'Student not found.',
        ], 401);
    }

    // Check if the student is already a member of the organization with position_id > 1
    $existingMember = Member::where('student_id', $student->student_id)
                            ->where('org_id', $orgId)
                            ->where('position_id', '>', 1)
                            ->first();

    if ($existingMember) {
        return response()->json([
            'success' => true,
            'message' => 'You are already a member of this organization.',
            'position_id' => $existingMember->position_id,
        ], 200);
    }

    // Check if the student has already requested to join (position_id = 1)
    $existingRequest = Member::where('student_id', $student->student_id)
                             ->where('org_id', $orgId)
                             ->where('position_id', 1)
                             ->first();

    if ($existingRequest) {
        return response()->json([
            'success' => true,
            'message' => 'You have already requested to join this organization.',
            'position_id' => $existingRequest->position_id,
        ], 200);
    }

    try {
        // Add the student as a member of the organization with position_id = 1 (default for new members)
        Member::create([
            'student_id' => $student->student_id,
            'org_id' => $orgId,
            'position_id' => 1, // Default position for new members
            'joined_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your request to join the organization has been submitted. You are now under review.',
            'position_id' => 1,
        ]);
    } catch (\Exception $e) {
        Log::error('Error joining organization:', [
            'error' => $e->getMessage(),
            'student_id' => $student->student_id,
            'org_id' => $orgId,
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while joining the organization.',
        ], 500);
    }
}

    public function fetchPostDetails($postId)
    {
        try {
            $post = Post::with('event')->find($postId);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'post_id' => $post->post_id,
                'event_id' => $post->event_id,
                'org_id' => $post->org_id,
                'event' => $post->event,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching post details:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching post details.',
            ], 500);
        }
    }

    public function fetchEventDetails($eventId)
    {
        try {
            // Fetch the event with its associated organization
            $event = Events::with('organization')->find($eventId);
    
            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found.',
                ], 404);
            }
    
            // Fetch the organization name directly from the event's organization relationship
            $organizationName = $event->organization->org_name ?? 'Unknown Organization';
            $organizationId = $event->organization->org_id ?? null;
    
            return response()->json([
                'success' => true,
                'event_name' => $event->event_name,
                'event_description' => $event->event_description,
                'event_start_date' => $event->event_start_date,
                'event_end_date' => $event->event_end_date,
                'event_location' => $event->event_location,
                'organization_name' => $organizationName,
                'organization_id' => $organizationId,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching event details:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching event details.',
            ], 500);
        }
    }

public function joinEvent(Request $request, $eventId)
{
    Log::info('Join Event Request Initiated', [
        'event_id' => $eventId,
        'student_email' => session('student_email'),
    ]);

    $student = Student::where('student_email', session('student_email'))->first();
    if (!$student) {
        Log::warning('Student not found', ['student_email' => session('student_email')]);
        return response()->json([
            'success' => false,
            'message' => 'Student not found.',
        ], 404);
    }

    $event = Events::find($eventId);
    if (!$event) {
        Log::warning('Event not found', ['event_id' => $eventId]);
        return response()->json([
            'success' => false,
            'message' => 'Event not found.',
        ], 404);
    }

    $member = Member::where('student_id', $student->student_id)
                    ->where('org_id', $event->org_id)
                    ->first();

    Log::info('Member retrieved', [
        'member' => $member,
        'student_id' => $student->student_id,
        'org_id' => $event->org_id,
    ]);

    if (!$member) {
        Log::warning('Membership check failed', [
            'student_id' => $student->student_id,
            'event_id' => $eventId,
            'org_id' => $event->org_id,
            'message' => 'Student is not a member of the organization hosting the event.'
        ]);
        return response()->json([
            'success' => false,
            'message' => 'You must be a member of the organization to join this event.',
        ], 403);
    }

    // Verify that the member_id exists in the members table
    $memberExists = DB::table('members')->where('member_id', $member->member_id)->exists();
    if (!$memberExists) {
        Log::error('Member ID does not exist in members table', [
            'member_id' => $member->member_id,
            'message' => 'The member_id does not exist in the members table.'
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Invalid member ID. Please contact support.',
        ], 400);
    }

    $existingRegistration = EventRegistrations::where('event_id', $eventId)
        ->where('member_id', $member->member_id)
        ->first();

    if ($existingRegistration) {
        Log::info('Registration check', [
            'student_id' => $student->student_id,
            'event_id' => $eventId,
            'message' => 'Student is already registered for this event.'
        ]);
        return response()->json([
            'success' => false,
            'message' => 'You are already registered for this event.',
        ], 400);
    }

    $uniqueCode = $this->generateUniqueQRCode($student->student_id, $eventId);

    Log::info('Generating QR code', [
        'member_id' => $member->member_id,
        'event_id' => $eventId,
        'unique_code' => $uniqueCode,
    ]);

    try {
        EventRegistrations::create([
            'event_id' => $eventId,
            'member_id' => $member->member_id,
            'status' => 'pending',
            'joined_at' => now(),
            'qr_code' => $uniqueCode,
        ]);

        Log::info('Student successfully joined event', [
            'student_id' => $student->student_id,
            'event_id' => $eventId,
            'qr_code' => $uniqueCode,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'You have successfully joined the event!',
            'qrcode' => $uniqueCode,
        ]);
    } catch (\Exception $e) {
        Log::error('Error joining event:', [
            'error' => $e->getMessage(),
            'student_id' => $student->student_id,
            'event_id' => $eventId,
        ]);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while joining the event.',
        ], 500);
    }
}

    private function generateUniqueQRCode($memberId, $eventId)
    {
        // Ensure $memberId is an integer
        $memberId = (int) $memberId;

        $randomString = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
        $uniqueCode = "{$memberId}-{$eventId}-{$randomString}";
        return $uniqueCode;
    }

    public function checkEventParticipation($eventId)
{
    $student = Student::where('student_email', session('student_email'))->first();

    if (!$student) {
        return response()->json([
            'success' => false,
            'message' => 'Student not found.',
        ], 404);
    }

    // Fetch the event
    $event = Events::find($eventId);

    if (!$event) {
        return response()->json([
            'success' => false,
            'message' => 'Event not found.',
        ], 404);
    }

    // Check if the event has ended
    $eventEnded = now()->gt($event->event_end_date);

    // Fetch the member_id for the student in the organization hosting the event
    $member = Member::where('student_id', $student->student_id)
                    ->where('org_id', $event->org_id)
                    ->first();

    if (!$member) {
        return response()->json([
            'success' => true,
            'hasJoined' => false,
            'eventEnded' => $eventEnded,
        ]);
    }

    // Check if the member is already registered for the event
    $existingRegistration = EventRegistrations::where('event_id', $eventId)
        ->where('member_id', $member->member_id)
        ->first();

    return response()->json([
        'success' => true,
        'hasJoined' => $existingRegistration ? true : false,
        'eventEnded' => $eventEnded,
    ]);
}

    public function fetchOrganizationDetails($orgId)
    {
        try {
            $organization = Organization::find($orgId);

            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Organization not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'org_name' => $organization->org_name,
                'org_bio' => $organization->org_bio,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching organization details:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching organization details.',
            ], 500);
        }
    }

    public function organizations()
    {
        $student = Student::where('student_email', session('student_email'))->first();
        $organizations = Organization::all();
    
        foreach ($organizations as $org) {
            $member = $student->members()->where('org_id', $org->org_id)->first();
            if ($member) {
                $org->has_joined = $member->position_id > 1; // Check if the position_id indicates membership
                $org->has_requested = $member->position_id == 1; // Check if the position_id indicates a request
            } else {
                $org->has_joined = false;
                $org->has_requested = false;
            }
        }
    
        return view('student-side.organizations', compact('organizations'));
    }

    public function events()
{
    // Fetch the logged-in student
    $student = Student::where('student_email', session('student_email'))->first();

    if (!$student) {
        return redirect()->route('login')->withErrors(['student_email' => 'Student not found.']);
    }

    // Fetch event IDs the student has joined (using student_id)
    $joinedEventIds = EventRegistrations::join('members', 'event_registrations.member_id', '=', 'members.member_id')
        ->where('members.student_id', $student->student_id)
        ->pluck('event_registrations.event_id')
        ->toArray();

    Log::info('Joined event IDs:', ['joined_event_ids' => $joinedEventIds]);

    // Fetch all events with their organization
    $allEvents = Events::with('organization')->get();

    // Add the `has_joined` and `has_ended` properties to each event
    $events = $allEvents->map(function ($event) use ($joinedEventIds) {
        $event->has_joined = in_array($event->event_id, $joinedEventIds);
        $event->has_ended = now()->gt($event->event_end_date); // Check if the event has ended

        Log::info('Event joined check:', [
            'event_id' => $event->event_id,
            'event_name' => $event->event_name,
            'joined_event_ids' => $joinedEventIds,
            'has_joined' => $event->has_joined,
        ]);

        return $event;
    });

    // Sort events in the desired order:
    // 1. Available for joining (not joined, not ended)
    // 2. Already joined (has_joined = true)
    // 3. Event ended (has_ended = true)
    $sortedEvents = $events->sortBy(function ($event) {
        if (!$event->has_joined && !$event->has_ended) {
            return 1; // Highest priority: available for joining
        } elseif ($event->has_joined) {
            return 2; // Medium priority: already joined
        } else {
            return 3; // Lowest priority: event ended
        }
    })->values();

    // Log sorted events for debugging
    Log::info('Sorted events:', [
        'sorted_events' => $sortedEvents->pluck('event_name', 'event_id')->toArray(),
    ]);

    // Pass the sorted events to the view
    return view('student-side.events', [
        'student' => $student,
        'sortedEvents' => $sortedEvents,
    ]);
}

        public function checkOrganizationMembership($orgId)
    {
        // Fetch the student based on the session email
        $student = Student::where('student_email', session('student_email'))->first();
    
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ], 404);
        }
    
        // Check if the student is associated with the organization
        $existingMember = Member::where('student_id', $student->student_id)
                                ->where('org_id', $orgId)
                                ->first();
    
        if ($existingMember) {
            // If position_id is greater than 1, the user is a member
            if ($existingMember->position_id > 1) {
                return response()->json([
                    'success' => true,
                    'hasJoined' => true, // Already a member
                    'position_id' => $existingMember->position_id,
                ]);
            } else {
                // If position_id is 1, the user has requested to join
                return response()->json([
                    'success' => true,
                    'hasJoined' => false, // Requested to join
                    'position_id' => $existingMember->position_id,
                ]);
            }
        }
    
        // If the user is not associated with the organization
        return response()->json([
            'success' => true,
            'hasJoined' => false, // Not a member
            'position_id' => null,
        ]);
    }

    public function checkOrganizationMembershipForEvent($eventId)
    {
        $student = Student::where('student_email', session('student_email'))->first();
    
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ], 404);
        }
    
        // Fetch the event to get the organization ID
        $event = Events::find($eventId);
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.',
            ], 404);
        }
    
        // Check if the student is a member of the organization hosting the event
        $isMember = Member::where('student_id', $student->student_id)
                          ->where('org_id', $event->org_id)
                          ->where('position_id', '>', 1) // Only consider members with position_id > 1
                          ->exists();
    
        return response()->json([
            'success' => true,
            'isMember' => $isMember,
        ]);
    }

}