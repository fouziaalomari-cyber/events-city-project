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
ensureDatabase();
$conn = connectDb();
if ($id > 0) {
    $stmt = $conn->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');

    if ($id <= 0 || $title === '' || $description === '' || $category === '' || $location === '' || $eventDate === '') {
        $message = 'يرجى تعبئة جميع الحقول.';
        $messageType = 'danger';
    } else {
        $imagePath = $_POST['existing_image'] ?? 'assets/img/placeholder.svg';
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadName = uniqid('event_', true) . '.' . $ext;
                $target = __DIR__ . '/../assets/img/uploads/' . $uploadName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $imagePath = 'assets/img/uploads/' . $uploadName;
                }
            }
        }

        $stmt = $conn->prepare('UPDATE events SET title = ?, description = ?, category = ?, location = ?, event_date = ?, image = ? WHERE id = ?');
        $stmt->bind_param('ssssssi', $title, $description, $category, $location, $eventDate, $imagePath, $id);
        $stmt->execute();
        $stmt->close();
        $message = 'تم تعديل الفعالية بنجاح.';
        $messageType = 'success';
    }
}

if (!isset($event)) {
    $event = null;
}

$conn->close();
require_once __DIR__ . '/../includes/header.php';
?>

<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">تعديل الفعالية</h1>
            <p class="text-muted mb-0">تعديل بيانات فعالية موجودة.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary">العودة إلى لوحة التحكم</a>
    </div>

    <?php if ($message !== ''): ?>
        <div class="alert alert-<?= htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if ($event): ?>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= (int) $event['id'] ?>">
                    <input type="hidden" name="existing_image" value="<?= htmlspecialchars($event['image'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">عنوان الفعالية</label>
                            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">التصنيف</label>
                            <input type="text" class="form-control" name="category" value="<?= htmlspecialchars($event['category'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">المكان</label>
                            <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">التاريخ</label>
                            <input type="date" class="form-control" name="event_date" value="<?= htmlspecialchars($event['event_date'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">الوصف</label>
                            <textarea class="form-control" name="description" rows="5" required><?= htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">صورة الفعالية</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                    </div>
                    <button class="btn btn-primary mt-4">حفظ التعديلات</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">لم يتم العثور على الفعالية المطلوبة.</div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
