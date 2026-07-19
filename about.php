<?php
$basePath = '';
$activePage = 'about';
require_once __DIR__ . '/db.php';
ensureDatabase();
require_once __DIR__ . '/includes/header.php';
?>

<main class="container py-5">
    <section class="row g-4 align-items-center">
        <div class="col-lg-7">
            <h1 class="h3 fw-bold mb-3">عن دليل فعاليات الجامعة الافتراضية</h1>
            <p class="lead text-muted">يهدف الدليل إلى تسهيل اكتشاف الفعاليات والأنشطة الجامعية وتوفير منصة موحدة لجميع الطلاب والموظفين.</p>
            <p>نحن نؤمن بأن المشاركة في الأنشطة الثقافية والرياضية والفنية تعزز من تجربة الطالب الجامعي وتفتح أبواب التعلم والتواصل المجتمعي.</p>
        </div>
        <div class="col-lg-5">
            <img src="assets/img/placeholder.svg" class="img-fluid rounded shadow-sm" alt="عن الدليل">
        </div>
    </section>

    <section class="mt-5">
        <h2 class="h4 mb-3">فريق العمل</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="h6 fw-bold">سارة أحمد</h3>
                        <p class="text-muted mb-0">منسقة المحتوى</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="h6 fw-bold">محمد سالم</h3>
                        <p class="text-muted mb-0">مدير المنصة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="h6 fw-bold">ليلى حسن</h3>
                        <p class="text-muted mb-0">مسؤولة التواصل</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-5">
        <h2 class="h4 mb-3">سياسات تقديم الفعاليات</h2>
        <ul class="list-group list-group-flush">
            <li class="list-group-item px-0">يجب أن تكون الفعالية ذات صلة بالأنشطة الجامعية أو المجتمعية.</li>
            <li class="list-group-item px-0">يتم مراجعة المعلومات قبل نشرها لضمان الدقة والوضوح.</li>
            <li class="list-group-item px-0">يمكن للمشرف تعديل أو حذف الفعالية في حال عدم الالتزام بالسياسات.</li>
        </ul>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
