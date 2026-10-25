<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login();

$conn = db();
$message = '';
$error = '';

// 1. Handle Delete Operation
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        $message = "Student record deleted successfully.";
    } else {
        $error = "Failed to delete student record.";
    }
}

// 2. Handle Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_student') {
    verify_csrf();
    $editId = (int)$_POST['id'];
    $fullName = trim($_POST['full_name']);
    $mobile = trim($_POST['mobile']);
    $course = trim($_POST['course']);
    $year = (int)$_POST['year_of_study'];

    $stmt = $conn->prepare("UPDATE students SET full_name = ?, mobile = ?, course = ?, year_of_study = ? WHERE id = ?");
    $stmt->bind_param("sssii", $fullName, $mobile, $course, $year, $editId);
    if ($stmt->execute()) {
        $message = "Student record updated successfully.";
    } else {
        $error = "Failed to update record.";
    }
}

// 3. Check if editing a specific record
$editingStudent = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editingStudent = $stmt->get_result()->fetch_assoc();
}

// 4. Search & Filter logic
$search = $_GET['search'] ?? '';
$courseFilter = $_GET['course'] ?? '';

$query = "SELECT * FROM students WHERE (full_name LIKE ? OR email LIKE ?)";
if ($courseFilter !== '') {
    $query .= " AND course = ?";
}
$query .= " ORDER BY id DESC";

$stmt = $conn->prepare($query);
$searchTerm = "%$search%";
if ($courseFilter !== '') {
    $stmt->bind_param('sss', $searchTerm, $searchTerm, $courseFilter);
} else {
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
}
$stmt->execute();
$result = $stmt->get_result();

$pageTitle = 'Student Directory';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <p class="eyebrow">Student Records</p>
        <h1>Student Directory & Management</h1>
        <p class="lead">Search, filter, edit, and manage student profiles securely using MySQLi.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($message): ?>
            <div class="notice success" style="padding: 1rem; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 1.5rem;">
                <?= e($message) ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="notice error" style="padding: 1rem; background: #f8d7da; color: #721c24; border-radius: 6px; margin-bottom: 1.5rem;">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <!-- Edit Form Modal/Section (Appears when Edit is clicked) -->
        <?php if ($editingStudent): ?>
            <div class="card" style="padding: 1.5rem; margin-bottom: 2rem; border: 2px solid #3b82f6;">
                <h2>Edit Record: <?= e($editingStudent['full_name']) ?></h2>
                <form method="POST" action="directory.php">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="edit_student">
                    <input type="hidden" name="id" value="<?= e($editingStudent['id']) ?>">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label>Full Name</label>
                            <input type="text" name="full_name" class="field" value="<?= e($editingStudent['full_name']) ?>" required style="width: 100%; padding: 0.5rem;">
                        </div>
                        <div>
                            <label>Mobile</label>
                            <input type="text" name="mobile" class="field" value="<?= e($editingStudent['mobile']) ?>" required style="width: 100%; padding: 0.5rem;">
                        </div>
                        <div>
                            <label>Course</label>
                            <select name="course" class="field" style="width: 100%; padding: 0.5rem;">
                                <?php foreach(['Computer Engineering','Information Technology','Computer Science','Design','Business'] as $c): ?>
                                    <option value="<?= e($c) ?>" <?= $editingStudent['course'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label>Year of Study</label>
                            <select name="year_of_study" class="field" style="width: 100%; padding: 0.5rem;">
                                <?php for($i=1; $i<=4; $i++): ?>
                                    <option value="<?= $i ?>" <?= (int)$editingStudent['year_of_study'] === $i ? 'selected' : '' ?>>Year <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="button">Save Changes</button>
                    <a href="directory.php" class="button" style="background: #6c757d; margin-left: 0.5rem;">Cancel</a>
                </form>
            </div>
        <?php endif; ?>

        <!-- Search & Filter Form -->
        <form method="GET" style="margin-bottom: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" name="search" class="field" placeholder="Search by name or email..." value="<?= e($search) ?>" style="flex: 1; padding: 0.75rem;">
            <select name="course" class="field" style="padding: 0.75rem;">
                <option value="">All Courses</option>
                <?php foreach(['Computer Engineering','Information Technology','Computer Science','Design','Business'] as $c): ?>
                    <option value="<?= e($c) ?>" <?= $courseFilter === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="button" style="padding: 0.75rem 1.5rem;">Filter</button>
        </form>

        <!-- Student Cards Grid -->
        <div class="grid-3">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
                    <h3><?= e($row['full_name']) ?></h3>
                    <p><strong>Email:</strong> <?= e($row['email']) ?></p>
                    <p><strong>Mobile:</strong> <?= e($row['mobile']) ?></p>
                    <p><strong>Course:</strong> <?= e($row['course']) ?></p>
                    <p><strong>Year of Study:</strong> Year <?= e($row['year_of_study']) ?></p>

                    <!-- P11 CRUD Action Buttons -->
                    <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.1); display: flex; gap: 0.5rem;">
                        <a href="directory.php?edit=<?= $row['id'] ?>" class="button" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">Edit</a>
                        <a href="directory.php?delete=<?= $row['id'] ?>" class="button" onclick="return confirm('Are you sure you want to delete this record?');" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; background: #e63946;">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php if ($result->num_rows === 0): ?>
                <p>No student records found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>