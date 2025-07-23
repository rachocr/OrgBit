<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\OrganizationStats;
use App\Models\Events;
use App\Models\Post;
use App\Models\MediaContent;
use App\Models\Member;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $org_id = session('org_id');
        if (!$org_id) {
            return redirect()->route('login')->withErrors(['error' => 'Please log in first.']);
        }

        $organization = Organization::find($org_id);
        if (!$organization) {
            return redirect()->route('login')->withErrors(['error' => 'Organization not found.']);
        }

        $totalMembers = Member::where('org_id', $org_id)
            ->whereIn('position_id', [2, 3, 4])
            ->count();

        $stats = OrganizationStats::where('org_id', $org_id)->first();
        if (!$stats) {
            $stats = (object) [
                'total_members' => $totalMembers,
                'total_events'  => Events::where('org_id', $org_id)->count(),
                'recent_posts'  => Post::where('org_id', $org_id)->count(),
            ];
        }

        $events = Events::where('org_id', $org_id)
                        ->orderBy('event_start_date', 'desc')
                        ->get();

        $posts = Post::where('org_id', $org_id)
                     ->with('event', 'media', 'organization')
                     ->orderBy('post_date_time', 'desc')
                     ->get();

        $now = now();
        $ongoingEvent = Events::where('org_id', $org_id)
                              ->where(function ($query) use ($now) {
                                  $query->where('event_start_date', '<=', $now)
                                        ->where('event_end_date', '>=', $now);
                              })
                              ->orWhere(function ($query) use ($now) {
                                  $query->where('event_start_date', '>', $now)
                                        ->where('event_start_date', '<=', $now->copy()->addDay());
                              })
                              ->first();

        Log::info('Current Time: ' . $now);
        Log::info('Ongoing Event: ' . print_r($ongoingEvent, true));

        return view('admin-side.dashboard', compact('organization', 'totalMembers', 'stats', 'events', 'posts', 'ongoingEvent'));
    }

    public function storePost(Request $request)
    {
        Log::info('StorePost method called.', ['request_data' => $request->all()]);

        try {
            $validatedData = $request->validate([
                'post_title'   => 'required|string|max:255',
                'post_content' => 'required',
                'event_id'     => 'required|exists:events,event_id',
                'media_file'   => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480',
            ]);

            $media_id = null;
            if ($request->hasFile('media_file')) {
                $file = $request->file('media_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('posts', $fileName, 'public');

                $media = MediaContent::create([
                    'file_name'   => $fileName,
                    'file_type'   => $file->getClientMimeType(),
                    'file_url'    => asset('storage/posts/' . $fileName),
                    'org_id'      => session('org_id'),
                    'uploaded_by' => session('org_id'),
                ]);

                $media_id = $media->media_id;
            }

            Post::create([
                'post_title'     => $request->post_title,
                'post_content'   => $request->post_content,
                'event_id'       => $request->event_id,
                'media_id'       => $media_id,
                'org_id'         => session('org_id'),
                'post_date_time' => now(),
            ]);

            return response()->json([
                'message' => 'Post created successfully!',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error storing post: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred. Please try again.',
            ], 500);
        }
    }

    public function getPost($postId)
    {
        try {
            $post = Post::with('media')->findOrFail($postId);
            return response()->json($post);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Post not found.',
            ], 404);
        }
    }

    public function updatePost(Request $request, $postId)
    {
        DB::beginTransaction(); // Start a database transaction
        try {
            Log::info('Update Post function triggered.');
    
            // Validate the request data
            $validated = $request->validate([
                'post_title' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'post_content' => 'required',
                'event_id' => 'required|exists:events,event_id',
                'media_file' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480', // Optional file upload
            ]);
    
            Log::info('Validation passed.');
    
            // Find the post
            $post = Post::findOrFail($postId);
            Log::info('Post found', ['post' => $post]);
    
            // Handle media update
            if ($request->hasFile('media_file')) {
                Log::info('New file detected: ' . $request->file('media_file')->getClientOriginalName());
    
                // Save the new file
                $file = $request->file('media_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('posts', $fileName, 'public');
                Log::info('New file saved at: ' . $filePath);
    
                // Update the existing media record (if it exists)
                if ($post->media_id) {
                    $media = MediaContent::find($post->media_id);
                    if ($media) {
                        // Update the media record with the new file details
                        $media->update([
                            'file_name' => $fileName,
                            'file_type' => $file->getClientMimeType(),
                            'file_url' => asset('storage/posts/' . $fileName),
                            'updated_at' => now(),
                        ]);
                        Log::info('Media record updated: ' . $media->media_id);
                    }
                } else {
                    // Create a new media record if no media_id exists
                    $media = MediaContent::create([
                        'file_name' => $fileName,
                        'file_type' => $file->getClientMimeType(),
                        'file_url' => asset('storage/posts/' . $fileName),
                        'org_id' => session('org_id'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    Log::info('New media saved in database with ID: ' . $media->media_id);
                }
    
                // Update the post's media_id
                $post->media_id = $media->media_id;
            }
    
            // Update the post
            $post->update([
                'post_title' => $validated['post_title'],
                'post_content' => $validated['post_content'],
                'event_id' => $validated['event_id'],
            ]);
    
            DB::commit(); // Commit the transaction
            Log::info('Post updated successfully.');
    
            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully!',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack(); // Rollback the transaction on validation error
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on any other error
            Log::error('Update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update post.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroyPost($postId)
    {
        try {
            $post = Post::findOrFail($postId);
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post. Please try again.',
            ], 500);
        }
    }
}