<?php
if (!is_logged_in() && isset($_COOKIE['studenthub_remember'])) {
  $parts=explode(':',(string)$_COOKIE['studenthub_remember'],2);
  if(count($parts)===2 && preg_match('/^[a-f0-9]{18}$/',$parts[0]) && preg_match('/^[a-f0-9]{64}$/',$parts[1])) {
    try {
      $conn=db(); $stmt=$conn->prepare('SELECT rt.user_id,rt.token_hash,rt.expires_at,u.id,u.full_name,u.username,u.email,u.role,u.is_active FROM remember_tokens rt JOIN users u ON u.id=rt.user_id WHERE rt.selector=? LIMIT 1'); $stmt->bind_param('s',$parts[0]); $stmt->execute(); $row=$stmt->get_result()->fetch_assoc();
      if($row && (bool)$row['is_active'] && strtotime($row['expires_at'])>time() && hash_equals($row['token_hash'],hash('sha256',$parts[1]))) { set_authenticated_user($row); }
      else { $del=$conn->prepare('DELETE FROM remember_tokens WHERE selector=?');$del->bind_param('s',$parts[0]);$del->execute();setcookie('studenthub_remember','',['expires'=>time()-3600,'path'=>APP_BASE!==''?APP_BASE:'/','httponly'=>true,'samesite'=>'Lax']); }
    } catch(Throwable $ex) { error_log('Remember token auto-login: '.$ex->getMessage()); }
  }
}
