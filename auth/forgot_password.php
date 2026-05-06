<?php 
session_start();
require_once __DIR__ . '/../includes/db.php';

if (isset($_POST['reset_request'])) {
    $email = mysqli_real_escape_string($conn, $_POST['user_email']);
    $query = "SELECT * FROM users WHERE email = '$email'";
    $res = mysqli_query($conn, $query);

    if (mysqli_num_rows($res) > 0) {
        $_SESSION['reset_email'] = $email;
        header("Location: reset_password.php");
        exit();
    } else {
        $_SESSION['status'] = "Email not found!";
        $_SESSION['status_icon'] = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background: #f4f4f4;">
<?php require_once __DIR__ . '/../includes/alert.php'; ?>

<div class="container" style="margin-top: 100px;">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4 shadow-sm border-0">
                <h4 class="text-center mb-4" style="font-weight: 700;">FORGOT PASSWORD</h4>
                <p class="text-muted text-center mb-4">Enter your email and we'll help you reset your password.</p>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="user_email" class="form-control" required style="padding: 12px; border-radius: 8px;">
                    </div>
                    <button type="submit" name="reset_request" class="btn w-100" style="background: #ca1515; color: white; padding: 12px; font-weight: 600;">Check Email</button>
                    <div class="text-center mt-3">
                        <a href="login.php" class="text-muted">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
