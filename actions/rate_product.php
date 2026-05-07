<?php
require_once __DIR__ . '/../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['status'] = "Please login to rate this product.";
        $_SESSION['status_title'] = "Login Required";
        $_SESSION['status_icon'] = "warning";
        header("Location: ../auth/login.php");
        exit();
    }

    $p_id = mysqli_real_escape_string($conn, $_POST['p_id']);
    $user_id = $_SESSION['user_id'];
    $rating = (int)$_POST['rating'];

    if ($rating < 1 || $rating > 5) {
        $_SESSION['status'] = "Invalid rating value.";
        $_SESSION['status_icon'] = "error";
        header("Location: ../pages/product-details.php?id=$p_id");
        exit();
    }

    // Check if user already rated
    $check_q = "SELECT review_id FROM product_reviews WHERE p_id = '$p_id' AND user_id = '$user_id'";
    $check_res = mysqli_query($conn, $check_q);

    if (mysqli_num_rows($check_res) > 0) {
        // Update existing rating
        $update_q = "UPDATE product_reviews SET rating = '$rating' WHERE p_id = '$p_id' AND user_id = '$user_id'";
        if (mysqli_query($conn, $update_q)) {
            $_SESSION['status'] = "Rating updated successfully!";
            $_SESSION['status_icon'] = "success";
        } else {
            $_SESSION['status'] = "Failed to update rating.";
            $_SESSION['status_icon'] = "error";
        }
    } else {
        // Insert new rating
        $insert_q = "INSERT INTO product_reviews (p_id, user_id, rating) VALUES ('$p_id', '$user_id', '$rating')";
        if (mysqli_query($conn, $insert_q)) {
            $_SESSION['status'] = "Rating submitted successfully!";
            $_SESSION['status_icon'] = "success";
        } else {
            $_SESSION['status'] = "Failed to submit rating.";
            $_SESSION['status_icon'] = "error";
        }
    }

    header("Location: ../pages/product-details.php?id=$p_id");
    exit();
} else {
    header("Location: ../pages/index.php");
    exit();
}
?>
