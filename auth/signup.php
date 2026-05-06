<?php 
session_start();
require_once __DIR__ . '/../includes/db.php';

if (isset($_POST['register_user'])) {
    $name    = trim($_POST['user_name']);
    $email   = trim($_POST['user_email']);
    $pass    = password_hash($_POST['user_pass'], PASSWORD_BCRYPT); // Secure hashing
    $phone   = trim($_POST['user_phone']);
    $address = trim($_POST['user_address']);
    $dob     = trim($_POST['user_dob']);
    $remarks = trim($_POST['user_remarks']);

    // Get CUSTOMERS role_id
    $role_query = "SELECT id FROM role WHERE name = 'CUSTOMERS' LIMIT 1";
    $role_result = mysqli_query($conn, $role_query);
    
    if($role_row = mysqli_fetch_assoc($role_result)) {
        $role_id = $role_row['id'];
    } else {
        die("Error: 'CUSTOMERS' role not found.");
    }

    // Check if email exists using prepared statement
    $check_email = "SELECT email FROM users WHERE email = ?";
    $stmt_check = mysqli_prepare($conn, $check_email);
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    $run_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($run_check) > 0) {
        $_SESSION['status'] = "This email is already registered!";
        $_SESSION['status_title'] = "Error";
        $_SESSION['status_icon'] = "error";
        header("Location: ../auth/signup.php");
        exit();
    } else {
        // Insert new user using prepared statement
        $sql = "INSERT INTO users (name, email, password, role_id, address, phone, dob, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt_insert, "sssissss", $name, $email, $pass, $role_id, $address, $phone, $dob, $remarks);

        if (mysqli_stmt_execute($stmt_insert)) {
            $_SESSION['status'] = "Registration Successful!";
            $_SESSION['status_title'] = "Success";
            $_SESSION['status_icon'] = "success";
            header("Location: ../auth/login.php");
            exit();
        } else {
            $_SESSION['status'] = "Database Error: " . mysqli_error($conn);
            $_SESSION['status_icon'] = "error";
            header("Location: ../auth/signup.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .error-msg { color: #ca1515; font-size: 13px; display: none; margin-top: 2px; font-weight: 500; }
        .password-container { position: relative; }
        .toggle-password { position: absolute; right: 15px; top: 45px; cursor: pointer; color: #666; z-index: 10; }
        textarea { width: 100%; border: 1px solid #ebebeb; padding: 10px 20px; font-size: 14px; color: #666666; border-radius: 5px; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../includes/alert.php'; ?>

<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="../pages/index.php"><i class="fa fa-home"></i> Home</a>
                    <span>Register</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<section class="checkout spad">
    <div class="container">
        <form action="" method="POST" class="checkout__form" id="registrationForm" novalidate>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h5>Create An Account</h5>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="checkout__form__input">
                                <p>Full Name <span>*</span></p>
                                <input type="text" name="user_name" id="user_name">
                                <span id="name_error" class="error-msg">Name must be at least 3 characters.</span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="checkout__form__input">
                                <p>Email Address <span>*</span></p>
                                <input type="email" name="user_email" id="user_email">
                                <span id="email_error" class="error-msg">Enter a valid email address.</span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="checkout__form__input password-container">
                                <p>Password <span>*</span></p>
                                <input type="password" name="user_pass" id="user_pass">
                                <i class="fa fa-eye toggle-password" id="eyeIcon"></i>
                                <span id="pass_error" class="error-msg">Password must be 8+ characters.</span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="checkout__form__input">
                                <p>Phone Number <span>*</span></p>
                                <input type="text" name="user_phone" id="user_phone">
                                <span id="phone_error" class="error-msg">Valid phone: 10-15 digits.</span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="checkout__form__input">
                                <p>Date of Birth <span>*</span></p>
                                <input type="date" name="user_dob" id="user_dob">
                                <span id="dob_error" class="error-msg">Please select your date of birth.</span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="checkout__form__input">
                                <p>Address <span>*</span></p>
                                <input type="text" name="user_address" id="user_address">
                                <span id="address_error" class="error-msg">Please provide your address.</span>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="checkout__form__input">
                                <p>Remarks (Optional)</p>
                                <textarea name="user_remarks" id="user_remarks" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <button type="submit" name="register_user" class="site-btn" style="width: 100%;">Create Account</button>
                            <p class="text-center mt-3">Already have an account? <a href="../auth/login.php" style="color: #ca1515; font-weight: 600;">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script src="../assets/js/jquery-3.3.1.min.js"></script>
<script>
// Password Toggle
$('#eyeIcon').click(function() {
    let input = $('#user_pass');
    if(input.attr('type') == 'password'){
        input.attr('type', 'text');
        $(this).removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        input.attr('type', 'password');
        $(this).removeClass('fa-eye-slash').addClass('fa-eye');
    }
});

// Submit Validation
$('#registrationForm').on('submit', function(e) {
    let isValid = true;
    $('.error-msg').hide();

    if($('#user_name').val().trim().length < 3) { $('#name_error').show(); isValid = false; }
    let email = $('#user_email').val().trim();
    if(email == "" || !email.includes('@')) { $('#email_error').show(); isValid = false; }
    if($('#user_pass').val().length < 8) { $('#pass_error').show(); isValid = false; }
    let phoneRegex = /^[0-9]{10,15}$/;
    if(!phoneRegex.test($('#user_phone').val().trim())) { $('#phone_error').show(); isValid = false; }
    if($('#user_dob').val() == "") { $('#dob_error').show(); isValid = false; }
    if($('#user_address').val().trim().length < 5) { $('#address_error').show(); isValid = false; }

    if(!isValid) e.preventDefault();
});
</script>
</body>
</html>