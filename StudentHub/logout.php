<?php
require_once __DIR__ . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');
verify_csrf();
if (isset($_COOKIE['studenthub_remember'])) {
  $parts=explode(':',(string)$_COOKIE['studenthub_remember'],2);
  if (count($parts)===2) { try { $conn=db(); $stmt=$conn->prepare('DELETE FROM remember_tokens WHERE selector=?'); $stmt->bind_param('s',$parts[0]); $stmt->execute(); } catch(Throwable $ex) { error_log('Remember-token revoke failed: '.$ex->getMessage()); } }
  setcookie('studenthub_remember','',['expires'=>time()-3600,'path'=>APP_BASE!==''?APP_BASE:'/','httponly'=>true,'samesite'=>'Lax']);
}
$_SESSION=[];
if (ini_get('session.use_cookies')) { $params=session_get_cookie_params(); setcookie(session_name(),'',time()-42000,$params['path'],'',$params['secure'],$params['httponly']); }
session_destroy();
session_start(); flash('success','You have been signed out.'); redirect('index.php');
