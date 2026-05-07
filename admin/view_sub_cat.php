<?php
include 'config.php';
if (isset($_GET['del_id'])) {
    $id=(int)$_GET['del_id'];
    mysqli_query($con,"DELETE FROM sub_cat WHERE sub_cat_id=$id");
    echo "<script>location='view_sub_cat.php';</script>"; exit;
}
if (isset($_POST['add_sub'])) {
    $name = mysqli_real_escape_string($con, trim($_POST['sub_cat_name']));
    $mcat = (int)$_POST['mcat_id'];
    if ($name!='' && $mcat>0) mysqli_query($con,"INSERT INTO sub_cat (sub_cat_name,mcat_id) VALUES ('$name',$mcat)");
    echo "<script>location='view_sub_cat.php';</script>"; exit;
}
include 'header.php';
$mains = mysqli_query($con,"SELECT * FROM main_cat ORDER BY mcat_name");
$rows = mysqli_query($con,"SELECT s.*,m.mcat_name FROM sub_cat s LEFT JOIN main_cat m ON s.mcat_id=m.mcat_id ORDER BY s.sub_cat_id DESC");
?>
<div class="page-header"><div><h1>Sub Categories</h1><div class="crumb">Manage sub-categories under main categories</div></div></div>

<div class="grid-2" style="grid-template-columns:1fr 2fr;">
  <div class="card">
    <h3 class="card-title">Add Sub Category</h3>
    <form method="post">
      <div class="form-group">
        <label>Main Category</label>
        <select class="form-control" name="mcat_id" required>
          <option value="">Select main category</option>
          <?php while($m=mysqli_fetch_assoc($mains)): ?>
            <option value="<?= $m['mcat_id'] ?>"><?= htmlspecialchars($m['mcat_name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Sub Category Name</label>
        <input type="text" name="sub_cat_name" class="form-control" required>
      </div>
      <button class="btn btn-primary" name="add_sub"><i class="fas fa-plus"></i> Add</button>
    </form>
  </div>

  <div class="card">
    <h3 class="card-title">All Sub Categories</h3>
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>ID</th><th>Name</th><th>Main Category</th><th style="width:140px">Actions</th></tr></thead>
        <tbody>
        <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
          <tr>
            <td>#<?= $r['sub_cat_id'] ?></td>
            <td><strong><?= htmlspecialchars($r['sub_cat_name']) ?></strong></td>
            <td><span class="pill purple"><?= htmlspecialchars($r['mcat_name']??'-') ?></span></td>
            <td>
              <a href="edit_sub_cat.php?id=<?= $r['sub_cat_id'] ?>" class="btn btn-soft btn-icon"><i class="fas fa-pen"></i></a>
              <a href="?del_id=<?= $r['sub_cat_id'] ?>" class="btn btn-danger btn-icon" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="4" class="text-center text-muted py-4">No sub categories</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
