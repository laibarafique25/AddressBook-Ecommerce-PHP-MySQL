<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (isset($_GET['del_id'])) {
    $p_id = $_GET['del_id'];

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $delete_query = "DELETE FROM wishlist WHERE user_id = '$user_id' AND p_id = '$p_id'";
        mysqli_query($conn, $delete_query);
    } else {
        if (isset($_SESSION['guest_wishlist']) && in_array($p_id, $_SESSION['guest_wishlist'])) {
            $key = array_search($p_id, $_SESSION['guest_wishlist']);
            if ($key !== false) {
                unset($_SESSION['guest_wishlist'][$key]);
                // Re-index array
                $_SESSION['guest_wishlist'] = array_values($_SESSION['guest_wishlist']);
            }
        }
    }
}
header("Location: ../pages/cart_wishlist.php");
exit();
?>
