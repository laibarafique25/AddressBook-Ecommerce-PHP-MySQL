<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (isset($_GET['id'])) {
    $item_id = $_GET['id'];
    
    if (isset($_SESSION['user_id'])) {
        // Logged-in User Logic
        $user_id = $_SESSION['user_id'];
        $check_query = "SELECT ci.cart_item_id 
                        FROM cart_items ci 
                        JOIN cart c ON ci.cart_id = c.cart_id 
                        WHERE ci.cart_item_id = '$item_id' AND c.user_id = '$user_id'";
        $check_res = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_res) > 0) {
            $delete_query = "DELETE FROM cart_items WHERE cart_item_id = '$item_id'";
            if (mysqli_query($conn, $delete_query)) {
                $_SESSION['status'] = "Item removed from cart!";
                $_SESSION['status_title'] = "Removed";
                $_SESSION['status_icon'] = "success";
                header("Location: ../pages/shopcart.php");
                exit();
            } else {
                echo "Error deleting item: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['status'] = "Access denied!";
            $_SESSION['status_title'] = "Error";
            $_SESSION['status_icon'] = "error";
            header("Location: ../pages/shopcart.php");
            exit();
        }
    } else {
        // Guest Cart Logic
        if (isset($_SESSION['guest_cart'])) {
            foreach ($_SESSION['guest_cart'] as $key => $item) {
                if ($item['cart_item_id'] == $item_id) {
                    unset($_SESSION['guest_cart'][$key]);
                    $_SESSION['status'] = "Item removed from cart!";
                    $_SESSION['status_title'] = "Removed";
                    $_SESSION['status_icon'] = "success";
                    break;
                }
            }
        }
        header("Location: ../pages/shopcart.php");
        exit();
    }
} else {
    header("Location: ../pages/shopcart.php");
}
?>
