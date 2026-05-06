<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (isset($_POST['update_cart'])) {
    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        if (isset($_SESSION['user_id'])) {
            // Logged-in User Logic
            foreach ($_POST['qty'] as $item_id => $quantity) {
                $item_id = mysqli_real_escape_string($conn, $item_id);
                $quantity = (int)$quantity;
                
                if ($quantity > 0) {
                    $update_query = "UPDATE cart_items SET quantity = '$quantity' WHERE cart_item_id = '$item_id'";
                    mysqli_query($conn, $update_query);
                } else {
                    $delete_query = "DELETE FROM cart_items WHERE cart_item_id = '$item_id'";
                    mysqli_query($conn, $delete_query);
                }
            }
        } else {
            // Guest Cart Logic
            if (isset($_SESSION['guest_cart'])) {
                foreach ($_POST['qty'] as $item_id => $quantity) {
                    $quantity = (int)$quantity;
                    foreach ($_SESSION['guest_cart'] as $key => $item) {
                        if ($item['cart_item_id'] == $item_id) {
                            if ($quantity > 0) {
                                $_SESSION['guest_cart'][$key]['quantity'] = $quantity;
                            } else {
                                unset($_SESSION['guest_cart'][$key]);
                            }
                            break;
                        }
                    }
                }
            }
        }
        
        $_SESSION['status'] = "Cart updated successfully!";
        $_SESSION['status_title'] = "Success";
        $_SESSION['status_icon'] = "success";
        header("Location: ../pages/shopcart.php");
        exit();
    } else {
        header("Location: ../pages/shopcart.php");
    }
} else {
    header("Location: ../pages/shopcart.php");
}
?>
