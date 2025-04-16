<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized.");
}

$group_id = $_POST['group_id'];
$branch_id = $_POST['branch_id'];
$officer_id = $_POST['officer_id'];

$stmt = $conn->prepare("UPDATE groups SET branch_id = ?, officer_id = ? WHERE id = ?");
$stmt->execute([$branch_id, $officer_id, $group_id]);

header("Location: index.php");
exit;
