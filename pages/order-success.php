<?php
// =============================================
// order-success.php — Order Confirmation Page
// =============================================

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../includes/header.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$uid      = (int)$_SESSION['user_id'];

// Fetch order
$order_res = mysqli_query($conn, "
    SELECT * FROM orders WHERE order_id = $order_id AND user_id = $uid
");
$order = mysqli_fetch_assoc($order_res);

if (!$order) {
    header("Location: ../pages/index.php');
    exit;
}

// Fetch order details
$details_res = mysqli_query($conn, "
    SELECT od.*, p.p_name, p.p_image, sc.shade_name, sc.shade_color_code
    FROM order_details od
    JOIN product p ON od.p_id = p.p_id
    LEFT JOIN shade_cards sc ON od.scard_id = sc.scard_id
    WHERE od.order_id = $order_id
");
$details = mysqli_fetch_all($details_res, MYSQLI_ASSOC);
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <!-- Success Banner -->
                <div class="text-center mb-4">
                    <div class="success-checkmark mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:5rem;"></i>
                    </div>
                    <h2 class="fw-bold">Thank You for Your Order!</h2>
                    <p class="text-muted">Order <strong>#<?= $order_id ?></strong> has been placed successfully.</p>
                </div>

                <!-- Order Details Card -->
                <div class="glass-card p-4 mb-4">
                    <h5 class="fw-bold mb-3">Order Summary</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($details as $d): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="assets/images/products/<?= htmlspecialchars($d['p_image']) ?>"
                                             class="cart-thumb rounded" onerror="this.src='assets/images/no-image.png'">
                                        <div>
                                            <?= htmlspecialchars($d['p_name']) ?>
                                            <?php if ($d['shade_name']): ?>
                                            <br><small class="text-muted d-flex align-items-center gap-1">
                                                <span class="shade-dot" style="background:<?= htmlspecialchars($d['shade_color_code']) ?>"></span>
                                                <?= htmlspecialchars($d['shade_name']) ?>
                                            </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $d['quantity'] ?></td>
                                <td>Rs. <?= number_format($d['price'] * $d['quantity'], 0) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="2" class="text-end">Total:</td>
                                    <td class="text-danger">Rs. <?= number_format($order['total_amount'], 0) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <span class="badge bg-warning text-dark"><?= $order['status'] ?></span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex gap-3 justify-content-center">
                    <a href="../pages/index.php" class="btn btn-danger px-4">Continue Shopping</a>
                    <a href="orders.php" class="btn btn-outline-secondary px-4">My Orders</a>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
