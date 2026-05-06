<?php
include 'config.php';
if (isset($_POST['m_id'])) {
    $id=(int)$_POST['m_id'];
    $q=mysqli_query($con,"SELECT * FROM sub_cat WHERE mcat_id=$id ORDER BY sub_cat_name");
    while($r=mysqli_fetch_assoc($q)) echo "<option value='{$r['sub_cat_id']}'>".htmlspecialchars($r['sub_cat_name'])."</option>";
}
?>
