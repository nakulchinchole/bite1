<?php
include 'db.php';
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
$customer_id = $_SESSION['customer_id'];
$restaurant_id = $_POST['restaurant_id'];
$cart_data = json_decode($_POST['cart_data'], true);
$total = $_POST['total'];
$address = $_POST['address'];

mysqli_begin_transaction($conn);
try {
    $sql = "INSERT INTO orders (customer_id, restaurant_id, total_amount, status, address) VALUES ($customer_id, $restaurant_id, $total, 'placed', '$address')";
    mysqli_query($conn, $sql);
    $order_id = mysqli_insert_id($conn);
    foreach ($cart_data as $item_id => $item) {
        $menu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM menu_items WHERE item_id=$item_id"));
        $subtotal = $menu['price'] * $item['qty'];
        mysqli_query($conn, "INSERT INTO order_items (order_id, item_id, quantity, subtotal) VALUES ($order_id, $item_id, {$item['qty']}, $subtotal)");
    }
    mysqli_commit($conn);
    header("Location: track_order.php?id=$order_id");
    exit();
} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: index.php?error=Order failed");
    exit();
}
?>