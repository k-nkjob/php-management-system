<?php
declare(strict_types=1);
$root=dirname(__DIR__);$file=$root.'/config.php';$GLOBALS['app_config']=require is_file($file)?$file:$root.'/config.example.php';
foreach(['helpers','Database','Auth','Csrf','Validator','CustomerRepository'] as $name)require_once __DIR__.'/'.$name.'.php';
$https=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')||(($_SERVER['HTTP_X_FORWARDED_PROTO']??'')==='https');
session_name('pms_session');session_set_cookie_params(['lifetime'=>0,'path'=>url(),'secure'=>$https,'httponly'=>true,'samesite'=>'Lax']);if(session_status()!==PHP_SESSION_ACTIVE)session_start();
header('X-Content-Type-Options: nosniff');header('X-Frame-Options: DENY');header('Referrer-Policy: strict-origin-when-cross-origin');header("Content-Security-Policy: default-src 'self'; style-src 'self'; script-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");
