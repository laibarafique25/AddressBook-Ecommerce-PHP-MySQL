<?php
include "includes/db.php";

echo "TABLES:\n";
$res = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_row($res)) {
    echo $row[0] . "\n";
}

echo "\nPRODUCT TABLE SCHEMA:\n";
$res = mysqli_query($conn, "DESCRIBE product");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
