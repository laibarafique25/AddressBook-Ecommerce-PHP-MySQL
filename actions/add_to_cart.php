<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (isset($_GET['p_id'])) {
    $p_id = $_GET['p_id'];
    $scard_id = isset($_GET['scard_id']) && $_GET['scard_id'] != 'NULL' ? $_GET['scard_id'] : 'NULL';

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // 1. Check if user has an active cart
        $cart_query = "SELECT cart_id FROM cart WHERE user_id = '$user_id' LIMIT 1";
        $cart_res = mysqli_query($conn, $cart_query);

        if (mysqli_num_rows($cart_res) > 0) {
            $cart_row = mysqli_fetch_assoc($cart_res);
            $cart_id = $cart_row['cart_id'];
        } else {
            // Create a new cart for the user
            $create_cart = "INSERT INTO cart (user_id) VALUES ('$user_id')";
            if (mysqli_query($conn, $create_cart)) {
                $cart_id = mysqli_insert_id($conn);
            } else {
                die("Error creating cart: " . mysqli_error($conn));
            }
        }

        // 2. Check if product already exists in cart_item
        $item_query = "SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = '$cart_id' AND p_id = '$p_id'";
        if ($scard_id != 'NULL') {
            $item_query .= " AND scard_id = '$scard_id'";
        } else {
            $item_query .= " AND scard_id IS NULL";
        }
        
        $item_res = mysqli_query($conn, $item_query);

        if (mysqli_num_rows($item_res) > 0) {
            // Increment quantity
            $item_row = mysqli_fetch_assoc($item_res);
            $new_qty = $item_row['quantity'] + 1;
            $cart_item_id = $item_row['cart_item_id'];
            $update_query = "UPDATE cart_items SET quantity = '$new_qty' WHERE cart_item_id = '$cart_item_id'";
            mysqli_query($conn, $update_query);
        } else {
            // Insert new item
            $insert_item = "INSERT INTO cart_items (cart_id, p_id, quantity, scard_id) VALUES ('$cart_id', '$p_id', 1, $scard_id)";
            mysqli_query($conn, $insert_item);
        }
    } else {
        // Guest Cart Logic
        if (!isset($_SESSION['guest_cart'])) {
            $_SESSION['guest_cart'] = [];
        }
        
        // Generate a unique key for the item based on product ID and shade ID
        $item_key = $p_id . '_' . $scard_id;
        
        if (isset($_SESSION['guest_cart'][$item_key])) {
            $_SESSION['guest_cart'][$item_key]['quantity'] += 1;
        } else {
            // Generate a random temporary cart_item_id for guest items to be used in UI updates/removes
            $temp_id = uniqid('guest_item_');
            $_SESSION['guest_cart'][$item_key] = [
                'cart_item_id' => $temp_id,
                'p_id' => $p_id,
                'scard_id' => $scard_id,
                'quantity' => 1
            ];
        }
    }

    $_SESSION['status'] = "Product added to cart!";
    $_SESSION['status_title'] = "Success";
    $_SESSION['status_icon'] = "success";
    header("Location: ../pages/shopcart.php");
    exit();
} else {
    header("Location: ../pages/index.php");
}
?>
