<?php
$basePath = '';
$activePage = 'contact';
require_once __DIR__ . '/db.php';
ensureDatabase();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $messageText = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $messageText === '') {
        $message = 'يرجى تعبئة جميع الحقول المطلوبة.';
        $messageType = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'يرجى إدخال بريد إلكتروني صحيح.';
        $messageType = 'danger';
    } else {
        $message = 'تم إرسال الرسالة بنجاح. سنعاود التواصل معك قريباً.';
        $messageType = 'success';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="container py-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <h1 class="h3 fw-bold mb-3">تواصل معنا</h1>
            <p class="text-muted">إذا كان لديك سؤال أو اقتراح، أرسل لنا رسالة وسنرد عليك في أقرب وقت.</p>

            <?php if ($message !== ''): ?>
                <div class="alert alert-<?= htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8') ?>" role="alert">
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form id="contactForm" method="post" novalidate>
                <div class="mb-3">
                    <label for="name" class="form-label">الاسم</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <div class="invalid-feedback">يرجى إدخال الاسم.</div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="invalid-feedback">يرجى إدخال بريد إلكتروني صحيح.</div>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">الرسالة</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    <div class="invalid-feedback">يرجى كتابة الرسالة.</div>
                </div>
                <button type="submit" class="btn btn-primary">إرسال</button>
            </form>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h5">معلومات التواصل</h2>
                    <ul class="list-unstyled mt-3">
                        <li><strong>البريد العام:</strong> events@university.edu</li>
                        <li><strong>فيسبوك:</strong> /UniversityEvents</li>
                        <li><strong>إنستغرام:</strong> @university.events</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
