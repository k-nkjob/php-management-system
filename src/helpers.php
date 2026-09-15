<?php
declare(strict_types=1);
function config(string $key,mixed $default=null):mixed{$value=$GLOBALS['app_config']??[];foreach(explode('.',$key) as $segment){if(!is_array($value)||!array_key_exists($segment,$value))return $default;$value=$value[$segment];}return $value;}
function e(?string $value):string{return htmlspecialchars($value??'',ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function url(string $path=''):string{return (string)config('app.base_path','').'/'.ltrim($path,'/');}
function redirect(string $path):never{header('Location: '.url($path));exit;}
function flash(string $type,string $message):void{$_SESSION['_flash'][$type]=$message;}
function pull_flash(string $type):?string{$m=$_SESSION['_flash'][$type]??null;unset($_SESSION['_flash'][$type]);return is_string($m)?$m:null;}
function render_header(string $title):void{$app=e((string)config('app.name'));$user=Auth::user();echo '<!doctype html><html lang="ja"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' | '.$app.'</title><link rel="stylesheet" href="'.e(url('assets/css/style.css')).'"></head><body><header class="site-header"><a class="brand" href="'.e(url()).'">'.$app.'</a>';if($user)echo '<nav><span>'.e($user['name']).'</span><form method="post" action="'.e(url('logout.php')).'"><input type="hidden" name="_token" value="'.e(Csrf::token()).'"><button class="link-button">ログアウト</button></form></nav>';echo '</header><main class="container">';foreach(['success','error'] as $type)if($m=pull_flash($type))echo '<div class="flash flash-'.$type.'">'.e($m).'</div>';}
function render_footer():void{echo '</main><script src="'.e(url('assets/js/main.js')).'" defer></script></body></html>';}
