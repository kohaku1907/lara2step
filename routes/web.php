<?php

Route::group(
    ['as' => '2step::', 'namespace' => 'Kohaku1907\Laravel2step\Http\Controllers', 'middleware' => ['web']],
    function () {
        Route::post('2step-resend', ['uses' => 'TwoStepController@resend'])->name('resend');
    }
);