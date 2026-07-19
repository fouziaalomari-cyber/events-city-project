<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل فعاليات الجامعة الافتراضية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>assets/css/styles.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>index.php">دليل فعاليات الجامعة</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>index.php">الرئيسية</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'events' ? 'active' : '' ?>" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>events.php">الفعاليات</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'about' ? 'active' : '' ?>" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>about.php">عن الدليل</a></li>
                <li class="nav-item"><a class="nav-link <?= $activePage === 'contact' ? 'active' : '' ?>" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>contact.php">اتصل بنا</a></li>
                <?php if (empty($_SESSION['admin_logged_in'])): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>login.php">تسجيل الدخول</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>admin/dashboard.php">لوحة التحكم</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>admin/logout.php">تسجيل الخروج</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
