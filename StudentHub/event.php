<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Events';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="eyebrow">What's happening</p>
        <h1>Find your next campus moment.</h1>
        <p class="lead">Browse technical events, career sessions and cultural activities. Search, filter, sort and page through the calendar.</p>
        
        <?php if (is_admin()): ?>
            <div style="margin-top: 1.5rem;">
                <a href="<?= e(app_url('admin/events.php')) ?>" class="button" style="background: #3b82f6;">Manage Events & Upload Posters (P12)</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="toolbar" aria-label="Event search and filters">
            <label class="field" style="flex:2;margin:0">
                <span>Search events</span>
                <input type="search" id="searchInput" placeholder="Try coding, career, culture…" autocomplete="off">
            </label>
            <label class="field" style="margin:0">
                <span>Category</span>
                <select id="filterType">
                    <option value="all">All categories</option>
                    <option value="Technical">Technical</option>
                    <option value="Career">Career</option>
                    <option value="Cultural">Cultural</option>
                </select>
            </label>
            <label class="field" style="margin:0">
                <span>Sort by</span>
                <select id="sortEvents">
                    <option value="dateAsc">Date: earliest first</option>
                    <option value="dateDesc">Date: latest first</option>
                    <option value="titleAsc">Title: A–Z</option>
                    <option value="titleDesc">Title: Z–A</option>
                </select>
            </label>
        </div>
        <p id="eventStatus" class="muted" role="status" aria-live="polite">Loading events…</p>
        <div id="eventContainer" class="event-grid"></div>
        <div id="pagination" class="pagination" aria-label="Event pages"></div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-heading">
            <p class="eyebrow">Stay in the loop</p>
            <h2>Notices and updates.</h2>
            <p>Notices are loaded from an external JSON file using the Fetch API.</p>
        </div>
        <div id="noticeContainer" class="grid-3">
            <p class="muted">Loading notices…</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="panel">
            <h2>Ready to take part?</h2>
            <p>Sign in to register your interest in events. Event registration is available to student accounts.</p>
            <?php if (is_logged_in() && !is_admin()): ?>
                <form method="post" action="<?= e(app_url('student/register-event.php')) ?>">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <label class="field">
                        <span>Event</span>
                        <select name="event_id" id="registerEventSelect" required>
                            <option value="">Choose an event</option>
                        </select>
                    </label>
                    <button class="button" type="submit">Register for event</button>
                </form>
            <?php elseif (!is_logged_in()): ?>
                <a class="button" href="<?= e(app_url('login.php')) ?>">Sign in to register</a>
            <?php else: ?>
                <p class="muted">Administrators manage event details and posters in the <a href="<?= e(app_url('admin/events.php')) ?>" style="color: #3b82f6; text-decoration: underline;">admin dashboard</a>.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<script src="<?= e(app_url('assets/js/events.js')) ?>" defer></script>
<?php require __DIR__ . '/includes/footer.php'; ?>