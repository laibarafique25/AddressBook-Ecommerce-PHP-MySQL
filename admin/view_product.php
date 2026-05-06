<?php
include 'config.php';

// Use relative paths from /admin to avoid any URL encoding issues
$img_web_base = "../assets/images/shop/";

function normalize_product_image_name($val) {
    $val = trim((string)$val);
    if ($val === '') return '';
    $val = str_replace('\\', '/', $val);
    // Avoid double-encoding issues if DB stored an encoded filename
    $val = rawurldecode($val);
    // If DB accidentally stored a path like "img/shop/x.jpg", keep only the filename.
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

    // Fallback to the new canonical location
    return $img_web_base . rawurlencode($filename);
}

function product_image_debug_info($filename) {
    $filename = normalize_product_image_name($filename);
    if ($filename === '') return ['name' => '', 'url' => '', 'exists' => []];

    $checks = [
        'shop' => __DIR__ . '/../assets/images/shop/' . $filename,
        'product' => __DIR__ . '/../assets/images/product/' . $filename,
    ];
    $exists = [];
    foreach ($checks as $k => $fs) {
        $exists[$k] = file_exists($fs);
    }

    return [
        'name' => $filename,
        'url' => product_image_web_url($filename),
        'exists' => $exists,
    ];
}

// 1. Delete Product Logic
if (isset($_GET['del_id'])) {
    $id = (int)$_GET['del_id'];
    
    // Check if product is in any orders first (Protect order history)
    $check_orders = mysqli_query($con, "SELECT COUNT(*) as c FROM order_details WHERE p_id=$id");
    $order_count = mysqli_fetch_assoc($check_orders)['c'];

    if ($order_count > 0) {
        echo "<script>alert('Cannot delete: This product is linked to " . $order_count . " completed orders. Delete orders first or mark as inactive.'); location='view_product.php';</script>";
        exit;
    }

    // Clear dependent data that is safe to delete
    mysqli_query($con, "DELETE FROM cart_items WHERE p_id=$id");
    mysqli_query($con, "DELETE FROM wishlist WHERE p_id=$id");
    mysqli_query($con, "DELETE FROM product_ratings WHERE p_id=$id");

    $res = mysqli_query($con, "SELECT p_image FROM product WHERE p_id=$id");
    $r = mysqli_fetch_assoc($res);
    
    if ($r && !empty($r['p_image'])) {
        $img_name = normalize_product_image_name($r['p_image']);
        if ($img_name !== '') {
            $delete_candidates = [
                __DIR__ . '/../assets/images/shop/' . $img_name,
                __DIR__ . '/../assets/images/product/' . $img_name,
            ];
            foreach ($delete_candidates as $target) {
                if (file_exists($target)) {
                    @unlink($target);
                    break;
                }
            }
        }
    }
    
    if(mysqli_query($con, "DELETE FROM product WHERE p_id=$id")) {
        echo "<script>alert('Product deleted successfully'); location='view_product.php';</script>";
    } else {
        echo "<script>alert('Error deleting product: " . mysqli_real_escape_string($con, mysqli_error($con)) . "'); location='view_product.php';</script>";
    }
    exit;
}

// 2. Add Product Logic
if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($con, trim($_POST['p_name']));
    $desc = mysqli_real_escape_string($con, trim($_POST['pro_description']));
    $price = (float)$_POST['p_price'];
    $scat = (int)$_POST['scat_id'];
    $ccat = isset($_POST['ccat_id']) && $_POST['ccat_id'] != '' ? (int)$_POST['ccat_id'] : 'NULL';
    $img = '';

    if (!empty($_FILES['p_image']['name'])) {
        $img_dir = "../assets/images/shop";
        if (!is_dir($img_dir)) {
            mkdir($img_dir, 0777, true);
        }
        $img = time() . "_" . basename($_FILES['p_image']['name']);
        move_uploaded_file($_FILES['p_image']['tmp_name'], $img_dir . "/" . $img);
    }
    
    $query = "INSERT INTO product (p_name, pro_description, p_price, p_image, scat_id, ccat_id) 
              VALUES ('$name', '$desc', $price, '$img', $scat, $ccat)";
    
    mysqli_query($con, $query);
    echo "<script>location='view_product.php';</script>"; 
    exit;
}

include 'header.php';

$mains = mysqli_query($con, "SELECT * FROM main_cat ORDER BY mcat_name");

