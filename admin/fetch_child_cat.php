<?php
include 'config.php';
if (isset($_POST['s_id'])) {
    $id=(int)$_POST['s_id'];
    $q=mysqli_query($con,"SELECT * FROM child_cat WHERE scat_id=$id ORDER BY ccat_name");
    if (mysqli_num_rows($q)==0) { echo ''; exit; }
    while($r=mysqli_fetch_assoc($q)) echo "<option value='{$r['ccat_id']}'>".htmlspecialchars($r['ccat_name'])."</option>";
}
?>
