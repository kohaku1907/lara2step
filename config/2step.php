<?php

return [
    'default_channel' => 'email',
    /*
    |--------------------------------------------------------------------------
    | Two-Step Authentication Table
    |--------------------------------------------------------------------------
    |
    | This is the table used to store the 2FA codes. You may change this
    | value to anything you like. Just make sure to update the table
    | name accordingly.
    |
    */
    'table_name' => 'two_step_auths',

    /*
    |--------------------------------------------------------------------------
    | Two-Step Authentication Code Length
    |--------------------------------------------------------------------------
    |
    | This is the length of the 2FA code. The default is 4.
    |
    */
    'code_length' => 4,

    'numeric_code' => false,

    /*
    |--------------------------------------------------------------------------
    | Require Two-Step Middleware
    |--------------------------------------------------------------------------
    |
    | The "2step.confirm" middleware acts as a gatekeeper to a route by asking
    | the user to confirm with a 2FA Code. This configuration sets the key
    | of the session to remember the data and how much time to remember.
    |
    | Time is set in minutes.
    |
    */

    'confirm_key' => '_2fa',
    'timeout' => 60,
    'max_attempts' => 5,
    'exceed_countdown_minutes' => 1440,
    'resend_code_seconds' => 60,



];
