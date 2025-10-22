<?php
// Parent Dashboard API endpoints
Route::get('/parent/linked-students', 'HomeController@parentLinkedStudents')->middleware('auth');
Route::get('/parent/incidents', 'HomeController@parentIncidents')->middleware('auth');
Route::get('/parent/behavior-insights', 'HomeController@parentBehaviorInsights')->middleware('auth');
Route::get('/parent/monthly-reports', 'HomeController@parentMonthlyReports')->middleware('auth');
Route::get('/parent/messages', 'HomeController@parentMessages')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

// Monthly Reports routes
require_once __DIR__.'/monthly_reports.php';

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/add-student', 'HomeController@addStudent')->name('add-student');

Route::get('/student/{id}', 'HomeController@student')->name('student');
Route::get('/get-student/{id}', 'HomeController@getStudent')->name('get-student');
Route::post('/update-student', 'HomeController@updateStudent')->name('update-student');
Route::post('/delete-student/{id}', 'HomeController@deleteStudent')->name('delete-student');
Route::post('/check-student', 'HomeController@checkStudent')->name('check-student');

Route::post('/add-student-violation', 'HomeController@addStudentViolation')->name('add-student-violation');
Route::get('/get-student-violation/{id}', 'HomeController@getStudentViolation')->name('get-student-violation');
Route::post('/update-student-violation', 'HomeController@updateStudentViolation')->name('update-student-violation');
Route::post('/delete-student-violation/{id}', 'HomeController@deleteStudentViolation')->name('delete-student-violation');

Route::post('/add-parent', 'HomeController@addParent')->name('add-parent');
Route::get('/get-parent/{id}', 'HomeController@getParent')->name('get-parent');
Route::post('/update-parent', 'HomeController@updateParent')->name('update-parent');
Route::post('/delete-parent/{id}', 'HomeController@deleteParent')->name('delete-parent');

Route::post('/add-contact', 'HomeController@addContact')->name('add-contact');
Route::post('/reset-parent-password', 'HomeController@resetParentPassword')->name('reset-parent-password');
Route::get('/get-contact/{id}', 'HomeController@getContact')->name('get-contact');
Route::post('/update-contact', 'HomeController@updateContact')->name('update-contact');
Route::post('/delete-contact/{id}', 'HomeController@deleteContact')->name('delete-contact');

Route::get('/inquiry', 'StudentController@inquiry')->name('inquiry');
Route::post('/inquiryViolation', 'StudentController@inquiryViolation')->name('inquiry-violation');
Route::get('/messages', 'HomeController@messages')->name('messages');
Route::post('/add-message', 'StudentController@addMessage')->name('add-message');
Route::post('/add-appointment', 'HomeController@addAppointment')->name('add-appointment');
Route::post('/get-student-appointment/id/{id}', 'HomeController@getStudentAppointment')->name('get-student-appointment');

Route::get('/users', 'HomeController@users')->name('users');
Route::get('/get-user/{id}', 'HomeController@getUser')->name('get-user');
Route::post('/add-user', 'HomeController@addUser')->name('add-user');
Route::post('/update-user', 'HomeController@updateUser')->name('update-user');
Route::post('/delete-user/{id}', 'HomeController@deleteUser')->name('delete-user');

Route::get('/violations', 'HomeController@violations')->name('violations');
Route::get('/statistics', 'HomeController@statistics')->name('statistics');
Route::get('/statistics/data', 'HomeController@statisticsData')->name('statistics.data');
Route::post('/add-violation', 'HomeController@addViolation')->name('add-violation');
Route::post('/delete-violation/{id}', 'HomeController@deleteViolation')->name('delete-violation');
Route::get('/get-violation/{id}', 'HomeController@getViolation')->name('get-violation');
Route::get('/get-violators/{id}', 'HomeController@getViolators')->name('get-violators');
Route::post('/update-violation', 'HomeController@updateViolation')->name('update-violation');
Route::post('/check-violation-code', 'HomeController@checkViolationCode')->name('check-violation-code');

Route::get('/course', 'HomeController@course')->name('course');
Route::post('/add-course', 'HomeController@addCourse')->name('add-course');
Route::get('/get-course/{id}', 'HomeController@getCourse')->name('get-course');
Route::post('/update-course', 'HomeController@updateCourse')->name('update-course');
Route::post('/delete-course/{id}', 'HomeController@deleteCourse')->name('delete-course');

Route::get('/block', 'HomeController@block')->name('block');
Route::post('/add-block', 'HomeController@addBlock')->name('add-block');
Route::get('/get-block/{id}', 'HomeController@getBlock')->name('get-block');
Route::post('/update-block', 'HomeController@updateBlock')->name('update-block');
Route::post('/delete-block/{id}', 'HomeController@deleteBlock')->name('delete-block');


// Prototype preview routes (Tailwind)
Route::get('/prototype/tailwind-students', function(){
    return view('tailwind-students');
});
Route::get('/prototype/tailwind-dashboard', function(){
    return view('prototype.tailwind-dashboard');
});
Route::get('/prototype/tailwind-student-profile', function(){
    return view('prototype.tailwind-student-profile');
});

// Theme demo route for quick visual verification
Route::get('/theme-demo', function(){
    return view('theme-demo');
});

// Debug endpoint: quick information about the theme CSS file (helps confirm server-side asset)
Route::get('/debug-theme-css', function(){
    // only expose in debug mode
    if(!config('app.debug')){
        abort(404);
    }

    $path = public_path('css/theme-dashboard.css');
    $exists = file_exists($path);
    $mtime = $exists ? filemtime($path) : null;
    $size = $exists ? filesize($path) : null;
    $snippet = null;
    if($exists){
        $contents = @file_get_contents($path);
        $snippet = $contents ? substr($contents, 0, 512) : null;
    }
    return response()->json([
        'exists' => $exists,
        'file' => $exists ? str_replace(base_path(), '', $path) : null,
        'mtime' => $mtime,
        'size' => $size,
        'snippet' => $snippet,
    ]);
});

