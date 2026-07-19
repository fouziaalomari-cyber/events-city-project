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

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ensureDatabase();
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');

    if ($title === '' || $description === '' || $category === '' || $location === '' || $eventDate === '') {
        $message = 'يرجى تعبئة جميع الحقول.';
        $messageType = 'danger';
    } else {
        $imagePath = 'assets/img/placeholder.svg';
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

        $conn = connectDb();
        $stmt = $conn->prepare('INSERT INTO events (title, description, category, location, event_date, image) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $title, $description, $category, $location, $eventDate, $imagePath);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        $message = 'تمت إضافة الفعالية بنجاح.';
        $messageType = 'success';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">إضافة فعالية جديدة</h1>
            <p class="text-muted mb-0">أدخل بيانات الفعالية في النموذج أدناه.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary">العودة إلى لوحة التحكم</a>
    </div>

    <?php if ($message !== ''): ?>
        <div class="alert alert-<?= htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">عنوان الفعالية</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">التصنيف</label>
                        <input type="text" class="form-control" name="category" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">المكان</label>
                        <input type="text" class="form-control" name="location" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">التاريخ</label>
                        <input type="date" class="form-control" name="event_date" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">الوصف</label>
                        <textarea class="form-control" name="description" rows="5" required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">صورة الفعالية</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                </div>
                <button class="btn btn-primary mt-4">حفظ الفعالية</button>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
