<?php
require_once __DIR__ . '/db.php';

$basePath = '';
$activePage = 'events';
$dbReady = ensureDatabase();
$events = $dbReady ? getEventsList() : [];

require_once __DIR__ . '/includes/header.php';
?>

<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">جميع الفعاليات</h1>
            <p class="text-muted mb-0">ابحث أو Filter حسب التاريخ أو التصنيف.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" id="eventSearch" class="form-control" placeholder="ابحث عن فعالية...">
                </div>
                <div class="col-md-6">
                    <select id="eventCategory" class="form-select">
                        <option value="">كل التصنيفات</option>
                        <option value="ثقافة">ثقافة</option>
                        <option value="رياضة">رياضة</option>
                        <option value="موسيقى">موسيقى</option>
                        <option value="عائلي">عائلي</option>
                        <option value="تقنية">تقنية</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4" id="eventsContainer">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <div class="col-md-6 col-lg-4 event-card" data-title="<?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>" data-category="<?= htmlspecialchars($event['category'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?= htmlspecialchars($event['image'], ENT_QUOTES, 'UTF-8') ?>" class="card-img-top event-image" alt="<?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-info-subtle text-info"><?= htmlspecialchars($event['category'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="text-muted small"><?= htmlspecialchars($event['event_date'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <h2 class="h6 fw-bold"><?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                            <p class="text-muted small mb-3"><?= htmlspecialchars(substr($event['description'], 0, 120), ENT_QUOTES, 'UTF-8') ?>...</p>
                            <div class="text-muted small mb-3"><strong>المكان:</strong> <?= htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8') ?></div>
                            <a href="event.php?id=<?= (int) $event['id'] ?>" class="btn btn-primary btn-sm">التفاصيل</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning">لا توجد فعاليات حالياً.</div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
