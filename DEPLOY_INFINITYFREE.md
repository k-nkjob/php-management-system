# InfinityFree deployment

This guide deploys the project while keeping the repository free of production credentials.

## 1. Database

Create an InfinityFree MySQL database and import database/schema.mysql.sql with phpMyAdmin.

## 2. Configuration

Copy config.infinityfree.example.php to config.php on the hosting server. Replace:

- CHANGE_ME_DATABASE_NAME
- CHANGE_ME_DATABASE_USER
- CHANGE_ME_DATABASE_PASSWORD
- CHANGE_ME_RANDOM_INSTALL_KEY

Use a unique install key containing at least 24 random characters. Never commit config.php.

## 3. Upload

Upload the repository contents into the domain's htdocs directory. The top-level .htaccess routes requests into public and blocks direct HTTP access to src, database, tests, configuration and documentation files.

The contents must be directly under htdocs:

    htdocs/.htaccess
    htdocs/config.php
    htdocs/public/
    htdocs/src/
    htdocs/database/

Do not leave the entire project inside an additional php-management-system-main directory.

## 4. Create the first administrator

Open:

    https://YOUR_DOMAIN/setup.php

Enter the install key from config.php and choose the administrator's login details. The setup endpoint refuses all later execution once a user exists.

## 5. Verify

Check login, customer list, prefix search, create, edit, delete and logout. Then confirm that /config.php, /src/ and /database/ return 403.
