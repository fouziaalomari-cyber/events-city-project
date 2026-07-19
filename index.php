<?php
require_once __DIR__ . '/db.php';

$basePath = '';
$activePage = 'home';
$dbReady = ensureDatabase();
$events = $dbReady ? getEventsList(4) : [];
$categories = ['ثقافة', 'رياضة', 'موسيقى', 'عائلي', 'تقنية'];

require_once __DIR__ . '/includes/header.php';
?>

<main>
    <section class="hero-section py-5">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <h1 class="display-5 fw-bold mb-3">دليل فعاليات الجامعة الافتراضية</h1>
                    <p class="lead text-muted">اكتشف أحدث الفعاليات الثقافية والرياضية والفنية التي تنظمها الجامعة وتشارك فيها المجتمع الأكاديمي.</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="events.php" class="btn btn-primary btn-lg">استعرض الفعاليات</a>
                        <a href="about.php" class="btn btn-outline-primary btn-lg">تعرف على الدليل</a>
                    </div>

                    <div class="card border-0 shadow-sm mt-4" style="max-width: 420px; background: linear-gradient(135deg, #eef4ff 0%, #e0ecff 100%);">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                <span class="fw-semibold text-primary">الاسم:</span>
                                <span class="fw-bold">فوزية العمري</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-semibold text-primary">ID:</span>
                                <span class="fw-bold">284905</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h3 class="h5">فعاليات بارزة هذا الأسبوع</h3>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item px-0">ورشة ثقافية رقمية</li>
                                <li class="list-group-item px-0">مهرجان رياضي مفتوح</li>
                                <li class="list-group-item px-0">ليلة موسيقى عربية</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 mb-0">تصنيفات سريعة</h2>
        </div>
        <div class="row g-3">
            <?php foreach ($categories as $category): ?>
                <div class="col-sm-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <span class="badge bg-primary-subtle text-primary fs-6"><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></span>
                            <p class="mt-3 mb-0 text-muted">استكشف الفعاليات المتعلقة بهذا المجال.</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 mb-0">أحدث الفعاليات</h2>
            <a href="events.php" class="btn btn-outline-secondary btn-sm">عرض الكل</a>
        </div>
        <div class="row g-4">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="<?= htmlspecialchars($event['image'], ENT_QUOTES, 'UTF-8') ?>" class="card-img-top event-image" alt="<?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-success-subtle text-success"><?= htmlspecialchars($event['category'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="text-muted small"><?= htmlspecialchars($event['event_date'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <h3 class="h6 fw-bold"><?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="text-muted small mb-3"><?= htmlspecialchars(substr($event['description'], 0, 100), ENT_QUOTES, 'UTF-8') ?>...</p>
                                <a href="event.php?id=<?= (int) $event['id'] ?>" class="btn btn-outline-primary btn-sm">التفاصيل</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning">لا توجد فعاليات متاحة حالياً.</div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
