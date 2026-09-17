<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';
require_login();

$pageTitle = 'แก้ไขรายการแจ้งซ่อม';
$currentNav = 'list';

$id = parse_positive_int($_GET['id'] ?? null);

if ($id === null) {
    $_SESSION['flash_error'] = 'รหัสรายการไม่ถูกต้อง';
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT id, repair_date, reporter, description
     FROM repairs
     WHERE id = :id'
);
$stmt->execute(['id' => $id]);
$repair = $stmt->fetch();

if (!$repair) {
    $_SESSION['flash_error'] = 'ไม่พบรายการที่ต้องการแก้ไข';
    header('Location: index.php');
    exit;
}

$repairDate = date('Y-m-d\TH:i', strtotime((string) $repair['repair_date']));
$reporter = (string) $repair['reporter'];
$description = (string) $repair['description'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $repairDate = trim((string) ($_POST['repair_date'] ?? ''));
    $reporter = trim((string) ($_POST['reporter'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $errors = validate_repair($repairDate, $reporter, $description);

    if ($errors === []) {
        $updateStmt = $pdo->prepare(
            'UPDATE repairs
             SET repair_date = :repair_date,
                 reporter = :reporter,
                 description = :description
             WHERE id = :id'
        );
        $updateStmt->execute([
            'repair_date' => to_mysql_datetime($repairDate),
            'reporter' => $reporter,
            'description' => $description,
            'id' => $id,
        ]);

        $_SESSION['flash_success'] = 'แก้ไขข้อมูลแจ้งซ่อมเรียบร้อยแล้ว';
        header('Location: index.php');
        exit;
    }
}

require __DIR__ . '/includes/header.php';

?>
<a class="back-link" href="index.php">รายการแจ้งซ่อม</a>

<div class="page-head">
    <div>
        <p class="page-kicker">รหัส <?= h((string) $id) ?></p>
        <h1>แก้ไขรายการ</h1>
    </div>
</div>

<div class="form-panel">
    <form method="post" novalidate>
        <div class="form-grid">
            <div>
                <label for="repair_date" class="form-label">วันที่และเวลา</label>
                <input
                    type="datetime-local"
                    class="form-control<?= isset($errors['repair_date']) ? ' is-invalid' : '' ?>"
                    id="repair_date"
                    name="repair_date"
                    value="<?= h($repairDate) ?>"
                >
                <?php if (isset($errors['repair_date'])): ?>
                    <div class="invalid-feedback"><?= h($errors['repair_date']) ?></div>
                <?php endif; ?>
            </div>
            <div>
                <label for="reporter" class="form-label">ชื่อผู้แจ้ง</label>
                <input
                    type="text"
                    class="form-control<?= isset($errors['reporter']) ? ' is-invalid' : '' ?>"
                    id="reporter"
                    name="reporter"
                    maxlength="150"
                    value="<?= h($reporter) ?>"
                >
                <?php if (isset($errors['reporter'])): ?>
                    <div class="invalid-feedback"><?= h($errors['reporter']) ?></div>
                <?php endif; ?>
            </div>
            <div class="full">
                <label for="description" class="form-label">รายละเอียดงาน</label>
                <textarea
                    class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>"
                    id="description"
                    name="description"
                    rows="6"
                ><?= h($description) ?></textarea>
                <?php if (isset($errors['description'])): ?>
                    <div class="invalid-feedback"><?= h($errors['description']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <a class="btn-text" href="index.php">ยกเลิก</a>
            <button type="submit" class="btn btn-primary">บันทึก</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
