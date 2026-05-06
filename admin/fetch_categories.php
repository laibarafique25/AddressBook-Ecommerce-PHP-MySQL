<?php
include 'config.php';
if (isset($_POST['m_id'])) {
    $id=(int)$_POST['m_id'];
    $q=mysqli_query($con,"SELECT * FROM sub_cat WHERE mcat_id=$id ORDER BY sub_cat_name");
    while($r=mysqli_fetch_assoc($q)) echo "<option value='{$r['sub_cat_id']}'>".htmlspecialchars($r['sub_cat_name'])."</option>";
    exit;
}
if (isset($_POST['s_id'])) {
    $id=(int)$_POST['s_id'];
    $q=mysqli_query($con,"SELECT * FROM child_cat WHERE scat_id=$id ORDER BY ccat_name");
    if (mysqli_num_rows($q)==0) { echo ''; exit; }
    while($r=mysqli_fetch_assoc($q)) echo "<option value='{$r['ccat_id']}'>".htmlspecialchars($r['ccat_name'])."</option>";
    exit;
}
?>
