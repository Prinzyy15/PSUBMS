<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/monthly-reports', 'MonthlyReportController@index')->name('monthly-reports.index');
    Route::get('/monthly-reports/create', 'MonthlyReportController@create')->name('monthly-reports.create');
    Route::post('/monthly-reports', 'MonthlyReportController@store')->name('monthly-reports.store');
    Route::get('/monthly-reports/students-with-violations', 'MonthlyReportController@studentsWithViolations');
    Route::get('/monthly-reports/all-students', 'MonthlyReportController@allStudents');
});
