<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin(); // Restricts access to authorized/admin sessions

$conn = db();

// Fetch statistics
$studentCount = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$eventCount = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$userCount = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'] ?? 1;

// Fetch latest registrations
$latestStudents = $conn->query("SELECT * FROM students ORDER BY id DESC LIMIT 5");

$pageTitle = 'Admin Dashboard';
require __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Control Panel</p>
        <h1>Admin Dashboard</h1>
        <p class="lead">Overview of platform metrics, live counts, and recent user registrations.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Statistics Grid -->
        <div class="grid-3" style="margin-bottom: 2.5rem;">
            <div class="card" style="padding: 1.5rem; text-align: center;">
                <h3>Total Students</h3>
                <p style="font-size: 2.5rem; font-weight: bold; color: #3b82f6; margin: 0.5rem 0;"><?= (int)$studentCount ?></p>
                <a href="students.php" style="font-size: 0.9rem; color: #3b82f6;">Manage Students →</a>
            </div>
            <div class="card" style="padding: 1.5rem; text-align: center;">
                <h3>Total Events</h3>
                <p style="font-size: 2.5rem; font-weight: bold; color: #10b981; margin: 0.5rem 0;"><?= (int)$eventCount ?></p>
                <a href="events.php" style="font-size: 0.9rem; color: #10b981;">Manage Events →</a>
            </div>
            <div class="card" style="padding: 1.5rem; text-align: center;">
                <h3>Registered Users</h3>
                <p style="font-size: 2.5rem; font-weight: bold; color: #8b5cf6; margin: 0.5rem 0;"><?= (int)$userCount ?></p>
                <span style="font-size: 0.9rem; color: #6b7280;">Active Accounts</span>
            </div>
        </div>

        <!-- Latest Registrations Table -->
        <h2>Latest Student Registrations</h2>
        <div class="card" style="padding: 1.5rem; margin-top: 1rem; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.1);">
                        <th style="padding: 0.75rem;">Name</th>
                        <th style="padding: 0.75rem;">Email</th>
                        <th style="padding: 0.75rem;">Course</th>
                        <th style="padding: 0.75rem;">Year</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $latestStudents->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 0.75rem;"><?= e($student['full_name']) ?></td>
                            <td style="padding: 0.75rem;"><?= e($student['email']) ?></td>
                            <td style="padding: 0.75rem;"><?= e($student['course']) ?></td>
                            <td style="padding: 0.75rem;">Year <?= e($student['year_of_study']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>