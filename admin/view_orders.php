<?php
include 'config.php';

// 1. Update Status Logic
if (isset($_POST['update_status'])) {
    $oid = (int)$_POST['order_id'];
    $st = mysqli_real_escape_string($con, $_POST['status']);
    mysqli_query($con, "UPDATE orders SET status='$st' WHERE order_id=$oid");
    echo "<script>location='view_orders.php';</script>"; 
    exit;
}

// 2. Delete Order Logic
if (isset($_GET['del_id'])) {
    $id = (int)$_GET['del_id'];
    mysqli_query($con, "DELETE FROM order_details WHERE order_id=$id");
    mysqli_query($con, "DELETE FROM orders WHERE order_id=$id");
    echo "<script>location='view_orders.php';</script>"; 
    exit;
}

include 'header.php';

// 3. Fetch Main Orders
$rows = mysqli_query($con, "SELECT o.*, u.name, u.email,
    (SELECT COUNT(*) FROM order_details od WHERE od.order_id=o.order_id) items
    FROM orders o LEFT JOIN users u ON o.user_id=u.user_id
    ORDER BY o.order_id DESC");
?>

<div class="page-header">
    <div>
        <h1>Orders</h1>
        <div class="crumb">Manage customer orders & status</div>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th style="width:200px">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if($rows && mysqli_num_rows($rows) > 0): ?>
                <?php while($r = mysqli_fetch_assoc($rows)): 
                    $st = strtolower($r['status'] ?? 'pending');
                    $cls = ($st == 'paid' || $st == 'completed') ? 'green' : (($st == 'pending') ? 'orange' : (($st == 'cancelled') ? 'red' : 'blue'));
                ?>
                <tr>
                    <td><strong>#<?= $r['order_id'] ?></strong></td>
                    <td>
                        <strong><?= htmlspecialchars($r['name'] ?? 'Guest') ?></strong>
                        <div style="font-size:11.5px;color:var(--text-muted)"><?= htmlspecialchars($r['email'] ?? '') ?></div>
                    </td>
                    <td><span class="pill blue"><?= $r['items'] ?> items</span></td>
                    <td><strong>$<?= number_format($r['total_amount'], 2) ?></strong></td>
                    <td><span class="pill outline"><?= htmlspecialchars($r['payment_method'] ?? 'N/A') ?></span></td>
                    <td><span class="pill <?= $cls ?>"><?= ucfirst($st) ?></span></td>
                    <td>
                        <button class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#order<?= $r['order_id'] ?>"><i class="fas fa-eye"></i> View</button>
                        <a href="?del_id=<?= $r['order_id'] ?>" class="btn btn-danger btn-icon" onclick="return confirm('Delete order?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>

                <div class="modal fade" id="order<?= $r['order_id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Order #<?= $r['order_id'] ?> Details</h5>
                                <div class="ms-auto me-3"><span class="pill outline">Payment: <?= htmlspecialchars($r['payment_method'] ?? 'N/A') ?></span></div>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form method="post" class="d-flex gap-2 mb-3">
                                    <input type="hidden" name="order_id" value="<?= $r['order_id'] ?>">
                                    <select name="status" class="form-control" style="max-width:200px">
                                        <?php foreach(['pending','paid','completed','cancelled'] as $s): ?>
                                            <option value="<?= $s ?>" <?= ($s == $st) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-primary" name="update_status">Update Status</button>
                                </form>

                                <table class="data">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $oid = $r['order_id'];
                                    $det = mysqli_query($con, "SELECT od.*, p.p_name FROM order_details od LEFT JOIN product p ON od.p_id = p.p_id WHERE od.order_id = $oid");
                                    
                                    if ($det && mysqli_num_rows($det) > 0):
                                        while($d = mysqli_fetch_assoc($det)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($d['p_name'] ?? 'Unknown Product') ?></td>
                                            <td><?= $d['quantity'] ?></td>
                                            <td>$<?= number_format($d['price'] ?? 0, 2) ?></td>
                                            <td><strong>$<?= number_format(($d['price'] ?? 0) * $d['quantity'], 2) ?></strong></td>
                                        </tr>
                                        <?php endwhile; 
                                    else: ?>
                                        <tr><td colspan="4" class="text-center">No details found for this order.</td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No orders found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>