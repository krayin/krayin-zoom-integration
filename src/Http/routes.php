<?php
Route::group([
    'prefix'     => 'admin/zoom',
    'namespace'  => 'Webkul\ZoomMeeting\Http\Controllers',
    'middleware' => ['web']
], function () {
    
    Route::group(['middleware' => ['user']], function () {
        Route::get('', 'AccountController@index')->name('admin.zoom_meeting.index');

        Route::get('oauth', 'AccountController@store')->name('admin.zoom_meeting.store');

        Route::delete('{id}', 'AccountController@destroy')->name('admin.zoom_meeting.destroy');

        Route::post('create-link', 'AccountController@createLink')->name('admin.zoom_meeting.create_link');
    });

});