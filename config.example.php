<?php
declare(strict_types=1);
return [
 'app'=>['name'=>getenv('APP_NAME')?:'PHP Management System','base_path'=>rtrim(getenv('APP_BASE_PATH')?:'','/')],
 'database'=>['driver'=>getenv('DB_DRIVER')?:'sqlite','sqlite_path'=>getenv('DB_PATH')?:__DIR__.'/database/app.sqlite','host'=>getenv('DB_HOST')?:'127.0.0.1','port'=>getenv('DB_PORT')?:'3306','name'=>getenv('DB_NAME')?:'','user'=>getenv('DB_USER')?:'','password'=>getenv('DB_PASSWORD')?:'','charset'=>'utf8mb4'],
];
