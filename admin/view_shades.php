<?php
include 'config.php';
if (isset($_GET['del_id'])) {
    $id=(int)$_GET['del_id'];
    $r=mysqli_fetch_assoc(mysqli_query($con,"SELECT shade_image FROM shade_cards WHERE scard_id=$id"));
    if ($r && $r['shade_image'] && file_exists('img/'.$r['shade_image'])) @unlink('img/'.$r['shade_image']);
    mysqli_query($con,"DELETE FROM shade_cards WHERE scard_id=$id");
    echo "<script>location='view_shades.php';</script>"; exit;
}
if (isset($_POST['add_shade'])) {
    $g=mysqli_real_escape_string($con,trim($_POST['group_name']));
    $n=mysqli_real_escape_string($con,trim($_POST['shade_name']));
    $c=mysqli_real_escape_string($con,trim($_POST['shade_color_code']));
    $img='';
    if (!empty($_FILES['shade_image']['name'])) {
        if (!is_dir('img')) mkdir('img',0777,true);
        $img=time().'_'.basename($_FILES['shade_image']['name']);
        move_uploaded_file($_FILES['shade_image']['tmp_name'],'img/'.$img);
    }
    mysqli_query($con,"INSERT INTO shade_cards (group_name,shade_name,shade_color_code,shade_image) VALUES ('$g','$n','$c','$img')");
    echo "<script>location='view_shades.php';</script>"; exit;
}
include 'header.php';
$rows=mysqli_query($con,"SELECT * FROM shade_cards ORDER BY scard_id DESC");
?>
<div class="page-header"><div><h1>Shade Cards</h1><div class="crumb">Manage cosmetic color shades</div></div></div>

<div class="card mb-3">
  <h3 class="card-title">Add Shade</h3>
  <form method="post" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-md-3"><div class="form-group"><label>Group Name</label><input type="text" name="group_name" class="form-control" placeholder="e.g. Lipstick" required></div></div>
      <div class="col-md-3"><div class="form-group"><label>Shade Name</label><input type="text" name="shade_name" class="form-control" placeholder="e.g. Ruby Red" required></div></div>
      <div class="col-md-3"><div class="form-group"><label>Color Code</label><input type="color" name="shade_color_code" class="form-control" style="height:42px" required></div></div>
      <div class="col-md-3"><div class="form-group"><label>Image (optional)</label><input type="file" name="shade_image" class="form-control" accept="image/*"></div></div>
    </div>
    <button class="btn btn-primary" name="add_shade"><i class="fas fa-plus"></i> Add Shade</button>
  </form>
</div>

<div class="card">
  <h3 class="card-title">All Shades</h3>
  <div class="table-wrap">
    <table class="data">
      <thead><tr><th>Color</th><th>Name</th><th>Group</th><th>Code</th><th style="width:80px">Actions</th></tr></thead>
      <tbody>
      <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
        <tr>
          <td><span class="color-swatch" style="background:<?= htmlspecialchars($r['shade_color_code']) ?>"></span></td>
          <td><strong><?= htmlspecialchars($r['shade_name']) ?></strong></td>
          <td><span class="pill purple"><?= htmlspecialchars($r['group_name']) ?></span></td>
          <td><code><?= htmlspecialchars($r['shade_color_code']) ?></code></td>
          <td><a href="?del_id=<?= $r['scard_id'] ?>" class="btn btn-danger btn-icon" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a></td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="5" class="text-center text-muted py-4">No shades</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include 'footer.php'; ?>
