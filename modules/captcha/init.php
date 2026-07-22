<?php

if ( ! Route::cache())
{
        // Catch-all route for Captcha classes to run
    Route::set('captcha', 'captcha(/<group>)')->defaults([
        'controller' => 'captcha',
        'action' => 'index',
        'group' => NULL
    ]);
}
