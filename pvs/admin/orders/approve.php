<?php
require_once '../../db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login.php");
    exit;
}

// Get the order ID from the query string
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Begin a transaction
    $conn->beginTransaction();

    try {
        // Update the order's status to "Approved"
        $stmt = $conn->prepare("UPDATE orders SET status = 'Approved' WHERE id = :id");
        $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
        $stmt->execute();

        // Now, delete the order from the database after updating the status
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = :id");
        $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Redirect to the orders list with a success message
        header("Location: index.php?message=Order Approved and Deleted Successfully");
        exit;

    } catch (Exception $e) {
        // Rollback if there's an error
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    echo "Invalid order ID.";
    exit;
}
?>
