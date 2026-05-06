<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "eproject");

if (isset($_POST['login_user'])) {
    $email = trim($_POST['user_email']);
    $password = $_POST['user_password']; // Plain text password entered by user

    // Join query taake 'role' table se naam mil jaye. Use prepared statement to prevent SQL injection.
    $query = "SELECT users.*, role.name as role_name 
              FROM users 
              INNER JOIN role ON users.role_id = role.id 
              WHERE users.email = ?";
              
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user_data = mysqli_fetch_assoc($result)) {
        // Verify the hashed password
        if (password_verify($password, $user_data['password'])) {
            // Password is correct
            
            // Secure session handling: regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user_data['user_id'];
            $_SESSION['user_name'] = $user_data['name'];
            $_SESSION['role_name'] = $user_data['role_name']; // Role Name session mein save kiya

            $user_id = $user_data['user_id'];

            // --- Merge Guest Cart ---
            if (isset($_SESSION['guest_cart']) && count($_SESSION['guest_cart']) > 0) {
                // Ensure user has a cart
                $cart_query = "SELECT cart_id FROM cart WHERE user_id = ? LIMIT 1";
                $stmt_cart = mysqli_prepare($con, $cart_query);
                mysqli_stmt_bind_param($stmt_cart, "i", $user_id);
                mysqli_stmt_execute($stmt_cart);
                $cart_res = mysqli_stmt_get_result($stmt_cart);
                
                if (mysqli_num_rows($cart_res) > 0) {
                    $cart_row = mysqli_fetch_assoc($cart_res);
                    $cart_id = $cart_row['cart_id'];
                } else {
                    $create_cart = "INSERT INTO cart (user_id) VALUES (?)";
                    $stmt_create_cart = mysqli_prepare($con, $create_cart);
                    mysqli_stmt_bind_param($stmt_create_cart, "i", $user_id);
                    mysqli_stmt_execute($stmt_create_cart);
                    $cart_id = mysqli_insert_id($con);
                }

                foreach ($_SESSION['guest_cart'] as $item) {
                    $p_id = $item['p_id'];
                    $quantity = $item['quantity'];
                    $scard_id = $item['scard_id'];

                    $item_query = "SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = ? AND p_id = ?";
                    if ($scard_id != 'NULL') {
                        $item_query .= " AND scard_id = ?";
                        $stmt_item = mysqli_prepare($con, $item_query);
                        mysqli_stmt_bind_param($stmt_item, "iii", $cart_id, $p_id, $scard_id);
                    } else {
                        $item_query .= " AND scard_id IS NULL";
                        $stmt_item = mysqli_prepare($con, $item_query);
                        mysqli_stmt_bind_param($stmt_item, "ii", $cart_id, $p_id);
                    }
                    
                    mysqli_stmt_execute($stmt_item);
                    $item_res = mysqli_stmt_get_result($stmt_item);

                    if (mysqli_num_rows($item_res) > 0) {
                        $item_row = mysqli_fetch_assoc($item_res);
                        $new_qty = $item_row['quantity'] + $quantity;
                        $cart_item_id = $item_row['cart_item_id'];
                        $update_query = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
                        $stmt_update = mysqli_prepare($con, $update_query);
                        mysqli_stmt_bind_param($stmt_update, "ii", $new_qty, $cart_item_id);
                        mysqli_stmt_execute($stmt_update);
                    } else {
                        if ($scard_id != 'NULL') {
                            $insert_item = "INSERT INTO cart_items (cart_id, p_id, quantity, scard_id) VALUES (?, ?, ?, ?)";
                            $stmt_insert = mysqli_prepare($con, $insert_item);
                            mysqli_stmt_bind_param($stmt_insert, "iiii", $cart_id, $p_id, $quantity, $scard_id);
                        } else {
                            $insert_item = "INSERT INTO cart_items (cart_id, p_id, quantity, scard_id) VALUES (?, ?, ?, NULL)";
                            $stmt_insert = mysqli_prepare($con, $insert_item);
                            mysqli_stmt_bind_param($stmt_insert, "iii", $cart_id, $p_id, $quantity);
                        }
                        mysqli_stmt_execute($stmt_insert);
                    }
                }
                unset($_SESSION['guest_cart']);
            }

            // --- Merge Guest Wishlist ---
            if (isset($_SESSION['guest_wishlist']) && count($_SESSION['guest_wishlist']) > 0) {
                foreach ($_SESSION['guest_wishlist'] as $p_id) {
                    $check_query = "SELECT * FROM wishlist WHERE user_id = ? AND p_id = ?";
                    $stmt_check_wish = mysqli_prepare($con, $check_query);
                    mysqli_stmt_bind_param($stmt_check_wish, "ii", $user_id, $p_id);
                    mysqli_stmt_execute($stmt_check_wish);
                    $result_wish = mysqli_stmt_get_result($stmt_check_wish);
                    
                    if (mysqli_num_rows($result_wish) == 0) {
                        $insert_query = "INSERT INTO wishlist (user_id, p_id) VALUES (?, ?)";
                        $stmt_insert_wish = mysqli_prepare($con, $insert_query);
                        mysqli_stmt_bind_param($stmt_insert_wish, "ii", $user_id, $p_id);
                        mysqli_stmt_execute($stmt_insert_wish);
                    }
                }
                unset($_SESSION['guest_wishlist']);
            }

            // --- Role Name based redirection ---
            if ($user_data['role_name'] == 'ADMIN') { 
                $_SESSION['status'] = "Welcome Admin!";
                $_SESSION['status_title'] = "Success";
                $_SESSION['status_icon'] = "success";
                header("Location: ../admin/index.php");
                exit();
            } 
            else if ($user_data['role_name'] == 'CUSTOMERS') {
                $_SESSION['status'] = "Login Successful!";
                $_SESSION['status_title'] = "Success";
                $_SESSION['status_icon'] = "success";
                header("Location: ../pages/index.php");
                exit();
            }
            else {
                // Agar koi aur role hai (maslan 'Editor' ya 'Manager')
                header("Location: ../pages/index.php");
            }
        } else {
            // Invalid password
            $_SESSION['status'] = "Invalid Email or Password!";
            $_SESSION['status_title'] = "Error";
            $_SESSION['status_icon'] = "error";
            header("Location: ../auth/login.php");
            exit();
        }
    } else {
        // User not found
        $_SESSION['status'] = "Invalid Email or Password!";
        $_SESSION['status_title'] = "Error";
        $_SESSION['status_icon'] = "error";
        header("Location: ../auth/login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Address Book</title>
    <link href="https://fonts.googleapis.com/css2?family=Cookie&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php require_once __DIR__ . '/../includes/alert.php'; ?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="../pages/index.php"><i class="fa fa-home"></i> Home</a>
                    <span>Login</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="checkout spad">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <form action="" method="POST" class="checkout__form">
                    <div class="row">
                        <div class="col-lg-12">
                            <h5 style="text-align: center; border-bottom: none; margin-bottom: 30px; font-weight: 700; text-transform: uppercase;">Login Account</h5>
                            
                            <div class="checkout__form__input">
                                <p>Email <span>*</span></p>
                                <input type="email" name="user_email" placeholder="example@mail.com" required>
                            </div>
                            
                            <div class="checkout__form__input">
                                <p>Password <span>*</span></p>
                                <input type="password" name="user_password" placeholder="******" required>
                            </div>

                            <div class="checkout__form__checkbox">
                                <p>Don't have an account? <a href="../auth/signup.php" class="text-danger" style="font-weight: 600;">Register here</a></p>
                            </div>
                            
                            <button type="submit" name="login_user" class="site-btn w-100 mt-3">Login Now</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="../assets/js/jquery-3.3.1.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>