<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('ไม่อนุญาตให้ลบข้อมูลด้วยวิธีนี้');
}

$id = parse_positive_int($_POST['id'] ?? null);

if ($id === null) {
    $_SESSION['flash_error'] = 'รหัสรายการไม่ถูกต้อง';
    header('Location: index.php');
    exit;
}

$checkStmt = $pdo->prepare('SELECT id FROM repairs WHERE id = :id');
$checkStmt->execute(['id' => $id]);

if (!$checkStmt->fetch()) {
    $_SESSION['flash_error'] = 'ไม่พบรายการที่ต้องการลบ';
    header('Location: index.php');
    exit;
}

$deleteStmt = $pdo->prepare('DELETE FROM repairs WHERE id = :id');
$deleteStmt->execute(['id' => $id]);

$_SESSION['flash_success'] = 'ลบข้อมูลแจ้งซ่อมเรียบร้อยแล้ว';
header('Location: index.php');
exit;
