<?php
include 'config.php';
if (isset($_GET['del_id'])) {
    $id=(int)$_GET['del_id'];
    mysqli_query($con,"DELETE FROM users WHERE user_id=$id");
    echo "<script>location='viewusers.php';</script>"; exit;
}
include 'header.php';
$rows=mysqli_query($con,"SELECT u.*, r.name role_name FROM users u LEFT JOIN role r ON u.role_id=r.id ORDER BY u.user_id DESC");
?>
<div class="page-header"><div><h1>Customers</h1><div class="crumb">All registered users</div></div></div>

<div class="card">
  <div class="table-wrap">
    <table class="data">
      <thead><tr><th></th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th style="width:80px">Actions</th></tr></thead>
      <tbody>
      <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
        <tr>
          <td><div class="avatar-sm"><?= strtoupper(substr($r['name']??'?',0,1)) ?></div></td>
          <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
          <td><?= htmlspecialchars($r['email']) ?></td>
          <td><?= htmlspecialchars($r['phone']??'-') ?></td>
          <td><span class="pill blue"><?= htmlspecialchars($r['role_name']??'User') ?></span></td>
          <td><a href="?del_id=<?= $r['user_id'] ?>" class="btn btn-danger btn-icon" onclick="return confirm('Delete user?')"><i class="fas fa-trash"></i></a></td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No users</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include 'footer.php'; ?>