// UPDATED QUERY: Rating data fetch karne ke liye join aur group by add kiya gaya hai
$rows = mysqli_query($con, "SELECT p.*, s.sub_cat_name, m.mcat_name, c.ccat_name, 
                            AVG(r.rating_value) as avg_rating, 
                            COUNT(r.rating_id) as total_reviews
                            FROM product p
                            LEFT JOIN sub_cat s ON p.scat_id=s.sub_cat_id
                            LEFT JOIN main_cat m ON s.mcat_id=m.mcat_id
                            LEFT JOIN child_cat c ON p.ccat_id=c.ccat_id
                            LEFT JOIN product_ratings r ON p.p_id = r.p_id
                            GROUP BY p.p_id
                            ORDER BY p.p_id DESC");
?>

<style>
    .img-box { width: 50px; height: 50px; border-radius: 5px; border: 1px solid #ddd; object-fit: cover; background: #f8f8f8; }
    .no-img { width: 50px; height: 50px; background: #eee; display: flex; align-items: center; justify-content: center; border-radius: 5px; color: #aaa; }
    .rating-star { color: #f3d001; font-size: 12px; }
</style>

<div class="page-header">
    <div>
        <h1>Products</h1>
        <div class="crumb">Manage your product catalog</div>
    </div>
</div>

<div class="card mb-4">
    <div class="p-3">
        <h3 class="card-title">Add New Product</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Product Name</label>
                    <input type="text" name="p_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="p_price" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Image</label>
                    <input type="file" name="p_image" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4">
                    <label>Main Category</label>
                    <select id="mcat" class="form-control" required>
                        <option value="">Select</option>
                        <?php while($m=mysqli_fetch_assoc($mains)): ?>
                            <option value="<?= $m['mcat_id'] ?>"><?= $m['mcat_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Sub Category</label>
                    <select id="scat" name="scat_id" class="form-control" required>
                        <option value="">Select main first</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Child Category</label>
                    <select id="ccat" name="ccat_id" class="form-control">
                        <option value="">None</option>
                    </select>
                </div>
                <div class="col-12">
                    <label>Description</label>
                    <textarea name="pro_description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <button class="btn btn-primary mt-3" name="add_product"><i class="fas fa-plus"></i> Add Product</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Details</th>
                    <th>Rating</th> <th>Category</th>
                    <th>Price</th>
                    <th style="width:120px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if($rows && mysqli_num_rows($rows) > 0): while($r = mysqli_fetch_assoc($rows)): ?>
                <tr>
                    <td>
                        <?php if(!empty($r['p_image'])): ?>
                            <img src="<?= htmlspecialchars(product_image_web_url($r['p_image'])) ?>" class="img-box" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="no-img" style="display:none;"><i class="fas fa-image"></i></div>
                            <?php if(isset($_GET['debug']) && $_GET['debug'] == '1'): 
                                $dbg = product_image_debug_info($r['p_image']); ?>
                                <div style="margin-top:6px;font-size:11px;line-height:1.2;color:#6b7280;max-width:220px;word-break:break-word;">
                                    <div><strong>p_image:</strong> <?= htmlspecialchars($dbg['name']) ?></div>
                                    <div><strong>exists:</strong> shop=<?= $dbg['exists']['shop'] ? 'yes' : 'no' ?>, product=<?= $dbg['exists']['product'] ? 'yes' : 'no' ?>, assets_shop=<?= $dbg['exists']['assets_shop'] ? 'yes' : 'no' ?>, assets_product=<?= $dbg['exists']['assets_product'] ? 'yes' : 'no' ?>, admin=<?= $dbg['exists']['admin_img'] ? 'yes' : 'no' ?></div>
                                    <div><strong>url:</strong> <?= htmlspecialchars($dbg['url']) ?></div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="no-img"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($r['p_name']) ?></strong>
                        <div class="text-muted small"><?= mb_strimwidth($r['pro_description']??'', 0, 50, '...') ?></div>
                    </td>
                    <td>
                        <div class="rating-star">
                            <?php 
                            $avg = round($r['avg_rating']);
                            for($i=1; $i<=5; $i++){
                                echo ($i <= $avg) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                            }
                            ?>
                        </div>
                        <div class="small text-muted"><?= number_format($r['avg_rating'], 1) ?> (<?= $r['total_reviews'] ?> reviews)</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-primary"><?= $r['mcat_name'] ?? '-' ?></span>
                        <div class="small text-muted mt-1"><?= $r['sub_cat_name'] ?> <?= $r['ccat_name'] ? '› '.$r['ccat_name'] : '' ?></div>
                    </td>
                    <td><strong>$<?= number_format($r['p_price'], 2) ?></strong></td>
                    <td>
                        <a href="edit_product.php?ed_id=<?= $r['p_id'] ?>" class="btn btn-soft btn-icon"><i class="fas fa-pen"></i></a>
                        <a href="?del_id=<?= $r['p_id'] ?>" class="btn btn-danger btn-icon" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="6" class="text-center py-4">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Category dropdown ka JS wahi rahega jo aapke paas tha
document.getElementById('mcat').addEventListener('change',function(){
    fetch('fetch_categories.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'m_id='+this.value})
    .then(r=>r.text()).then(html=>{
        document.getElementById('scat').innerHTML='<option value="">Select</option>'+html;
        document.getElementById('ccat').innerHTML='<option value="">None</option>';
    });
});
document.getElementById('scat').addEventListener('change',function(){
    fetch('fetch_categories.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'s_id='+this.value})
    .then(r=>r.text()).then(html=>{
        document.getElementById('ccat').innerHTML='<option value="">None</option>'+html;
    });
});
</script>

<?php include 'footer.php'; ?>