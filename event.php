<?php
require_once __DIR__ . '/db.php';

$basePath = '';
$dbReady = ensureDatabase();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$event = $dbReady && $id > 0 ? getEventById($id) : null;
$relatedEvents = [];

if ($event && $dbReady) {
    $conn = connectDb();
    $stmt = $conn->prepare('SELECT * FROM events WHERE category = ? AND id != ? ORDER BY event_date DESC LIMIT 3');
    $stmt->bind_param('si', $event['category'], $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $relatedEvents[] = $row;
    }
    $stmt->close();
    $conn->close();
}

require_once __DIR__ . '/includes/header.php';
?>

<main class="container py-5">
    <?php if ($event): ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <img src="<?= htmlspecialchars($event['image'], ENT_QUOTES, 'UTF-8') ?>" class="img-fluid rounded shadow-sm w-100 event-image" alt="<?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="mt-4">
                    <h1 class="h3 fw-bold mb-3"><?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?></h1>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-primary"><?= htmlspecialchars($event['category'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="badge bg-secondary"><?= htmlspecialchars($event['event_date'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <p class="text-muted mb-3"><strong>المكان:</strong> <?= htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="lead"><?= nl2br(htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8')) ?></p>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=<?= urlencode($event['title']) ?>&dates=<?= date('Ymd', strtotime($event['event_date'])) ?><?= date('Ymd', strtotime($event['event_date'])) ?>&details=<?= urlencode($event['description']) ?>&location=<?= urlencode($event['location']) ?>" target="_blank" class="btn btn-primary">أضف للتقويم</a>
                    <button type="button" class="btn btn-outline-secondary" id="shareEvent">شارك</button>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h2 class="h5 mb-3">فعاليات ذات صلة</h2>
                        <?php if (!empty($relatedEvents)): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($relatedEvents as $related): ?>
                                    <li class="list-group-item px-0">
                                        <a href="event.php?id=<?= (int) $related['id'] ?>" class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($related['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted mb-0">لا توجد فعاليات مشابهة حالياً.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">لم يتم العثور على هذه الفعالية.</div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
