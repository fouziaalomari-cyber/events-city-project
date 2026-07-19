<?php
require_once __DIR__ . '/../db.php';

$basePath = '../';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        ensureDatabase();
        $conn = connectDb();
        $stmt = $conn->prepare('DELETE FROM events WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="container py-5">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h1 class="h4 mb-3">حذف الفعالية</h1>
            <p>هل أنت متأكد من رغبتك في حذف هذه الفعالية؟</p>
            <form method="post">
                <input type="hidden" name="id" value="<?= (int) $id ?>">
                <button class="btn btn-danger">نعم، حذف</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">إلغاء</a>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
