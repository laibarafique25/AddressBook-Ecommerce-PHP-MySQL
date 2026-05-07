<?php
include 'config.php';
if (isset($_GET['del_id'])) {
    $id=(int)$_GET['del_id'];
    mysqli_query($con,"DELETE FROM child_cat WHERE ccat_id=$id");
    echo "<script>location='view_child_cat.php';</script>"; exit;
}
if (isset($_POST['add_child'])) {
    $name=mysqli_real_escape_string($con,trim($_POST['ccat_name']));
    $s=(int)$_POST['scat_id'];
    if ($name!='' && $s>0) mysqli_query($con,"INSERT INTO child_cat (ccat_name,scat_id) VALUES ('$name',$s)");
    echo "<script>location='view_child_cat.php';</script>"; exit;
}
include 'header.php';
$subs=mysqli_query($con,"SELECT s.*,m.mcat_name FROM sub_cat s LEFT JOIN main_cat m ON s.mcat_id=m.mcat_id ORDER BY s.sub_cat_name");
$rows=mysqli_query($con,"SELECT c.*,s.sub_cat_name FROM child_cat c LEFT JOIN sub_cat s ON c.scat_id=s.sub_cat_id ORDER BY c.ccat_id DESC");
?>
<div class="page-header"><div><h1>Child Categories</h1><div class="crumb">Granular sub-sub categorization</div></div></div>

<div class="grid-2" style="grid-template-columns:1fr 2fr;">
  <div class="card">
    <h3 class="card-title">Add Child Category</h3>
    <form method="post">
      <div class="form-group">
        <label>Sub Category</label>
        <select class="form-control" name="scat_id" required>
          <option value="">Select sub category</option>
          <?php while($s=mysqli_fetch_assoc($subs)): ?>
            <option value="<?= $s['sub_cat_id'] ?>"><?= htmlspecialchars($s['sub_cat_name']) ?> <small>(<?= htmlspecialchars($s['mcat_name']??'') ?>)</small></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Child Category Name</label>
        <input type="text" name="ccat_name" class="form-control" required>
      </div>
      <button class="btn btn-primary" name="add_child"><i class="fas fa-plus"></i> Add</button>
    </form>
  </div>
  <div class="card">
    <h3 class="card-title">All Child Categories</h3>
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>ID</th><th>Name</th><th>Sub Category</th><th style="width:140px">Actions</th></tr></thead>
        <tbody>
        <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
          <tr>
            <td>#<?= $r['ccat_id'] ?></td>
            <td><strong><?= htmlspecialchars($r['ccat_name']) ?></strong></td>
            <td><span class="pill blue"><?= htmlspecialchars($r['sub_cat_name']??'-') ?></span></td>
            <td>
              <a href="edit_child_cat.php?id=<?= $r['ccat_id'] ?>" class="btn btn-soft btn-icon"><i class="fas fa-pen"></i></a>
              <a href="?del_id=<?= $r['ccat_id'] ?>" class="btn btn-danger btn-icon" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="4" class="text-center text-muted py-4">No child categories</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
