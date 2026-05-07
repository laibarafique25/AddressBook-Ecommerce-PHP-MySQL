<?php 
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

if (isset($_POST['update_password'])) {
    $email = $_SESSION['reset_email'];
    $pass = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $query = "UPDATE users SET password = '$pass' WHERE email = '$email'";
    if (mysqli_query($conn, $query)) {
        unset($_SESSION['reset_email']);
        $_SESSION['status'] = "Password Updated Successfully!";
        $_SESSION['status_title'] = "Success";
        $_SESSION['status_icon'] = "success";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['status'] = "Error updating password";
        $_SESSION['status_icon'] = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
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
                <h4 class="text-center mb-4" style="font-weight: 700;">RESET PASSWORD</h4>
                <p class="text-muted text-center mb-4">Set a new password for <strong><?php echo $_SESSION['reset_email']; ?></strong></p>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="8" style="padding: 12px; border-radius: 8px;">
                    </div>
                    <button type="submit" name="update_password" class="btn w-100" style="background: #ca1515; color: white; padding: 12px; font-weight: 600;">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
