<?php
include 'config.php';
$id = (int)($_GET['id'] ?? 0);
if (isset($_POST['update_main'])) {
    $name = mysqli_real_escape_string($con, trim($_POST['mcat_name']));
    mysqli_query($con, "UPDATE main_cat SET mcat_name='$name' WHERE mcat_id=$id");
    echo "<script>alert('Updated');location='view_main_cat.php';</script>"; exit;
}
$row = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM main_cat WHERE mcat_id=$id"));
include 'header.php';
?>
<div class="page-header"><div><h1>Edit Main Category</h1><div class="crumb">Update category details</div></div></div>
<div class="card" style="max-width:600px">
  <form method="post">
    <div class="form-group">
      <label>Category Name</label>
      <input type="text" name="mcat_name" class="form-control" value="<?= htmlspecialchars($row['mcat_name']??'') ?>" required>
    </div>
    <button class="btn btn-primary" name="update_main"><i class="fas fa-save"></i> Update</button>
    <a href="view_main_cat.php" class="btn btn-soft">Cancel</a>
  </form>
</div>
<?php include 'footer.php'; ?>
