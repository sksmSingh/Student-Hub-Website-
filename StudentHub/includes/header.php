<?php
$pageTitle = $pageTitle ?? 'StudentHub';
$user = current_user();
$flashes = get_flashes();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="StudentHub college portal for students, events, notices and campus information.">
  <title><?= e($pageTitle) ?> · StudentHub</title>
  <link rel="stylesheet" href="<?= e(app_url('style.css')) ?>">
  <script>window.STUDENTHUB_BASE = <?= json_encode(rtrim(APP_BASE, '/'), JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>;</script>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>
<div class="announcement" id="announcement" role="status" aria-live="polite">
  <span><strong>Campus update:</strong> Registration for upcoming student activities is open.</span>
  <button class="icon-button" type="button" id="dismissAnnouncement" aria-label="Dismiss announcement">×</button>
</div>
<header class="site-header">
  <a class="brand" href="<?= e(app_url('index.php')) ?>" aria-label="StudentHub home"><span class="brand-mark">S</span><span>Student<span class="brand-accent">Hub</span><small>Learn · Create · Innovate</small></span></a>
  <button class="menu-toggle" id="menuToggle" aria-expanded="false" aria-controls="primaryNav" aria-label="Open navigation menu"><span></span><span></span><span></span></button>
  <nav class="primary-nav" id="primaryNav" aria-label="Primary navigation">
    <a href="<?= e(app_url('index.php')) ?>">Home</a>
    <a href="<?= e(app_url('course.php')) ?>">Courses</a>
    <a href="<?= e(app_url('event.php')) ?>">Events</a>
    <a href="<?= e(app_url('placements.php')) ?>">Placements</a>
    <a href="<?= e(app_url('contact.php')) ?>">Contact</a>
    <a href="<?=e(app_url('directory.php'))?>">Student Directory</a>
    
    <?php if ($user): ?>
      <?php if (is_admin()): ?>
        <a href="<?= e(app_url('admin/dashboard.php')) ?>">Admin Dashboard</a>
      <?php else: ?>
        <a href="<?= e(app_url('student/dashboard.php')) ?>">My portal</a>
      <?php endif; ?>
      <form class="nav-logout" method="post" action="<?= e(app_url('logout.php')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <button class="nav-link-button" type="submit">Log out</button>
      </form>
    <?php else: ?>
      <a href="<?= e(app_url('login.php')) ?>">Log in</a>
      <a class="nav-cta" href="<?= e(app_url('register.php')) ?>">Register</a>
    <?php endif; ?>
    
    <button class="theme-toggle" type="button" id="themeToggle" aria-label="Switch colour theme" title="Switch colour theme">◐</button>
  </nav>
</header>
<?php foreach ($flashes as $flashMessage): ?>
  <div class="flash flash-<?= e($flashMessage['type']) ?>" role="status"><?= e($flashMessage['message']) ?></div>
<?php endforeach; ?>
<main id="main-content">