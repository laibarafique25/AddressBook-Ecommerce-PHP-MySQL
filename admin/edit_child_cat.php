<?php
include 'config.php';
$id=(int)($_GET['id']??0);
if (isset($_POST['update_child'])) {
    $name=mysqli_real_escape_string($con,trim($_POST['ccat_name']));
    $s=(int)$_POST['scat_id'];
    mysqli_query($con,"UPDATE child_cat SET ccat_name='$name', scat_id=$s WHERE ccat_id=$id");
    echo "<script>location='view_child_cat.php';</script>"; exit;
}
$row=mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM child_cat WHERE ccat_id=$id"));
$subs=mysqli_query($con,"SELECT * FROM sub_cat ORDER BY sub_cat_name");
include 'header.php';
?>
<div class="page-header"><div><h1>Edit Child Category</h1></div></div>
<div class="card" style="max-width:600px">
  <form method="post">
    <div class="form-group">
      <label>Sub Category</label>
      <select name="scat_id" class="form-control" required>
        <?php while($s=mysqli_fetch_assoc($subs)): ?>
          <option value="<?= $s['sub_cat_id'] ?>" <?= $s['sub_cat_id']==$row['scat_id']?'selected':'' ?>><?= htmlspecialchars($s['sub_cat_name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Name</label>
      <input type="text" name="ccat_name" class="form-control" value="<?= htmlspecialchars($row['ccat_name']??'') ?>" required>
    </div>
    <button class="btn btn-primary" name="update_child"><i class="fas fa-save"></i> Update</button>
    <a href="view_child_cat.php" class="btn btn-soft">Cancel</a>
  </form>
</div>
<?php include 'footer.php'; ?>
