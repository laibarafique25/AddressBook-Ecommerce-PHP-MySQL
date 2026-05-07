<?php
include 'config.php';
// Use relative paths from /admin to avoid any URL encoding issues
$img_web_base = "../assets/images/shop/";

function normalize_product_image_name($val) {
    $val = trim((string)$val);
    if ($val === '') return '';
    $val = str_replace('\\', '/', $val);
    $val = rawurldecode($val);
    if (strpos($val, '/') !== false) $val = basename($val);
    return $val;
}

function product_image_web_url($filename) {
    global $img_web_base;
    $filename = normalize_product_image_name($filename);
    if ($filename === '') return '';

    $filename_no_ts = preg_replace('/^[0-9]+_/', '', $filename);
    $filename_pct_replaced = str_replace('%', '_', $filename);
    $filename_no_ts_pct_replaced = str_replace('%', '_', $filename_no_ts);

    $candidates = [
        ['fs' => __DIR__ . '/../assets/images/shop/',    'web' => "../assets/images/shop/"],
        ['fs' => __DIR__ . '/../assets/images/product/', 'web' => "../assets/images/product/"],
    ];
    foreach ($candidates as $c) {
        if (file_exists($c['fs'] . $filename)) return $c['web'] . rawurlencode($filename);
        if ($filename !== $filename_pct_replaced && file_exists($c['fs'] . $filename_pct_replaced)) return $c['web'] . rawurlencode($filename_pct_replaced);
        if ($filename !== $filename_no_ts && file_exists($c['fs'] . $filename_no_ts)) return $c['web'] . rawurlencode($filename_no_ts);
        if ($filename_no_ts !== $filename_no_ts_pct_replaced && file_exists($c['fs'] . $filename_no_ts_pct_replaced)) return $c['web'] . rawurlencode($filename_no_ts_pct_replaced);
    }
    return $img_web_base . rawurlencode($filename);
}
$id=(int)($_GET['ed_id']??0);
if($id <= 0) {
    header("Location: view_product.php");
    exit;
}

if (isset($_POST['update_product'])) {
    $name=mysqli_real_escape_string($con,trim($_POST['p_name']));
    $desc=mysqli_real_escape_string($con,trim($_POST['pro_description']));
    $price=(float)$_POST['p_price'];
    $scat=(int)$_POST['scat_id'];
    $ccat=isset($_POST['ccat_id'])&&$_POST['ccat_id']!=''?(int)$_POST['ccat_id']:'NULL';
    $img_sql='';
    if (!empty($_FILES['p_image']['name'])) {
        $img_dir = "../assets/images/shop";
        if (!is_dir($img_dir)) {
            mkdir($img_dir, 0777, true);
        }
        $img=time().'_'.basename($_FILES['p_image']['name']);
        move_uploaded_file($_FILES['p_image']['tmp_name'], $img_dir . '/' . $img);
        $img_sql=", p_image='$img'";
    }
    if(mysqli_query($con,"UPDATE product SET p_name='$name', pro_description='$desc', p_price=$price, scat_id=$scat, ccat_id=$ccat $img_sql WHERE p_id=$id")) {
        echo "<script>alert('Product Updated Successfully'); location='view_product.php';</script>"; 
    } else {
        echo "<script>alert('Error updating product: " . mysqli_error($con) . "');</script>";
    }
    exit;
}
$row_q = mysqli_query($con,"SELECT * FROM product WHERE p_id=$id");
if(mysqli_num_rows($row_q) == 0) {
    echo "<script>alert('Product not found'); location='view_product.php';</script>";
    exit;
}
$row=mysqli_fetch_assoc($row_q);
$subs=mysqli_query($con,"SELECT * FROM sub_cat ORDER BY sub_cat_name");
$childs=mysqli_query($con,"SELECT * FROM child_cat ORDER BY ccat_name");
include 'header.php';
?>
<div class="page-header"><div><h1>Edit Product</h1><div class="crumb">Update product details</div></div></div>
<div class="card">
  <form action="edit_product.php?ed_id=<?= $id ?>" method="post" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-md-6"><div class="form-group">
        <label>Product Name</label>
        <input type="text" name="p_name" class="form-control" value="<?= htmlspecialchars($row['p_name']??'') ?>" required>
      </div></div>
      <div class="col-md-3"><div class="form-group">
        <label>Price ($)</label>
        <input type="number" step="0.01" name="p_price" class="form-control" value="<?= $row['p_price']??'' ?>" required>
      </div></div>
      <div class="col-md-3"><div class="form-group">
        <label>Replace Image</label>
        <input type="file" name="p_image" class="form-control" accept="image/*">
        <?php if($row['p_image']): ?><img src="<?= htmlspecialchars(product_image_web_url($row['p_image'])) ?>" style="width:50px;height:50px;border-radius:8px;margin-top:6px;object-fit:cover"><?php endif; ?>
      </div></div>
      <div class="col-md-6"><div class="form-group">
        <label>Sub Category</label>
        <select name="scat_id" class="form-control" required>
          <?php while($s=mysqli_fetch_assoc($subs)): ?>
            <option value="<?= $s['sub_cat_id'] ?>" <?= $s['sub_cat_id']==$row['scat_id']?'selected':'' ?>><?= htmlspecialchars($s['sub_cat_name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div></div>
      <div class="col-md-6"><div class="form-group">
        <label>Child Category</label>
        <select name="ccat_id" class="form-control">
          <option value="">None</option>
          <?php while($c=mysqli_fetch_assoc($childs)): ?>
            <option value="<?= $c['ccat_id'] ?>" <?= $c['ccat_id']==$row['ccat_id']?'selected':'' ?>><?= htmlspecialchars($c['ccat_name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div></div>
      <div class="col-12"><div class="form-group">
        <label>Description</label>
        <textarea name="pro_description" class="form-control"><?= htmlspecialchars($row['pro_description']??'') ?></textarea>
      </div></div>
    </div>
    <button class="btn btn-primary" name="update_product"><i class="fas fa-save"></i> Update</button>
    <a href="view_product.php" class="btn btn-soft">Cancel</a>
  </form>
</div>
<?php include 'footer.php'; ?>
