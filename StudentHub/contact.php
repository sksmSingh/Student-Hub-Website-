<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle='Contact'; $errors=[]; $success='';
$name=''; $email=''; $mobile=''; $subject=''; $message='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  verify_csrf();
  $name=trim((string)($_POST['name']??'')); $email=trim((string)($_POST['email']??'')); $mobile=trim((string)($_POST['mobile']??'')); $subject=trim((string)($_POST['subject']??'')); $message=trim((string)($_POST['message']??''));
  if (!preg_match("/^[\\p{L}][\\p{L} .'-]{1,79}$/u",$name)) $errors['name']='Enter a valid name.';
  if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors['email']='Enter a valid email address.';
  if ($mobile!=='' && !preg_match('/^[6-9][0-9]{9}$/',$mobile)) $errors['mobile']='Enter a valid 10-digit Indian mobile number.';
  if (strlen($subject)<3 || strlen($subject)>120) $errors['subject']='Subject must be 3–120 characters.';
  if (strlen($message)<10 || strlen($message)>3000) $errors['message']='Message must be 10–3000 characters.';
  if (!$errors) {
    $record=['submitted_at'=>date(DATE_ATOM),'name'=>$name,'email'=>$email,'mobile'=>$mobile,'subject'=>$subject,'message'=>$message];
    $csvPath=__DIR__.'/storage/contact-submissions.csv'; $jsonPath=__DIR__.'/storage/contact-submissions.json';
    try {
      $fp=fopen($csvPath,'ab'); if (!$fp) throw new RuntimeException('CSV unavailable');
      if (filesize($csvPath)===0) fputcsv($fp,array_keys($record));
      if (!flock($fp,LOCK_EX)) throw new RuntimeException('Could not lock CSV');
      fputcsv($fp,array_values($record)); flock($fp,LOCK_UN); fclose($fp);
      $items=[]; if (is_file($jsonPath)) { $decoded=json_decode((string)file_get_contents($jsonPath),true); if (is_array($decoded)) $items=$decoded; }
      $items[]=$record; if (file_put_contents($jsonPath,json_encode($items,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),LOCK_EX)===false) throw new RuntimeException('JSON unavailable');
      $success='Thanks, your message has been received. Our team will get back to you.'; $name=$email=$mobile=$subject=$message='';
    } catch (Throwable $ex) { error_log('Contact form storage error: '.$ex->getMessage()); $errors['general']='We could not save your message right now. Please try again later.'; }
  }
}
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero"><div class="container"><p class="eyebrow">Get in touch</p><h1>We’re here to help.</h1><p class="lead">Questions about courses, admissions, events or student services? Send a message to the campus team.</p></div></section>
<section class="section"><div class="container form-layout"><section class="panel"><h2>Send a message</h2><?php if($success): ?><div class="notice success" role="status"><?=e($success)?></div><?php endif; ?><?php if(isset($errors['general'])): ?><div class="notice error" role="alert"><?=e($errors['general'])?></div><?php endif; ?><p id="contactClientError" class="field-error" role="alert"></p><form id="contactForm" method="post" novalidate><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><div class="form-grid"><div class="field"><label for="name">Full name</label><input id="name" name="name" value="<?=e($name)?>" required maxlength="80" autocomplete="name"><small class="field-error"><?=e($errors['name']??'')?></small></div><div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" value="<?=e($email)?>" required maxlength="190" autocomplete="email"><small class="field-error"><?=e($errors['email']??'')?></small></div><div class="field"><label for="mobile">Mobile (optional)</label><input id="mobile" name="mobile" type="tel" inputmode="numeric" pattern="[6-9][0-9]{9}" value="<?=e($mobile)?>" maxlength="10"><small class="field-error"><?=e($errors['mobile']??'')?></small></div><div class="field"><label for="subject">Subject</label><input id="subject" name="subject" value="<?=e($subject)?>" required minlength="3" maxlength="120"><small class="field-error"><?=e($errors['subject']??'')?></small></div><div class="field field-full"><label for="message">Message</label><textarea id="message" name="message" required minlength="10" maxlength="3000"><?=e($message)?></textarea><small class="field-error"><?=e($errors['message']??'')?></small></div></div><button class="button" type="submit">Send message</button></form></section><aside class="panel"><p class="eyebrow">Campus contact</p><h2>Random College</h2><p>123 Education Street<br>Gujarat, India</p><p><strong>Phone</strong><br>+91 98765 43210</p><p><strong>Email</strong><br>info@studenthub.example</p><p><strong>Office hours</strong><br>Monday–Friday, 9:00 AM–5:00 PM</p><div class="notice">This practical stores valid contact submissions in both CSV and JSON files in the local storage folder.</div></aside></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
