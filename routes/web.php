<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\GoogleController;


Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('events', [GoogleController::class, 'listEvents'])->name('events.list');
Route::get('google/authenticate', [GoogleController::class, 'authenticate'])->name('google.authenticate');
//Route::get('/google/logout', [GoogleController::class, 'logout'])->name('google.logout');



Route::get('events/edit/{id}', [GoogleController::class, 'editEventForm'])->name('google.editEventForm');



Route::get('google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('google/events', [GoogleController::class, 'listEvents'])->name('google.listEvents');
Route::post('google/events/create', [GoogleController::class, 'createEvent'])->name('google.createEvent');
Route::post('google/events/update/{id}', [GoogleController::class, 'updateEvent'])->name('google.updateEvent');
Route::delete('google/events/delete/{id}', [GoogleController::class, 'deleteEvent'])->name('google.deleteEvent');




Route::get('/events', [GoogleController::class, 'listEvents'])->name('google.listEvents');
Route::post('/create-event', [GoogleController::class, 'createEvent'])->name('google.createEvent');
Route::delete('/delete-event/{id}', [GoogleController::class, 'deleteEvent'])->name('google.deleteEvent');
Route::put('/update-event/{id}', [GoogleController::class, 'updateEvent'])->name('google.updateEvent');
Route::get('/edit-event/{id}', [GoogleController::class, 'editEventForm'])->name('google.editEventForm');

// Other Google routes
Route::get('/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('/google/authenticate', [GoogleController::class, 'authenticate'])->name('google.authenticate');





Route::get('/auth/google', [GoogleController::class, 'authenticate'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('/events', [GoogleController::class, 'listEvents'])->name('events.list');
Route::get('/events/edit/{id}', [GoogleController::class, 'editEventForm'])->name('events.edit');
Route::post('/events/create', [GoogleController::class, 'createEvent'])->name('events.create');
Route::post('/events/update/{id}', [GoogleController::class, 'updateEvent'])->name('events.update');
Route::delete('/events/delete/{id}', [GoogleController::class, 'deleteEvent'])->name('events.delete');





Route::get('events', [GoogleController::class, 'listEvents'])->name('google.listEvents');
Route::post('google/createEvent', [GoogleController::class, 'createEvent'])->name('google.createEvent');
Route::put('google/updateEvent/{eventId}', [GoogleController::class, 'updateEvent'])->name('google.updateEvent');
Route::delete('google/deleteEvent/{eventId}', [GoogleController::class, 'deleteEvent'])->name('google.deleteEvent');






//Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
//Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
//Route::get('events', [GoogleController::class, 'listEvents'])->name('events.list');
//Route::get('google/authenticate', [GoogleController::class, 'authenticate'])->name('google.authenticate');
//Route::get('/logout', [GoogleController::class, 'logout'])->name('logout');
//Route::get('/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
//Route::get('/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
//Route::get('/google/logout', [GoogleController::class, 'logout'])->name('google.logout');
//Route::get('/google/events', [GoogleController::class, 'listEvents'])->name('google.events');




Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home.index');

Route::get('/admin/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('admin.login.submit');

Route::group(['middleware' => 'system.auth', 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', [\App\Http\Controllers\SystemAdminController::class, 'index'])->name('index');
    Route::group(['prefix' => 'configuration', 'as' => 'configuration.'], function () {
        Route::get('/', [\App\Http\Controllers\SystemAdminController::class, 'index'])->name('index');
        Route::get('/sso-lists', [\App\Http\Controllers\SystemAdminController::class, 'ssoConfigurationLists'])->name('sso-lists');
        Route::post('/create-sso', [\App\Http\Controllers\SystemAdminController::class, 'createSSOConfiguration'])->name('sso.create');
        Route::post('/store-sso', [\App\Http\Controllers\SystemAdminController::class, 'storeSSOConfiguration'])->name('sso.store');
        Route::post('/set-sso', [\App\Http\Controllers\SystemAdminController::class, 'setSSO'])->name('sso.set');
    });
});

Route::group(['middleware' => 'ndoptor.auth'], function () {
   
    Route::get('/dashboard', [\App\Http\Controllers\HomeController::class, 'dashboard'])->name('home.dashboard');
    Route::get('/dashboard/load-daily', [\App\Http\Controllers\HomeController::class, 'dailyDashboard'])->name('home.dashboard.daily');
    // Main Calendar
    Route::group(['as' => 'cal-event.'], function () {
        Route::get('/calendar-view', [\App\Http\Controllers\CalEventController::class, 'index'])->name('view');
        Route::get('/load-all-events/email', [\App\Http\Controllers\CalEventController::class, 'loadAllEventsByMail'])->name('all.email');
        Route::get('/load-all-events/officer', [\App\Http\Controllers\CalEventController::class, 'loadAllEventsByOfficerId'])->name('all.officer-id');
        Route::post('/load-calendars', [\App\Http\Controllers\CalEventController::class, 'loadCalendars'])->name('load-calendars');
        Route::post('/create-event', [\App\Http\Controllers\CalEventController::class, 'create'])->name('create-event');
        Route::post('/show-event', [\App\Http\Controllers\CalEventController::class, 'show'])->name('show-event');
        Route::post('/edit-event', [\App\Http\Controllers\CalEventController::class, 'edit'])->name('edit-event');
        Route::post('/update-event', [\App\Http\Controllers\CalEventController::class, 'update'])->name('update-event');
        Route::post('/store-event', [\App\Http\Controllers\CalEventController::class, 'store'])->name('store-event');
        Route::post('/unassign-event-user', [\App\Http\Controllers\CalEventController::class, 'unAssignEventUser'])->name('unassign-event-user');
        Route::post('/delete-event', [\App\Http\Controllers\CalEventController::class, 'destroy'])->name('delete-event');
    });

    Route::group(['as' => 'tasks.', 'prefix' => 'tasks'], function () {
        Route::get('/index', [\App\Http\Controllers\TaskController::class, 'index'])->name('index');
        Route::get('/load-all-tasks', [\App\Http\Controllers\TaskController::class, 'loadAllTasks'])->name('load-all-tasks');
        Route::post('/list', [\App\Http\Controllers\TaskController::class, 'getTasksList'])->name('list');
        Route::post('/delete', [\App\Http\Controllers\API\V1\TaskController::class, 'destroy'])->name('delete');

        Route::post('/create', [\App\Http\Controllers\TaskController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\TaskController::class, 'store'])->name('store');
        Route::post('/show', [\App\Http\Controllers\TaskController::class, 'show'])->name('show');

        Route::post('/edit', [\App\Http\Controllers\TaskController::class, 'edit'])->name('edit');
        Route::post('/update', [\App\Http\Controllers\TaskController::class, 'update'])->name('update');
        Route::post('/update/status', [\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('update.status');
        Route::post('/search-assignee', [\App\Http\Controllers\TaskController::class, 'searchAssignee'])->name('search-assignee');
        Route::post('/daily', [\App\Http\Controllers\TaskController::class, 'daily'])->name('daily');
        Route::post('/pending', [\App\Http\Controllers\TaskController::class, 'pendingTask'])->name('pending');
        Route::post('/update/comment', [\App\Http\Controllers\TaskController::class, 'updateComment'])->name('update.comment');
        Route::post('user/assign/panel', [\App\Http\Controllers\TaskController::class, 'assignTaskPanel'])->name('user.assign.panel');
        Route::post('user/assign', [\App\Http\Controllers\TaskController::class, 'assignTaskMultiple'])->name('user.assign');
        Route::post('user/assigned', [\App\Http\Controllers\TaskController::class, 'assignedUser'])->name('user.assigned');
        Route::post('user/unassign-user', [\App\Http\Controllers\TaskController::class, 'unAssignUser'])->name('user.unassign-user');

        Route::post('search/assigned/user-info', [\App\Http\Controllers\TaskController::class, 'searchAssignedTaskByUserInfo'])->name('search.assigned.user-info');
        Route::post('search/assigned/task-info', [\App\Http\Controllers\TaskController::class, 'searchAssignedTaskByTaskInfo'])->name('search.assigned.task-info');
        Route::post('search/assigned/datetime-range', [\App\Http\Controllers\TaskController::class, 'searchAssignedTaskByDateTimeRange'])->name('search.assigned.datetime-range');

        Route::post('/users', [\App\Http\Controllers\TaskController::class, 'getTaskUsers'])->name('users');

        Route::group(['as' => 'comments.', 'prefix' => 'comments'], function () {
            Route::post('/panel', [\App\Http\Controllers\TaskCommentController::class, 'loadCommentPanel'])->name('panel');
            Route::post('/get-by-officer-id', [\App\Http\Controllers\TaskCommentController::class, 'getComments'])->name('get-by-officer-id');
            Route::post('/save', [\App\Http\Controllers\TaskCommentController::class, 'saveComment'])->name('save');
        });
    });

    Route::group(['as' => 'settings.', 'prefix' => 'settings'], function () {
        Route::get('/index', [\App\Http\Controllers\SettingsController::class, 'index'])->name('index');
        Route::get('/notifications', [\App\Http\Controllers\SettingsController::class, 'loadNotifications'])->name('notifications.view');
        Route::post('/notifications/change', [\App\Http\Controllers\SettingsController::class, 'changeUserNotificationSetting'])->name('notifications.change');
    });

    Route::group(['as' => 'guests.', 'prefix' => 'guests'], function () {
        Route::get('/load-office-layer-wise', [\App\Http\Controllers\CalEventPreferredGuestController::class, 'loadOfficeLayerWise'])->name('load-office-layer-wise');
        Route::get('/load-custom-layer-level-wise', [\App\Http\Controllers\CalEventPreferredGuestController::class, 'loadCustomLayerLevelWise'])->name('load-custom-layer-level-wise');
        Route::get('/load-office-custom-layer-wise', [\App\Http\Controllers\CalEventPreferredGuestController::class, 'loadOfficeCustomLayerWise'])->name('load-office-custom-layer-wise');
        Route::get('/load-office-origin-layer-level-wise', [\App\Http\Controllers\CalEventPreferredGuestController::class, 'loadOfficeOriginLayerLevelWise'])->name('load-office-origin-layer-level-wise');
        Route::get('/load-unit-office-wise', [\App\Http\Controllers\CalEventPreferredGuestController::class, 'loadUnitOfficeWise'])->name('load-unit-office-wise');
        Route::get('/load-office-origin-wise', [\App\Http\Controllers\CalEventPreferredGuestController::class, 'loadOfficeOriginWise'])->name('load-office-origin-wise');
        Route::post('/search-preferred-users', [\App\Http\Controllers\CalEventPreferredGuestController::class, 'searchPreferredUsers'])->name('search-preferred-users');
    });

    //Generic
    Route::get('change/office/{id}/{office_id}/{office_unit_id}/{designation_id}', [App\Http\Controllers\ChangeController::class, 'changeDesignation'])->name('change.office');

    Route::get('/notifications/lists', [\App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications.lists');
});

// Google Calendar
//Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
//Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
//Route::get('events', [GoogleController::class, 'listEvents'])->name('events.list');
//Route::get('google/authenticate', [GoogleController::class, 'authenticate'])->name('google.authenticate');



// 3 line code
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    Artisan::call('route:clear');
    return "Cache is cleared";
});
Route::get('/clear-log', function () {
    exec('rm -f ' . storage_path('logs/*.log'));
    exec('rm -f ' . base_path('*.log'));
    return 'Logs have been cleared!';
});




