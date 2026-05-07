<?php
include 'config.php';
if (isset($_GET['del_id'])) {
    $id=(int)$_GET['del_id'];
    mysqli_query($con,"DELETE FROM role WHERE id=$id");
    echo "<script>location='addrole.php';</script>"; exit;
}
if (isset($_POST['add_role'])) {
    $n=mysqli_real_escape_string($con,trim($_POST['name']));
    if ($n!='') mysqli_query($con,"INSERT INTO role (name) VALUES ('$n')");
    echo "<script>location='addrole.php';</script>"; exit;
}
include 'header.php';
$rows=mysqli_query($con,"SELECT * FROM role ORDER BY id DESC");
?>
<div class="page-header"><div><h1>Roles</h1><div class="crumb">User roles & permissions</div></div></div>

<div class="grid-2" style="grid-template-columns:1fr 2fr;">
  <div class="card">
    <h3 class="card-title">Add Role</h3>
    <form method="post">
      <div class="form-group"><label>Role Name</label><input type="text" name="name" class="form-control" required></div>
      <button class="btn btn-primary" name="add_role"><i class="fas fa-plus"></i> Add Role</button>
    </form>
  </div>
  <div class="card">
    <h3 class="card-title">All Roles</h3>
    <div class="table-wrap">
      <table class="data">
        <thead><tr><th>ID</th><th>Name</th><th style="width:140px">Actions</th></tr></thead>
        <tbody>
        <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
          <tr>
            <td>#<?= $r['id'] ?></td>
            <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
            <td>
              <a href="editrole.php?ed_id=<?= $r['id'] ?>" class="btn btn-soft btn-icon"><i class="fas fa-pen"></i></a>
              <a href="?del_id=<?= $r['id'] ?>" class="btn btn-danger btn-icon" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="3" class="text-center text-muted py-4">No roles</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
