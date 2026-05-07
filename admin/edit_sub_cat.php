<?php
include 'config.php';
$id=(int)($_GET['id']??0);
if (isset($_POST['update_sub'])) {
    $name=mysqli_real_escape_string($con,trim($_POST['sub_cat_name']));
    $m=(int)$_POST['mcat_id'];
    mysqli_query($con,"UPDATE sub_cat SET sub_cat_name='$name', mcat_id=$m WHERE sub_cat_id=$id");
    echo "<script>location='view_sub_cat.php';</script>"; exit;
}
$row=mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM sub_cat WHERE sub_cat_id=$id"));
$mains=mysqli_query($con,"SELECT * FROM main_cat ORDER BY mcat_name");
include 'header.php';
?>
<div class="page-header"><div><h1>Edit Sub Category</h1></div></div>
<div class="card" style="max-width:600px">
  <form method="post">
    <div class="form-group">
      <label>Main Category</label>
      <select class="form-control" name="mcat_id" required>
        <?php while($m=mysqli_fetch_assoc($mains)): ?>
          <option value="<?= $m['mcat_id'] ?>" <?= $m['mcat_id']==$row['mcat_id']?'selected':'' ?>><?= htmlspecialchars($m['mcat_name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Name</label>
      <input type="text" name="sub_cat_name" class="form-control" value="<?= htmlspecialchars($row['sub_cat_name']??'') ?>" required>
    </div>
    <button class="btn btn-primary" name="update_sub"><i class="fas fa-save"></i> Update</button>
    <a href="view_sub_cat.php" class="btn btn-soft">Cancel</a>
  </form>
</div>
<?php include 'footer.php'; ?>
