<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$client_id = $_GET['id'];

// Optionally: check if client exists before deletion
$stmt = $conn->prepare("DELETE FROM clients WHERE id = :id");
$stmt->execute([':id' => $client_id]);

header("Location: index.php?msg=Client deleted successfully");
exit;
?>
