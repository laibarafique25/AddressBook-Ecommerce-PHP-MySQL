<?php
include "includes/db.php";

$sql = "CREATE TABLE IF NOT EXISTS product_reviews (
    review_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    p_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    rating INT(1) NOT NULL,
    review_text TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (p_id) REFERENCES product(p_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql)) {
    echo "Table product_reviews created successfully or already exists.\n";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "\n";
}
?>
