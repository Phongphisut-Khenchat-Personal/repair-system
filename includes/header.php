<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'ระบบแจ้งซ่อมทั่วไป';
$currentNav = $currentNav ?? '';
$user = current_user();

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/app.css" rel="stylesheet">
</head>
<body>
    <header class="site-header">
        <div class="wrap">
            <a class="logo" href="index.php">แจ้งซ่อม</a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="siteNav" aria-label="เปิดเมนู">
                <span></span>
            </button>
            <nav class="site-nav" id="siteNav">
                <a class="<?= $currentNav === 'list' ? 'is-active' : '' ?>" href="index.php">รายการ</a>
                <a class="d-md-none <?= $currentNav === 'create' ? 'is-active' : '' ?>" href="create.php">เพิ่มรายการ</a>
                <span class="user-name"><?= h((string) ($user['display_name'] ?? '')) ?></span>
                <a href="logout.php">ออกจากระบบ</a>
            </nav>
        </div>
    </header>
    <main class="page">
        <div class="wrap">
