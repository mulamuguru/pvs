<?php
session_start();
require_once '../db.php'; // update path if needed

if (!isset($_SESSION['officer_id'])) {
    // Officer must be logged in
    header('Location: ../login.php');
    exit;
}

$officerId = $_SESSION['officer_id'];

if (isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);

    try {
        // Check if item is already in cart for this officer
        $stmt = $conn->prepare("SELECT id FROM cart WHERE customer_id = :officerId AND product_id = :productId");
        $stmt->execute([
            'officerId' => $officerId,
            'productId' => $productId
        ]);

        if ($stmt->rowCount() > 0) {
            // Already in cart - update quantity instead
            $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE customer_id = :officerId AND product_id = :productId")
                ->execute([
                    'officerId' => $officerId,
                    'productId' => $productId
                ]);
        } else {
            // Insert new item
            $stmt = $conn->prepare("INSERT INTO cart (customer_id, product_id, quantity, added_at) VALUES (:officerId, :productId, 1, NOW())");
            $stmt->execute([
                'officerId' => $officerId,
                'productId' => $productId
            ]);
        }

        // Redirect back or send success response
        header('Location: index.php');
        exit;

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

} else {
    echo "Invalid request.";
}
?>
