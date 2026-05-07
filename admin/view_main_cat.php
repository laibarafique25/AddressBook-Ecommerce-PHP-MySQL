<?php
include 'config.php';

// Delete
if (isset($_GET['del_id'])) {
    $id = (int)$_GET['del_id'];
    mysqli_query($con, "DELETE FROM main_cat WHERE mcat_id=$id");
    echo "<script>alert('Category deleted');location='view_main_cat.php';</script>"; exit;
}
// Insert
if (isset($_POST['add_main'])) {
    $name = mysqli_real_escape_string($con, trim($_POST['mcat_name']));
    if ($name!='') mysqli_query($con, "INSERT INTO main_cat (mcat_name) VALUES ('$name')");
    echo "<script>location='view_main_cat.php';</script>"; exit;
}

include 'header.php';
$rows = mysqli_query($con, "SELECT * FROM main_cat ORDER BY mcat_id DESC");
?>
<div class="page-header">
  <div><h1>Main Categories</h1><div class="crumb">Manage primary product categories</div></div>
</div>

<div class="grid-2" style="grid-template-columns: 1fr 2fr;">
  <div class="card">
    <h3 class="card-title">Add Category</h3>
    <div class="card-sub">Create a new main category</div>
    <form method="post">
      <div class="form-group">
        <label>Category Name</label>
        <input type="text" name="mcat_name" class="form-control" placeholder="e.g. Cosmetics" required>
      </div>
      <button class="btn btn-primary" name="add_main"><i class="fas fa-plus"></i> Add Category</button>
    </form>
  </div>

  <div class="card">
    <h3 class="card-title">All Categories</h3>
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>ID</th><th>Name</th><th style="width:140px">Actions</th></tr></thead>
        <tbody>
        <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
          <tr>
            <td>#<?= $r['mcat_id'] ?></td>
            <td><strong><?= htmlspecialchars($r['mcat_name']) ?></strong></td>
            <td>
              <a href="edit_main_cat.php?id=<?= $r['mcat_id'] ?>" class="btn btn-soft btn-icon" title="Edit"><i class="fas fa-pen"></i></a>
              <a href="?del_id=<?= $r['mcat_id'] ?>" class="btn btn-danger btn-icon" onclick="return confirm('Delete?')" title="Delete"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="3" class="text-center text-muted py-4">No categories yet</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
