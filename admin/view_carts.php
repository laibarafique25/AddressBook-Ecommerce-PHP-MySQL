<?php
include 'config.php';
if (isset($_GET['del_id'])) {
    $id=(int)$_GET['del_id'];
    mysqli_query($con,"DELETE FROM cart_items WHERE cart_id=$id");
    mysqli_query($con,"DELETE FROM cart WHERE cart_id=$id");
    echo "<script>location='view_carts.php';</script>"; exit;
}
include 'header.php';
$rows=mysqli_query($con,"SELECT c.*, u.name, u.email,
                          (SELECT COUNT(*) FROM cart_items ci WHERE ci.cart_id=c.cart_id) items
                          FROM cart c LEFT JOIN users u ON c.user_id=u.user_id ORDER BY c.cart_id DESC");
?>
<div class="page-header"><div><h1>Active Carts</h1><div class="crumb">Customer cart sessions</div></div></div>

<div class="card">
  <div class="table-wrap">
    <table class="data">
      <thead><tr><th>Cart</th><th>Customer</th><th>Items</th><th style="width:200px">Actions</th></tr></thead>
      <tbody>
      <?php if($rows && mysqli_num_rows($rows)>0): while($r=mysqli_fetch_assoc($rows)): ?>
        <tr>
          <td><strong>#<?= $r['cart_id'] ?></strong></td>
          <td><strong><?= htmlspecialchars($r['name']??'Guest') ?></strong>
            <div style="font-size:11.5px;color:var(--text-muted)"><?= htmlspecialchars($r['email']??'') ?></div></td>
          <td><span class="pill purple"><?= $r['items'] ?> items</span></td>
          <td>
            <button class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#cart<?= $r['cart_id'] ?>"><i class="fas fa-eye"></i> View</button>
            <a href="?del_id=<?= $r['cart_id'] ?>" class="btn btn-danger btn-icon" onclick="return confirm('Delete cart?')"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
        <div class="modal fade" id="cart<?= $r['cart_id'] ?>" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header"><h5 class="modal-title">Cart #<?= $r['cart_id'] ?></h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
              <div class="modal-body">
                <table class="data">
                  <thead><tr><th>Product</th><th>Shade</th><th>Qty</th></tr></thead>
                  <tbody>
                  <?php $it=mysqli_query($con,"SELECT ci.*, p.p_name, s.shade_name, s.shade_color_code
                    FROM cart_items ci LEFT JOIN product p ON ci.p_id=p.p_id
                    LEFT JOIN shade_cards s ON ci.scard_id=s.scard_id WHERE ci.cart_id={$r['cart_id']}");
                    while($i=mysqli_fetch_assoc($it)): ?>
                    <tr>
                      <td><?= htmlspecialchars($i['p_name']??'-') ?></td>
                      <td><?php if($i['shade_color_code']): ?><span class="color-swatch" style="background:<?= htmlspecialchars($i['shade_color_code']) ?>;width:18px;height:18px;vertical-align:middle"></span> <?= htmlspecialchars($i['shade_name']??'') ?><?php else: echo '-'; endif; ?></td>
                      <td><?= $i['quantity'] ?></td>
                    </tr>
                  <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; else: ?>
        <tr><td colspan="4" class="text-center text-muted py-4">No active carts</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include 'footer.php'; ?>
