<?php
require_once '../../db.php';

if (!isset($_GET['category'])) {
    echo "Category not specified.";
    exit;
}

$category_name = $_GET['category'];

// Fetch all subcategories under this category to delete them
$sub_stmt = $conn->prepare("SELECT id FROM categories WHERE category_name = ?");
$sub_stmt->execute([$category_name]);
$subcategories = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$subcategories) {
    echo "Category not found.";
    exit;
}

// Start a transaction for safer deletion
try {
    $conn->beginTransaction();

    // Delete all subcategories for this category
    $delete_sub_stmt = $conn->prepare("DELETE FROM categories WHERE category_name = ?");
    $delete_sub_stmt->execute([$category_name]);

    // Delete the category itself
    $delete_category_stmt = $conn->prepare("DELETE FROM categories WHERE category_name = ?");
    $delete_category_stmt->execute([$category_name]);

    // Commit the transaction
    $conn->commit();

    echo "Category and subcategories deleted successfully!";
    header("Location: index.php"); // Redirect back to the categories list
    exit;
} catch (Exception $e) {
    // Rollback in case of error
    $conn->rollBack();
    echo "Error deleting category: " . $e->getMessage();
}
?>
