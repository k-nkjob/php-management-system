<?php
declare(strict_types=1);
final class Csrf{public static function token():string{if(!isset($_SESSION['_csrf']))$_SESSION['_csrf']=bin2hex(random_bytes(32));return $_SESSION['_csrf'];}public static function verify(?string $token):bool{return is_string($token)&&isset($_SESSION['_csrf'])&&hash_equals($_SESSION['_csrf'],$token);}public static function requireValid(?string $token):void{if(!self::verify($token)){http_response_code(419);exit('Invalid or expired CSRF token.');}}}
