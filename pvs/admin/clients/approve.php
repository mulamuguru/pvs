<?php
require_once '../../db.php';
session_start();

// Ensure only admins can access this
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

$clientId = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if ($clientId && in_array($status, ['approved', 'pending'])) {
    $stmt = $conn->prepare("UPDATE clients SET status = :status WHERE id = :id");
    $stmt->execute([
        ':status' => $status,
        ':id' => $clientId
    ]);

    $_SESSION['flash'] = "Client has been {$status}.";
} else {
    $_SESSION['flash'] = "Invalid request.";
}

// Redirect back to approvals page
header("Location: approvals.php");
exit;
