<?php
require_once __DIR__ . '/../includes/db.php'; // Apni DB file ka sahi naam check kar lena
session_start();

$p_id = $_POST['p_id'];

if (isset($_SESSION['user_id'])) {
    // Logged-in User Logic
    $user_id = $_SESSION['user_id'];

    // Check karein ke product pehle se wishlist mein hai ya nahi
    $check_query = "SELECT * FROM wishlist WHERE user_id = '$user_id' AND p_id = '$p_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Agar pehle se hai, to remove kar dein (Unlike)
        $delete_query = "DELETE FROM wishlist WHERE user_id = '$user_id' AND p_id = '$p_id'";
        mysqli_query($conn, $delete_query);
        echo "removed";
    } else {
        // Agar nahi hai, to add kar dein (Like)
        $insert_query = "INSERT INTO wishlist (user_id, p_id) VALUES ('$user_id', '$p_id')";
        mysqli_query($conn, $insert_query);
        echo "added";
    }
} else {
    // Guest Wishlist Logic
    if (!isset($_SESSION['guest_wishlist'])) {
        $_SESSION['guest_wishlist'] = [];
    }
    
    if (in_array($p_id, $_SESSION['guest_wishlist'])) {
        // Remove it
        $key = array_search($p_id, $_SESSION['guest_wishlist']);
        if ($key !== false) {
            unset($_SESSION['guest_wishlist'][$key]);
            $_SESSION['guest_wishlist'] = array_values($_SESSION['guest_wishlist']); // Re-index
        }
        echo "removed";
    } else {
        // Add it
        $_SESSION['guest_wishlist'][] = $p_id;
        echo "added";
    }
}
?>