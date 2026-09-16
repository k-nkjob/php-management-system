<?php
declare(strict_types=1);

/*
 * Copy this file to config.php on InfinityFree and replace every
 * CHANGE_ME value. config.php is ignored by Git and blocked by .htaccess.
 */
return [
    'app' => [
        'name' => 'PHP Management System',
        'base_path' => '',
        'debug' => false,
    ],
    'database' => [
        'driver' => 'mysql',
        'host' => 'sql102.infinityfree.com',
        'port' => '3306',
        'name' => 'CHANGE_ME_DATABASE_NAME',
        'user' => 'CHANGE_ME_DATABASE_USER',
        'password' => 'CHANGE_ME_DATABASE_PASSWORD',
        'charset' => 'utf8mb4',
    ],
    'install' => [
        // Use a new random value of at least 24 characters.
        'key' => 'CHANGE_ME_RANDOM_INSTALL_KEY',
    ],
];
