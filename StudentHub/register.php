<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (is_logged_in()) redirect(is_admin()?'admin/dashboard.php':'student/dashboard.php');
$pageTitle='Student registration'; $errors=[]; $values=['full_name'=>'','username'=>'','email'=>'','mobile'=>'','course'=>'','year'=>'','gender'=>''];
if ($_SERVER['REQUEST_METHOD']==='POST') {
  verify_csrf();
  foreach ($values as $key=>$_) $values[$key]=trim((string)($_POST[$key]??''));
  $password=(string)($_POST['password']??''); $confirm=(string)($_POST['confirm_password']??'');
  if (!preg_match("/^[\\p{L}][\\p{L} .'-]{1,79}$/u",$values['full_name'])) $errors['full_name']='Enter a valid full name.';
  if (!preg_match('/^[A-Za-z][A-Za-z0-9_]{3,23}$/',$values['username'])) $errors['username']='Use 4–24 letters, numbers or underscores; start with a letter.';
  if (!filter_var($values['email'],FILTER_VALIDATE_EMAIL) || strlen($values['email'])>190) $errors['email']='Enter a valid email address.';
  if (!preg_match('/^[6-9][0-9]{9}$/',$values['mobile'])) $errors['mobile']='Enter a valid 10-digit Indian mobile number.';
  if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,64}$/',$password)) $errors['password']='Use 8–64 characters with uppercase, lowercase, number and symbol.';
  if ($password!==$confirm) $errors['confirm_password']='Passwords do not match.';
  if (!in_array($values['course'],['Computer Engineering','Information Technology','Computer Science','Design','Business'],true)) $errors['course']='Choose a valid course.';
  if (!in_array($values['year'],['1','2','3','4'],true)) $errors['year']='Choose your current year.';
  if (!in_array($values['gender'],['Female','Male','Non-binary','Prefer not to say'],true)) $errors['gender']='Choose a gender option.';
  if (empty($_POST['terms'])) $errors['terms']='Accept the terms to continue.';
  if (!$errors) {
    try {
      $conn=db();
      $check=$conn->prepare('SELECT id FROM users WHERE email=? OR username=? LIMIT 1'); $check->bind_param('ss',$values['email'],$values['username']); $check->execute();
      if ($check->get_result()->fetch_assoc()) $errors['general']='That email address or username is already registered.';
      else {
        $conn->begin_transaction();
        $hash=password_hash($password,PASSWORD_DEFAULT); $role='student';
        $stmt=$conn->prepare('INSERT INTO users (full_name,username,email,mobile,password_hash,role) VALUES (?,?,?,?,?,?)');
        $stmt->bind_param('ssssss',$values['full_name'],$values['username'],$values['email'],$values['mobile'],$hash,$role); $stmt->execute(); $userId=$conn->insert_id;
        $student=$conn->prepare('INSERT INTO students (user_id,full_name,email,mobile,course,year_of_study,gender) VALUES (?,?,?,?,?,?,?)');
        $student->bind_param('issssis',$userId,$values['full_name'],$values['email'],$values['mobile'],$values['course'],$values['year'],$values['gender']); $student->execute();
        $conn->commit();
        $record=['registered_at'=>date(DATE_ATOM),'user_id'=>$userId,'full_name'=>$values['full_name'],'username'=>$values['username'],'email'=>$values['email'],'mobile'=>$values['mobile'],'course'=>$values['course'],'year'=>$values['year'],'gender'=>$values['gender']];
      }
    } catch (Throwable $ex) {
      if (isset($conn) && $conn instanceof mysqli) { try {$conn->rollback();} catch(Throwable $ignored){} }
      error_log('Registration error: '.$ex->getMessage()); $errors['general']='We could not complete registration. Check the database setup and try again.';
    }
  }
}
if (isset($userId) && !$errors) {
  $storage=__DIR__.'/storage';
  $csvPath=$storage.'/registration-records.csv'; $jsonPath=$storage.'/registration-records.json';
  try {
    $fp=fopen($csvPath,'ab'); if ($fp) { if (filesize($csvPath)===0) fputcsv($fp,array_keys($record)); if (flock($fp,LOCK_EX)) { fputcsv($fp,array_values($record)); flock($fp,LOCK_UN); } fclose($fp); }
    $records=[]; if (is_file($jsonPath)) { $existing=json_decode((string)file_get_contents($jsonPath),true); if (is_array($existing)) $records=$existing; }
    $records[]=$record; file_put_contents($jsonPath,json_encode($records,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),LOCK_EX);
  } catch(Throwable $ex) { error_log('Registration file log failed: '.$ex->getMessage()); }
  flash('success','Your account has been created. You can now sign in.'); redirect('login.php');
}
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><p class="eyebrow">Join the community</p><h1>Create your student account.</h1><p class="lead">Complete the form below. Fields are validated in your browser and revalidated securely on the server.</p></div></section>
<div class="auth-shell"><aside class="auth-aside"><p class="eyebrow">StudentHub account</p><h2>Make campus life easier.</h2><p>Use your account to access student features, find events and keep your profile details together.</p><ul><li>Student portal access</li><li>Event registration</li><li>Accessible, responsive forms</li><li>Secure password storage</li></ul></aside><section class="auth-main"><h2>Student registration</h2><?php if(isset($errors['general'])): ?><div class="notice error" role="alert"><?=e($errors['general'])?></div><?php endif; ?><form id="registrationForm" method="post" novalidate><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><div class="form-grid"><div class="field"><label for="full_name">Full name *</label><input id="full_name" name="full_name" required maxlength="80" autocomplete="name" value="<?=e($values['full_name'])?>"><small class="field-error" data-error-for="full_name"><?=e($errors['full_name']??'')?></small></div><div class="field"><label for="username">Username *</label><input id="username" name="username" required minlength="4" maxlength="24" autocomplete="username" value="<?=e($values['username'])?>"><small class="field-error" data-error-for="username"><?=e($errors['username']??'')?></small></div><div class="field"><label for="email">Email *</label><input id="email" name="email" type="email" required maxlength="190" autocomplete="email" value="<?=e($values['email'])?>"><small class="field-error" data-error-for="email"><?=e($errors['email']??'')?></small></div><div class="field"><label for="mobile">Mobile number *</label><input id="mobile" name="mobile" type="tel" inputmode="numeric" required maxlength="10" autocomplete="tel-national" value="<?=e($values['mobile'])?>"><small class="field-error" data-error-for="mobile"><?=e($errors['mobile']??'')?></small></div><div class="field"><label for="password">Password *</label><input id="password" name="password" type="password" required minlength="8" maxlength="64" autocomplete="new-password"><small class="field-error" data-error-for="password"><?=e($errors['password']??'Use uppercase, lowercase, number and symbol.')?></small></div><div class="field"><label for="confirm_password">Confirm password *</label><input id="confirm_password" name="confirm_password" type="password" required autocomplete="new-password"><small class="field-error" data-error-for="confirm_password"><?=e($errors['confirm_password']??'')?></small></div><div class="field"><label for="course">Course *</label><select id="course" name="course" required><option value="">Select a course</option><?php foreach(['Computer Engineering','Information Technology','Computer Science','Design','Business'] as $course): ?><option value="<?=e($course)?>" <?= $values['course']===$course?'selected':'' ?>><?=e($course)?></option><?php endforeach; ?></select><small class="field-error" data-error-for="course"><?=e($errors['course']??'')?></small></div><div class="field"><label for="year">Current year *</label><select id="year" name="year" required><option value="">Select year</option><?php for($i=1;$i<=4;$i++): ?><option value="<?=$i?>" <?= $values['year']===(string)$i?'selected':'' ?>>Year <?=$i?></option><?php endfor; ?></select><small class="field-error" data-error-for="year"><?=e($errors['year']??'')?></small></div><fieldset class="field field-full" style="border:0;padding:0;margin-inline:0"><legend>Gender *</legend><div class="radio-row"><?php foreach(['Female','Male','Non-binary','Prefer not to say'] as $gender): ?><label><input type="radio" name="gender" value="<?=e($gender)?>" <?= $values['gender']===$gender?'checked':'' ?>> <?=e($gender)?></label><?php endforeach; ?></div><small class="field-error" data-error-for="gender"><?=e($errors['gender']??'')?></small></fieldset><div class="field field-full"><label class="checkbox-row"><input id="terms" name="terms" type="checkbox" value="1" required><span>I accept the terms and confirm that the information above is accurate.</span></label><small class="field-error" data-error-for="terms"><?=e($errors['terms']??'')?></small></div></div><button class="button" type="submit">Create account</button><p class="help-text">Already registered? <a href="<?=e(app_url('login.php'))?>">Sign in</a>.</p></form></section></div>
<?php require __DIR__ . '/includes/footer.php'; ?>