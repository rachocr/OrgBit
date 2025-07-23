<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\MediaContent;
use App\Models\Organization;
use App\Models\Events;
use Carbon\Carbon;

class PostController extends Controller
{
    // Show the form to create a post
    public function create()
    {
        // Get all events and pass them to the view
        $events = Events::all();
        return view('admin-side.post', compact('events'));
    }

    // Store the post data
    public function storePost(Request $request)
    {
        try {
            Log::info('StorePost function triggered.');
    
            // Validate the request
            $validatedData = $request->validate([
                'post_title' => 'required|string|max:255',
                'post_content' => 'required',
                'event_id' => 'required|exists:events,event_id',
                'media_file' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480',
            ]);
    
            Log::info('Validation passed.');
    
            $media_id = null;
    
            // Check if a file is received
            if (!$request->hasFile('media_file')) {
                Log::error('No file detected in request.');
            } else {
                Log::info('File detected: ' . $request->file('media_file')->getClientOriginalName());
    
                $file = $request->file('media_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
    
                // Save file
                $filePath = $file->store('public/posts'); // Store file
                Log::info('File saved at: ' . $filePath);
    
                // Save media details to the database
                $media = MediaContent::create([
                    'file_name' => $fileName,
                    'file_type' => $file->getClientMimeType(),
                    'file_url' => asset('storage/posts/' . basename($filePath)), // Correct URL
                    'uploaded_by' => session('org_id'),
                ]);
    
                Log::info('Media saved in database.');
    
                $media_id = $media->media_id;
            }
    
            // Save the post
            Post::create([
                'post_title' => $request->post_title,
                'post_content' => $request->post_content,
                'event_id' => $request->event_id,
                'media_id' => $media_id,
                'org_id' => session('org_id'),
                'post_date_time' => now(),
            ]);
    
            Log::info('Post saved successfully.');
    
            return response()->json(['message' => 'Post created successfully!']);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
    
            return response()->json(['errors' => $e->errors()], 422);
    
        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
    
            return response()->json(['message' => 'An error occurred. Please try again.'], 500);
        }
    }    
}

