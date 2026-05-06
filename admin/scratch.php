<?php
$con = mysqli_connect('localhost', 'root', '', 'eproject');
$res = mysqli_query($con, "SELECT p_id, p_name, p_image FROM product WHERE p_name LIKE '%bangle%' OR p_name LIKE '%chunk%'");
while($r = mysqli_fetch_assoc($res)) {
    print_r($r);
}
?>
