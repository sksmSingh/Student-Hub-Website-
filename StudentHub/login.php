<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (is_logged_in()) redirect(is_admin()?'admin/dashboard.php':'student/dashboard.php');
$pageTitle='Sign in'; $error=''; $identifier='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  verify_csrf(); $identifier=trim((string)($_POST['identifier']??'')); $password=(string)($_POST['password']??''); $remember=!empty($_POST['remember']);
  if ($identifier==='' || $password==='') $error='Enter your username/email and password.';
  else {
    try {
      $conn=db(); $stmt=$conn->prepare('SELECT id,full_name,username,email,password_hash,role,is_active FROM users WHERE email=? OR username=? LIMIT 1'); $stmt->bind_param('ss',$identifier,$identifier); $stmt->execute(); $user=$stmt->get_result()->fetch_assoc();
      if (!$user || !(bool)$user['is_active'] || !password_verify($password,$user['password_hash'])) $error='Invalid sign-in details. Please try again.';
      else {
        if (password_needs_rehash($user['password_hash'],PASSWORD_DEFAULT)) { $newHash=password_hash($password,PASSWORD_DEFAULT); $update=$conn->prepare('UPDATE users SET password_hash=? WHERE id=?'); $update->bind_param('si',$newHash,$user['id']); $update->execute(); }
        set_authenticated_user($user);
        if ($remember) {
          $selector=bin2hex(random_bytes(9)); $validator=bin2hex(random_bytes(32)); $tokenHash=hash('sha256',$validator); $expires=(new DateTimeImmutable('+30 days'))->format('Y-m-d H:i:s');
          $ins=$conn->prepare('INSERT INTO remember_tokens (user_id,selector,token_hash,expires_at) VALUES (?,?,?,?)'); $ins->bind_param('isss',$user['id'],$selector,$tokenHash,$expires); $ins->execute();
          setcookie('studenthub_remember',$selector.':'.$validator,['expires'=>time()+60*60*24*30,'path'=>APP_BASE!==''?APP_BASE:'/','secure'=>!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off','httponly'=>true,'samesite'=>'Lax']);
        }
        redirect($user['role']==='admin'?'admin/dashboard.php':'student/dashboard.php');
      }
    } catch(Throwable $ex) { error_log('Login error: '.$ex->getMessage()); $error='Sign-in is temporarily unavailable. Please check the database setup.'; }
  }
}
require __DIR__ . '/includes/header.php';
?>
<div class="auth-shell"><aside class="auth-aside"><p class="eyebrow">Welcome back</p><h1 style="font-size:2.5rem">Your campus, connected.</h1><p>Sign in to view your portal, discover events and manage your student activity.</p><p>For local testing, create an admin account with <code>scripts/create_admin.php</code>.</p></aside><section class="auth-main"><p class="eyebrow">StudentHub account</p><h2>Sign in</h2><?php if($error): ?><div class="notice error" role="alert"><?=e($error)?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><div class="field"><label for="identifier">Email or username</label><input id="identifier" name="identifier" required autocomplete="username" value="<?=e($identifier)?>"></div><div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="current-password"></div><div class="field"><label class="checkbox-row"><input type="checkbox" name="remember" value="1"><span>Remember me for 30 days on this device</span></label></div><button class="button" type="submit">Sign in</button><p class="help-text">New to StudentHub? <a href="<?=e(app_url('register.php'))?>">Create a student account</a>.</p></form></section></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
