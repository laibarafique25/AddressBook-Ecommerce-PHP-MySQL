<?php
include 'config.php';
$id=(int)($_GET['ed_id']??0);
if (isset($_POST['update_role'])) {
    $n=mysqli_real_escape_string($con,trim($_POST['name']));
    mysqli_query($con,"UPDATE role SET name='$n' WHERE id=$id");
    echo "<script>location='addrole.php';</script>"; exit;
}
$row=mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM role WHERE id=$id"));
include 'header.php';
?>
<div class="page-header"><div><h1>Edit Role</h1></div></div>
<div class="card" style="max-width:600px">
  <form method="post">
    <div class="form-group"><label>Role Name</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']??'') ?>" required></div>
    <button class="btn btn-primary" name="update_role"><i class="fas fa-save"></i> Update</button>
    <a href="addrole.php" class="btn btn-soft">Cancel</a>
  </form>
</div>
<?php include 'footer.php'; ?>
