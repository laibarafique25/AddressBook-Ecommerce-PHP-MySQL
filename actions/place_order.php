<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Calculate total amount and verify items exist in cart
$cart_query = "SELECT ci.*, p.p_price 
               FROM cart_items ci 
               JOIN cart c ON ci.cart_id = c.cart_id 
               JOIN product p ON ci.p_id = p.p_id 
               WHERE c.user_id = '$user_id'";
$cart_res = mysqli_query($conn, $cart_query);

if (mysqli_num_rows($cart_res) == 0) {
    $_SESSION['status'] = "Your cart is empty!";
    $_SESSION['status_title'] = "Empty Cart";
    $_SESSION['status_icon'] = "warning";
    header("Location: ../pages/index.php");
    exit();
}

$total_amount = 0;
$items = [];
while ($item = mysqli_fetch_assoc($cart_res)) {
    $total_amount += ($item['p_price'] * $item['quantity']);
    $items[] = $item;
}

// 2. Capture payment method and Insert into orders table
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method'] ?? 'Cash on Delivery');
$status = "pending";
$order_query = "INSERT INTO orders (user_id, total_amount, status, payment_method) VALUES ('$user_id', '$total_amount', '$status', '$payment_method')";
if (mysqli_query($conn, $order_query)) {
    $order_id = mysqli_insert_id($conn);

    // 3. Insert items into order_details table
    foreach ($items as $item) {
        $p_id = $item['p_id'];
        $quantity = $item['quantity'];
        $price = $item['p_price'];
        $scard_id = isset($item['scard_id']) && $item['scard_id'] ? $item['scard_id'] : 'NULL';

        $detail_query = "INSERT INTO order_details (order_id, p_id, quantity, price, scard_id) VALUES ('$order_id', '$p_id', '$quantity', '$price', $scard_id)";
        mysqli_query($conn, $detail_query);
    }

    // 4. Clear the user's cart items
    $clear_cart = "DELETE ci FROM cart_items ci 
                   JOIN cart c ON ci.cart_id = c.cart_id 
                   WHERE c.user_id = '$user_id'";
    mysqli_query($conn, $clear_cart);

    // 5. Redirect to success page
    header("Location: ../pages/order_success.php?order_id=" . $order_id);
} else {
    echo "Error placing order: " . mysqli_error($conn);
}
?>