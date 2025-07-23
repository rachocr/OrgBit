<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CurrentEventController;
use App\Http\Controllers\EvaluationController;

// Routes for Admin
Route::get('/organization/create-post', [PostController::class, 'create'])->name('post.create');
Route::get('/organization/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/organization/events', [EventController::class, 'index'])->name('admin.events');
Route::get('/organization/members', [MemberController::class, 'index'])->name('admin.members');
Route::get('/organization/settings', [SettingsController::class, 'index'])->name('admin.settings');

Route::get('/information', function () {
    return view('student-side.information');
})->name('student.information');

Route::get('/organization/events/current-event', [CurrentEventController::class, 'index'])->name('admin.current-event');

// Event Routes
Route::post('/events', [EventController::class, 'store'])->name('events.store');
Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
Route::get('/check-event-name', [EventController::class, 'checkEventName'])->name('events.check-name');

// Post Store Route
Route::post('/admin/store-post', [PostController::class, 'store'])->name('post.store');

// Routes for Student
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/home', [StudentController::class, 'home'])->name('student-side.home');
Route::get('/organizations', [OrganizationController::class, 'index'])->name('student-side.organizations');
Route::get('/events', [StudentController::class, 'events'])->name('student-side.events'); // Corrected route
Route::get('/fetch-more-posts', [StudentController::class, 'fetchMorePosts']);

// Authentication Routes
Route::post('/login', [LoginController::class, 'login'])->name('authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard Routes
Route::get('/organization/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
Route::post('/dashboard/post/store', [DashboardController::class, 'storePost'])->name('post.store');
Route::put('/dashboard/post/update/{postId}', [DashboardController::class, 'updatePost'])->name('post.update');
Route::delete('/posts/{post}', [DashboardController::class, 'destroyPost'])->name('posts.destroy');

// Join Event and Organization Routes
Route::get('/fetch-post-details/{postId}', [StudentController::class, 'fetchPostDetails']);
Route::get('/fetch-event-details/{eventId}', [StudentController::class, 'fetchEventDetails']);
Route::post('/join-event/{eventId}', [StudentController::class, 'joinEvent'])->name('join.event');
Route::get('/check-event-participation/{eventId}', [StudentController::class, 'checkEventParticipation'])->name('check-event-participation');
Route::post('/join-organization/{orgId}', [StudentController::class, 'joinOrganization'])->name('join-organization');
Route::get('/check-organization-membership/{orgId}', [StudentController::class, 'checkOrganizationMembership'])->name('check-organization-membership');
Route::get('/fetch-organization-details/{orgId}', [StudentController::class, 'fetchOrganizationDetails'])->name('fetch-organization-details');

// Member Approval Routes
Route::post('/organization/members/approve/{id}', [MemberController::class, 'approve'])->name('admin.members.approve');
Route::post('/organization/members/reject/{id}', [MemberController::class, 'reject'])->name('admin.members.reject');
Route::post('/organization/members/update-position/{id}', [MemberController::class, 'updatePosition'])->name('admin.members.update-position');

// QR Code and Evaluation Routes
Route::post('/generate-qr-attendance', [CurrentEventController::class, 'generateQRAttendance'])->name('generate.qr.attendance');
Route::post('/scan-validate-qr', [CurrentEventController::class, 'scanValidateQR']);
Route::post('/disseminate-evaluation-link', [CurrentEventController::class, 'disseminateEvaluationLink']);
Route::get('/evaluation/form/{event_id}', [EvaluationController::class, 'showEvaluationForm'])->name('evaluation.form');
Route::get('/evaluation/{event_id}', function ($eventId) {
    return view('evaluation_form', ['event_id' => $eventId]);
})->name('evaluation.form');

// Route to fetch post data for editing
Route::get('/posts/{post}', [DashboardController::class, 'getPost'])->name('posts.get');

// Route to update a post
Route::put('/posts/{post}', [DashboardController::class, 'updatePost'])->name('posts.update');
Route::put('/organization/dashboard/{postId}', [DashboardController::class, 'updatePost']);
// Route to delete a post
Route::delete('/posts/{post}', [DashboardController::class, 'destroyPost'])->name('posts.destroy');

Route::get('/check-organization-membership-for-event/{eventId}', [StudentController::class, 'checkOrganizationMembershipForEvent']);

Route::post('/organization/members/remove/{id}', [MemberController::class, 'remove'])->name('members.remove');

Route::get('/check-event-participation/{eventId}', [EventController::class, 'checkParticipation']);

Route::get('/check-event-participation/{eventId}', [StudentController::class, 'checkEventParticipation']);
Route::post('/join-event/{eventId}', [StudentController::class, 'joinEvent']);

Route::put('/posts/{post}', [DashboardController::class, 'updatePost'])->name('posts.update');