<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';
require_login();

$pageTitle = 'รายการแจ้งซ่อม';
$currentNav = 'list';

$keyword = trim((string) ($_GET['q'] ?? ''));

if ($keyword !== '') {
    $stmt = $pdo->prepare(
        'SELECT id, repair_date, reporter, description
         FROM repairs
         WHERE reporter LIKE :keyword
            OR description LIKE :keyword
         ORDER BY repair_date DESC, id DESC'
    );
    $stmt->execute(['keyword' => '%' . $keyword . '%']);
    $repairs = $stmt->fetchAll();
} else {
    $stmt = $pdo->query(
        'SELECT id, repair_date, reporter, description
         FROM repairs
         ORDER BY repair_date DESC, id DESC'
    );
    $repairs = $stmt->fetchAll();
}

$resultCount = count($repairs);
$successMessage = $_SESSION['flash_success'] ?? '';
$errorMessage = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

require __DIR__ . '/includes/header.php';

?>
<div class="page-head">
    <div>
        <p class="page-kicker">
            <?= $keyword !== '' ? 'ผลการค้นหา ' . h((string) $resultCount) . ' รายการ' : h((string) $resultCount) . ' รายการ · ล่าสุดก่อน' ?>
        </p>
        <h1>รายการแจ้งซ่อม</h1>
    </div>
    <a href="create.php" class="btn btn-primary">เพิ่มรายการ</a>
</div>

<form method="get" class="search-form" role="search">
    <input
        type="search"
        class="form-control"
        name="q"
        value="<?= h($keyword) ?>"
        placeholder="ค้นหาชื่อผู้แจ้งหรือรายละเอียด"
        aria-label="ค้นหา"
    >
    <button type="submit" class="btn btn-quiet">ค้นหา</button>
    <?php if ($keyword !== ''): ?>
        <a href="index.php" class="btn-text">ล้าง</a>
    <?php endif; ?>
</form>

<?php if ($successMessage !== ''): ?>
    <div class="banner banner-success" role="status"><?= h($successMessage) ?></div>
<?php endif; ?>

<?php if ($errorMessage !== ''): ?>
    <div class="banner banner-error" role="alert"><?= h($errorMessage) ?></div>
<?php endif; ?>

<div class="list-panel">
    <?php if (!$repairs): ?>
        <div class="empty">
            <h2><?= $keyword !== '' ? 'ไม่พบรายการที่ค้นหา' : 'ยังไม่มีรายการแจ้งซ่อม' ?></h2>
            <p><?= $keyword !== '' ? 'ลองใช้คำค้นอื่น หรือล้างการค้นหา' : 'เริ่มจากรายการแรกได้เลย' ?></p>
            <?php if ($keyword !== ''): ?>
                <a href="index.php" class="btn btn-quiet">แสดงทั้งหมด</a>
            <?php else: ?>
                <a href="create.php" class="btn btn-primary">เพิ่มรายการ</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="desktop-table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">รหัส</th>
                        <th scope="col">วันเวลา</th>
                        <th scope="col">ผู้แจ้ง</th>
                        <th scope="col">รายละเอียด</th>
                        <th scope="col"><span class="visually-hidden">การจัดการ</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($repairs as $repair): ?>
                        <tr>
                            <td class="cell-id"><?= h((string) $repair['id']) ?></td>
                            <td class="cell-date"><?= h(date('d/m/Y H:i', strtotime((string) $repair['repair_date']))) ?></td>
                            <td class="cell-title"><?= h((string) $repair['reporter']) ?></td>
                            <td class="cell-copy"><?= h((string) $repair['description']) ?></td>
                            <td>
                                <div class="row-actions">
                                    <a href="edit.php?id=<?= (int) $repair['id'] ?>" class="btn-text">แก้ไข</a>
                                    <button
                                        type="button"
                                        class="btn-text is-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-delete-id="<?= (int) $repair['id'] ?>"
                                    >
                                        ลบ
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mobile-list">
            <?php foreach ($repairs as $repair): ?>
                <article class="ticket">
                    <div class="ticket-top">
                        <span><?= h((string) $repair['id']) ?></span>
                        <span><?= h(date('d/m/Y H:i', strtotime((string) $repair['repair_date']))) ?></span>
                    </div>
                    <h2><?= h((string) $repair['reporter']) ?></h2>
                    <p><?= h((string) $repair['description']) ?></p>
                    <div class="row-actions">
                        <a href="edit.php?id=<?= (int) $repair['id'] ?>" class="btn-text">แก้ไข</a>
                        <button
                            type="button"
                            class="btn-text is-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteModal"
                            data-delete-id="<?= (int) $repair['id'] ?>"
                        >
                            ลบ
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
