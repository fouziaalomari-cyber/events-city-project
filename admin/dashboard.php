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

ensureDatabase();
$conn = connectDb();
$result = $conn->query('SELECT * FROM events ORDER BY event_date DESC');
$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}
$conn->close();

require_once __DIR__ . '/../includes/header.php';
?>

<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">لوحة التحكم</h1>
            <p class="text-muted mb-0">أدخل، عدّل أو احذف فعاليات الدليل.</p>
        </div>
        <a href="add_event.php" class="btn btn-primary">إضافة فعالية جديدة</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>العنوان</th>
                            <th>التصنيف</th>
                            <th>المكان</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($event['category'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($event['event_date'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <a href="edit_event.php?id=<?= (int) $event['id'] ?>" class="btn btn-outline-primary btn-sm me-2">تعديل</a>
                                    <a href="delete_event.php?id=<?= (int) $event['id'] ?>" class="btn btn-outline-danger btn-sm">حذف</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
