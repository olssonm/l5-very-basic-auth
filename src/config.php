<?php

    /**
     * Configuration for the "HTTP Very Basic Auth"-middleware
     */
    return [
        // Environments where the middleware is active
        'envs'              => ['local', 'development', 'testing'],

        // Username
        'user'              => 'admin',

        // Password
        'password'          => 'admin123',

        // Message to display if the user "opts out"/clicks "cancel"
        'error_message'     => 'You must supply your credentials to access this resource.'
    ];
