<?php

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/config/database.php';

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (current_user() === null) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'index.php';
        header('Location: login.php');
        exit;
    }
}

function parse_positive_int(mixed $value): ?int
{
    $filtered = filter_var($value, FILTER_VALIDATE_INT);

    if ($filtered === false || $filtered <= 0) {
        return null;
    }

    return $filtered;
}

function to_mysql_datetime(string $datetimeLocal): string
{
    $normalized = str_replace('T', ' ', $datetimeLocal);
    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $normalized) === 1) {
        $normalized .= ':00';
    }

    return $normalized;
}

function validate_repair(string $repairDate, string $reporter, string $description): array
{
    $errors = [];

    if ($repairDate === '') {
        $errors['repair_date'] = 'กรุณาระบุวันที่และเวลาแจ้งซ่อม';
    } else {
        $date = DateTime::createFromFormat('Y-m-d\TH:i', $repairDate);
        $isValid = $date instanceof DateTime && $date->format('Y-m-d\TH:i') === $repairDate;

        if (!$isValid) {
            $date = DateTime::createFromFormat('Y-m-d\TH:i:s', $repairDate);
            $isValid = $date instanceof DateTime && $date->format('Y-m-d\TH:i:s') === $repairDate;
        }

        if (!$isValid) {
            $errors['repair_date'] = 'รูปแบบวันที่และเวลาไม่ถูกต้อง';
        }
    }

    if ($reporter === '') {
        $errors['reporter'] = 'กรุณาระบุชื่อผู้แจ้ง';
    } elseif (mb_strlen($reporter) > 150) {
        $errors['reporter'] = 'ชื่อผู้แจ้งต้องไม่เกิน 150 ตัวอักษร';
    }

    if ($description === '') {
        $errors['description'] = 'กรุณาระบุรายละเอียดงานที่ให้ทำ';
    }

    return $errors;
}
