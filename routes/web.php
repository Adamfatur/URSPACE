<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\ThreadController;

Route::get('/', [ThreadController::class, 'index'])->name('home');
Route::get('/search/query', [App\Http\Controllers\SearchController::class, 'search'])->name('search.query');

Route::middleware('auth')->group(function () {
    Route::get('/threads/create', [ThreadController::class, 'create'])->name('threads.create');
    Route::post('/threads', [ThreadController::class, 'store'])->name('threads.store');
    Route::put('/threads/{thread}', [ThreadController::class, 'update'])->name('threads.update');
    Route::delete('/threads/{thread}', [ThreadController::class, 'destroy'])->name('threads.destroy');
    Route::post('/threads/{thread}/posts', [App\Http\Controllers\PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [App\Http\Controllers\PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/hide', [App\Http\Controllers\PostController::class, 'hide'])->name('posts.hide');
    Route::post('/posts/{post}/pin', [App\Http\Controllers\PostController::class, 'togglePin'])->name('posts.pin');

    Route::post('/profile/{user}/follow', [
        App\Http\Controllers\ProfileController::class,
        'follow'
    ])->name('profile.follow');
    Route::post('/likes/{type}/{id}', [App\Http\Controllers\LikeController::class, 'toggle'])->name('likes.toggle');
    Route::post('/report/{type}/{id}', [App\Http\Controllers\ReportController::class, 'store'])->name('reports.store');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // Bookmarks
    Route::get('/bookmarks', [App\Http\Controllers\BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks/{thread}/toggle', [App\Http\Controllers\BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
});

// Email verification (manual users)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->intended('/');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Admin Routes
Route::middleware(['auth', 'role:global_admin,univ_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Space Approval
    Route::get('/spaces/pending', [App\Http\Controllers\Admin\SpaceApprovalController::class, 'index'])->name('spaces.pending');
    Route::post('/spaces/{space}/approve', [App\Http\Controllers\Admin\SpaceApprovalController::class, 'approve'])->name('spaces.approve');
    Route::post('/spaces/{space}/reject', [App\Http\Controllers\Admin\SpaceApprovalController::class, 'reject'])->name('spaces.reject');

    // User Management & Shadow Ban
    Route::get('/users', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/shadow-ban', [App\Http\Controllers\Admin\UserManagementController::class, 'shadowBan'])->name('users.shadow-ban');
    Route::delete('/users/{user}/shadow-ban', [App\Http\Controllers\Admin\UserManagementController::class, 'removeShadowBan'])->name('users.remove-shadow-ban');

    // Thread Moderation
    Route::get('/hidden-content', [App\Http\Controllers\Admin\ContentModerationController::class, 'hiddenIndex'])->name('hidden-content.index');
    Route::post('/threads/{thread}/hide', [App\Http\Controllers\Admin\ContentModerationController::class, 'hideThread'])->name('threads.hide');
    Route::delete('/threads/{thread}', [App\Http\Controllers\Admin\ContentModerationController::class, 'destroyThread'])->name('threads.destroy');

    // Global Announcements
    Route::get('/announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::post('/announcements/{announcement}/toggle', [App\Http\Controllers\Admin\AnnouncementController::class, 'toggle'])->name('announcements.toggle');

    // Reports Management
    Route::get('/reports', [App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports/{report}/resolve', [App\Http\Controllers\Admin\ReportsController::class, 'resolve'])->name('reports.resolve');
    Route::post('/reports/{report}/dismiss', [App\Http\Controllers\Admin\ReportsController::class, 'dismiss'])->name('reports.dismiss');
    Route::post('/reports/bulk-resolve', [App\Http\Controllers\Admin\ReportsController::class, 'bulkResolve'])->name('reports.bulk-resolve');
    Route::post('/reports/bulk-dismiss', [App\Http\Controllers\Admin\ReportsController::class, 'bulkDismiss'])->name('reports.bulk-dismiss');

    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');

    // AI Dashboard
    Route::get('/ai', [App\Http\Controllers\Admin\AIController::class, 'index'])->name('ai.dashboard');
    Route::post('/ai/configure', [App\Http\Controllers\Admin\AIController::class, 'configure'])->name('ai.configure');
});

// Moderator Routes
Route::middleware(['auth', 'role:moderator,global_admin'])->prefix('moderator')->name('moderator.')->group(function () {
    Route::get('/', [App\Http\Controllers\Moderator\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/reports/{report}', [
        App\Http\Controllers\Moderator\DashboardController::class,
        'handle'
    ])->name('reports.handle');
});

// Profile Routes (Auth Required)
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::post('/edit', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
    Route::post('/open-to-work', [App\Http\Controllers\ProfileController::class, 'toggleOpenToWork'])->name('toggleOpenToWork');

    // Experience
    Route::post('/experience', [App\Http\Controllers\ProfileController::class, 'storeExperience'])->name('experience.store');
    Route::put('/experience/{experience}', [App\Http\Controllers\ProfileController::class, 'updateExperience'])->name('experience.update');
    Route::delete('/experience/{experience}', [App\Http\Controllers\ProfileController::class, 'destroyExperience'])->name('experience.destroy');

    // Education
    Route::post('/education', [App\Http\Controllers\ProfileController::class, 'storeEducation'])->name('education.store');
    Route::put('/education/{education}', [App\Http\Controllers\ProfileController::class, 'updateEducation'])->name('education.update');
    Route::delete('/education/{education}', [App\Http\Controllers\ProfileController::class, 'destroyEducation'])->name('education.destroy');

    // Skills
    Route::post('/skill', [App\Http\Controllers\ProfileController::class, 'storeSkill'])->name('skill.store');
    Route::delete('/skill/{skill}', [App\Http\Controllers\ProfileController::class, 'destroySkill'])->name('skill.destroy');

    // Certifications
    Route::post('/certification', [App\Http\Controllers\ProfileController::class, 'storeCertification'])->name('certification.store');
    Route::put('/certification/{certification}', [App\Http\Controllers\ProfileController::class, 'updateCertification'])->name('certification.update');
    Route::delete('/certification/{certification}', [App\Http\Controllers\ProfileController::class, 'destroyCertification'])->name('certification.destroy');
});

// Spaces
Route::get('spaces', [App\Http\Controllers\SpaceController::class, 'index'])->name('spaces.index');
Route::get('spaces/{space}', [App\Http\Controllers\SpaceController::class, 'show'])->name('spaces.show');

Route::middleware('auth')->group(function () {
    Route::get('spaces/create', [App\Http\Controllers\SpaceController::class, 'create'])->name('spaces.create');
    Route::post('spaces', [App\Http\Controllers\SpaceController::class, 'store'])->name('spaces.store');
    Route::get('spaces/{space}/edit', [App\Http\Controllers\SpaceController::class, 'edit'])->name('spaces.edit');
    Route::put('spaces/{space}', [App\Http\Controllers\SpaceController::class, 'update'])->name('spaces.update');
    Route::delete('spaces/{space}', [App\Http\Controllers\SpaceController::class, 'destroy'])->name('spaces.destroy');

    Route::post('spaces/{space}/join', [App\Http\Controllers\SpaceMemberController::class, 'join'])->name('spaces.join');
    Route::post('spaces/{space}/leave', [App\Http\Controllers\SpaceMemberController::class, 'leave'])->name('spaces.leave');

    // Space Announcements
    Route::post('spaces/{space}/announcements', [App\Http\Controllers\SpaceAnnouncementController::class, 'store'])->name('spaces.announcements.store');
    Route::delete('spaces/{space}/announcements/{announcement}', [App\Http\Controllers\SpaceAnnouncementController::class, 'destroy'])->name('spaces.announcements.destroy');

    // Space Events
    Route::get('spaces/{space}/events', [App\Http\Controllers\SpaceEventController::class, 'index'])->name('spaces.events.index');
    Route::get('spaces/{space}/events/create', [App\Http\Controllers\SpaceEventController::class, 'create'])->name('spaces.events.create');
    Route::post('spaces/{space}/events', [App\Http\Controllers\SpaceEventController::class, 'store'])->name('spaces.events.store');
    Route::get('spaces/{space}/events/{event}', [App\Http\Controllers\SpaceEventController::class, 'show'])->name('spaces.events.show');
    Route::put('spaces/{space}/events/{event}', [App\Http\Controllers\SpaceEventController::class, 'update'])->name('spaces.events.update');
    Route::post('spaces/{space}/events/{event}/rsvp', [App\Http\Controllers\SpaceEventController::class, 'rsvp'])->name('spaces.events.rsvp');
    Route::post('spaces/{space}/events/{event}/share', [App\Http\Controllers\SpaceEventController::class, 'share'])->name('spaces.events.share');

    // Event Announcements
    Route::post('spaces/{space}/events/{event}/announcements', [App\Http\Controllers\SpaceEventController::class, 'storeAnnouncement'])->name('spaces.events.announcements.store');

    // Event Voting
    Route::post('spaces/{space}/events/{event}/votes', [App\Http\Controllers\EventVoteController::class, 'store'])->name('spaces.events.votes.store');
    Route::post('spaces/{space}/events/{event}/votes/{vote}/cast', [App\Http\Controllers\EventVoteController::class, 'castVote'])->name('spaces.events.votes.cast');

    // Event Brackets
    Route::post('spaces/{space}/events/{event}/brackets', [App\Http\Controllers\EventBracketController::class, 'store'])->name('spaces.events.brackets.store');
    Route::post('spaces/{space}/events/{event}/brackets/{bracket}/participants', [App\Http\Controllers\EventBracketController::class, 'addParticipant'])->name('spaces.events.brackets.participants.add');
    Route::post('spaces/{space}/events/{event}/brackets/{bracket}/generate', [App\Http\Controllers\EventBracketController::class, 'generateMatches'])->name('spaces.events.brackets.generate');
    Route::post('spaces/{space}/events/{event}/brackets/{bracket}/matches/{match}/result', [App\Http\Controllers\EventBracketController::class, 'updateMatchResult'])->name('spaces.events.brackets.matches.result');
    // Member Management
    Route::post('spaces/{space}/members/add', [App\Http\Controllers\SpaceMemberController::class, 'addMember'])->name('spaces.members.add');
    Route::put('spaces/{space}/members/{user}/role', [App\Http\Controllers\SpaceMemberController::class, 'updateRole'])->name('spaces.members.role');
    Route::delete('spaces/{space}/members/{user}', [App\Http\Controllers\SpaceMemberController::class, 'removeMember'])->name('spaces.members.remove');

    // Member Moderation (Kick, Ban, Unban, Report)
    Route::delete('spaces/{space}/members/{user}/kick', [App\Http\Controllers\SpaceMemberController::class, 'kick'])->name('spaces.members.kick');
    Route::post('spaces/{space}/members/{user}/ban', [App\Http\Controllers\SpaceMemberController::class, 'ban'])->name('spaces.members.ban');
    Route::delete('spaces/{space}/bans/{user}', [App\Http\Controllers\SpaceMemberController::class, 'unban'])->name('spaces.members.unban');
    Route::post('spaces/{space}/members/{user}/report', [App\Http\Controllers\SpaceMemberController::class, 'report'])->name('spaces.members.report');

    // Space Thread Pinning
    Route::post('spaces/{space}/threads/{thread}/pin', [App\Http\Controllers\SpaceController::class, 'pinThread'])->name('spaces.threads.pin');

    // Join Requests (for private spaces)
    Route::post('spaces/{space}/request-join', [App\Http\Controllers\SpaceJoinRequestController::class, 'store'])->name('spaces.request-join');
    Route::delete('spaces/{space}/cancel-join', [App\Http\Controllers\SpaceJoinRequestController::class, 'destroy'])->name('spaces.cancel-join');
    Route::post('spaces/{space}/join-requests/{request}/approve', [App\Http\Controllers\SpaceJoinRequestController::class, 'approve'])->name('spaces.join-requests.approve');
    Route::post('spaces/{space}/join-requests/{request}/reject', [App\Http\Controllers\SpaceJoinRequestController::class, 'reject'])->name('spaces.join-requests.reject');
});


Route::get('/threads/{thread}', [ThreadController::class, 'show'])->name('threads.show');
Route::post('/threads/{thread}/vote', [ThreadController::class, 'vote'])->name('threads.vote')->middleware('auth');
Route::get('/rules', [ThreadController::class, 'rules'])->name('rules');


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginView'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Social Login
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    Route::get('/register', [AuthController::class, 'registerView'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/2fa/verify', [AuthController::class, 'verify2faView'])->name('2fa.verify');
    Route::post('/2fa/verify', [AuthController::class, 'verify2fa'])->name('2fa.verify.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/{user:username}', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');