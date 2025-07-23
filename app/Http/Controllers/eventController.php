<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Events;
use App\Models\Organization;
use App\Models\MediaContent;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        $orgId = Session::get('org_id');
        $organization = Organization::find($orgId);
        if (!$organization) {
            return redirect()->route('login')->withErrors(['error' => 'Organization not found.']);
        }
    
        // Fetch events with the count of registrations
        $events = Events::withCount('registrants') // This will add a 'registrants_count' attribute
                        ->where('org_id', $orgId)
                        ->get();
    
        $now = now();
        $ongoingEvent = Events::where('org_id', $orgId)
            ->where(function ($query) use ($now) {
                $query->where('event_start_date', '<=', $now)
                      ->where('event_end_date', '>=', $now);
            })
            ->orWhere(function ($query) use ($now) {
                $query->where('event_start_date', '>', $now)
                      ->where('event_start_date', '<=', $now->copy()->addDay());
            })
            ->first();
    
        return view('admin-side.events', compact('organization', 'events', 'ongoingEvent'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Store function triggered.');

            $validated = $request->validate([
                'event_name' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        $normalizedEventName = strtolower(str_replace(' ', '', $value));
                        $exists = Events::whereRaw('LOWER(REPLACE(event_name, " ", "")) = ?', [$normalizedEventName])
                            ->where('org_id', Session::get('org_id'))
                            ->exists();

                        if ($exists) {
                            $fail('The event name already exists.');
                        }
                    },
                ],
                'event_start_date' => 'required|date_format:Y-m-d\TH:i',
                'event_end_date' => 'required|date_format:Y-m-d\TH:i|after_or_equal:event_start_date',
                'event_location' => 'required|string|max:255',
                'event_evaluation_link' => 'nullable|url',
                'event_certification_link' => 'nullable|url',
                'event_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            ]);

            Log::info('Validation passed.');

            $media_id = null;

            if ($request->hasFile('event_image')) {
                Log::info('File detected: ' . $request->file('event_image')->getClientOriginalName());

                $file = $request->file('event_image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('events', $fileName, 'public');
                Log::info('File saved at: ' . $filePath);

                $media = MediaContent::create([
                    'file_name' => $fileName,
                    'file_type' => $file->getClientMimeType(),
                    'file_url' => asset('storage/events/' . $fileName),
                    'org_id' => Session::get('org_id'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                Log::info('Media saved in database with ID: ' . $media->media_id);
                $media_id = $media->media_id;
            }

            $eventStartDate = Carbon::createFromFormat('Y-m-d\TH:i', $validated['event_start_date'])->format('Y-m-d H:i:s');
            $eventEndDate = Carbon::createFromFormat('Y-m-d\TH:i', $validated['event_end_date'])->format('Y-m-d H:i:s');

            $event = Events::create([
                'org_id' => Session::get('org_id'),
                'event_name' => $validated['event_name'],
                'event_start_date' => $eventStartDate,
                'event_end_date' => $eventEndDate,
                'event_location' => $validated['event_location'],
                'event_evaluation_link' => $validated['event_evaluation_link'] ?? null,
                'event_certification_link' => $validated['event_certification_link'] ?? null,
                'media_id' => $media_id,
            ]);

            Log::info('Event created successfully with media ID: ' . $media_id);

            return response()->json(['success' => true, 'message' => 'Event created successfully!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create event.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkEventName(Request $request)
    {
        $eventName = $request->query('event_name');
        $excludeEventId = $request->query('exclude_event_id');

        $normalizedEventName = strtolower(str_replace(' ', '', $eventName));

        $exists = Events::whereRaw('LOWER(REPLACE(event_name, " ", "")) = ?', [$normalizedEventName])
            ->where('org_id', Session::get('org_id'))
            ->when($excludeEventId, function ($query) use ($excludeEventId) {
                $query->where('event_id', '!=', $excludeEventId);
            })
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function update(Request $request, Events $event)
{
    DB::beginTransaction();
    try {
        Log::info('Update function triggered.');

        // Validate the request data
        $validated = $request->validate([
            'event_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($event) {
                    $normalizedEventName = strtolower(str_replace(' ', '', $value));
                    $exists = Events::whereRaw('LOWER(REPLACE(event_name, " ", "")) = ?', [$normalizedEventName])
                        ->where('org_id', Session::get('org_id'))
                        ->where('event_id', '!=', $event->event_id)
                        ->exists();

                    if ($exists) {
                        $fail('The event name already exists.');
                    }
                },
            ],
            'event_start_date' => 'required|date_format:Y-m-d\TH:i',
            'event_end_date' => 'required|date_format:Y-m-d\TH:i|after_or_equal:event_start_date',
            'event_location' => 'required|string|max:255',
            'event_evaluation_link' => 'nullable|url',
            'event_certification_link' => 'nullable|url',
            'event_image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // Optional file upload
        ]);

        Log::info('Validation passed.');

        // Handle media update
        if ($request->hasFile('event_image')) {
            Log::info('New file detected: ' . $request->file('event_image')->getClientOriginalName());

            // Save the new file
            $file = $request->file('event_image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('events', $fileName, 'public');
            Log::info('New file saved at: ' . $filePath);

            // Update the existing media record (if it exists)
            if ($event->media_id) {
                $media = MediaContent::find($event->media_id);
                if ($media) {
                    // Update the media record with the new file details
                    $media->update([
                        'file_name' => $fileName,
                        'file_type' => $file->getClientMimeType(),
                        'file_url' => asset('storage/events/' . $fileName),
                        'updated_at' => Carbon::now(),
                    ]);
                    Log::info('Media record updated: ' . $media->media_id);
                }
            } else {
                // Create a new media record if no media_id exists
                $media = MediaContent::create([
                    'file_name' => $fileName,
                    'file_type' => $file->getClientMimeType(),
                    'file_url' => asset('storage/events/' . $fileName),
                    'org_id' => Session::get('org_id'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                Log::info('New media saved in database with ID: ' . $media->media_id);
            }
        }

        // Format dates
        $eventStartDate = Carbon::createFromFormat('Y-m-d\TH:i', $validated['event_start_date'])->format('Y-m-d H:i:s');
        $eventEndDate = Carbon::createFromFormat('Y-m-d\TH:i', $validated['event_end_date'])->format('Y-m-d H:i:s');

        // Update the event
        $event->update([
            'event_name' => $validated['event_name'],
            'event_start_date' => $eventStartDate,
            'event_end_date' => $eventEndDate,
            'event_location' => $validated['event_location'],
            'event_evaluation_link' => $validated['event_evaluation_link'] ?? null,
            'event_certification_link' => $validated['event_certification_link'] ?? null,
        ]);

        DB::commit();
        Log::info('Event updated successfully.');

        return response()->json(['success' => true, 'message' => 'Event updated successfully!']);
    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        Log::error('Validation error: ' . json_encode($e->errors()));
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Update failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to update event.',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function destroy(Events $event)
{
    try {
        Log::info('Destroy method called for event ID: ' . $event->event_id);

        if ($event->media_id) {
            $media = MediaContent::find($event->media_id);
            if ($media) {
                if (Storage::disk('public')->exists('events/' . $media->file_name)) {
                    Storage::disk('public')->delete('events/' . $media->file_name);
                    Log::info('File deleted: ' . $media->file_name);
                }
                $media->delete();
                Log::info('Media record deleted: ' . $event->media_id);
            }
        }

        $event->delete();
        Log::info('Event deleted: ' . $event->event_id);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        Log::error('Delete failed: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}
}