<?php
// This script reads all plain text passwords in the `users` table and hashes them
// using password_hash() with PASSWORD_BCRYPT. Run this script once from the browser
// or CLI to upgrade existing user security.

require_once __DIR__ . '/../includes/db.php';

// Fetch all users
$query = "SELECT user_id, password FROM users";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

$updated_count = 0;
$skipped_count = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $current_password = $row['password'];
    
    // Check if the password is already a valid hash
    // password_get_info() returns 'algo' => 0 for plain text
    $info = password_get_info($current_password);
    
    if ($info['algo'] === 0) {
        // It's a plain text password (or unsupported hash), let's hash it
        $hashed_password = password_hash($current_password, PASSWORD_BCRYPT);
        
        $update_query = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $hashed_password, $row['user_id']);
            if (mysqli_stmt_execute($stmt)) {
                $updated_count++;
            } else {
                echo "Failed to update user ID: " . $row['user_id'] . "<br>";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Failed to prepare statement: " . mysqli_error($conn) . "<br>";
        }
    } else {
        // Password is already hashed
        $skipped_count++;
    }
}

echo "<h2>Migration Completed Successfully</h2>";
echo "<p><strong>$updated_count</strong> plain text passwords were hashed and updated.</p>";
echo "<p><strong>$skipped_count</strong> passwords were already hashed and skipped.</p>";

// Close connection
mysqli_close($conn);
?>
