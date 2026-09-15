<?php
declare(strict_types=1);
final class Auth{
 public static function attempt(string $email,string $password):bool{$s=Database::connection()->prepare('SELECT id,password_hash FROM users WHERE email=:email LIMIT 1');$s->execute(['email'=>mb_strtolower(trim($email))]);$u=$s->fetch();if(!$u||!password_verify($password,$u['password_hash']))return false;session_regenerate_id(true);$_SESSION['user_id']=(int)$u['id'];unset($_SESSION['login_attempts']);return true;}
 public static function check():bool{return isset($_SESSION['user_id'])&&is_int($_SESSION['user_id']);}
 public static function requireLogin():void{if(!self::check()){flash('error','ログインしてください。');redirect('login.php');}}
 public static function user():?array{if(!self::check())return null;$s=Database::connection()->prepare('SELECT id,name,email FROM users WHERE id=:id');$s->execute(['id'=>$_SESSION['user_id']]);return $s->fetch()?:null;}
 public static function logout():void{$_SESSION=[];if(ini_get('session.use_cookies')){$p=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);}session_destroy();}
 public static function tooManyAttempts():bool{$a=$_SESSION['login_attempts']??[];$cut=time()-60;$a=array_values(array_filter($a,fn($t)=>is_int($t)&&$t>=$cut));$_SESSION['login_attempts']=$a;return count($a)>=5;}
 public static function recordFailedAttempt():void{$_SESSION['login_attempts'][]=time();}
}
