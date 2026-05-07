<?php
require_once __DIR__ . '/../includes/header.php';

if (!isset($_GET['order_id'])) {
    header("Location: ../pages/index.php");
    exit();
}

$order_id = $_GET['order_id'];
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="../pages/index.php"><i class="fa fa-home"></i> Home</a>
                    <span>Order Success</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="shop-cart spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="order-success-content" style="padding: 100px 0;">
                    <i class="fa fa-check-circle" style="font-size: 80px; color: #ca1515; margin-bottom: 30px;"></i>
                    <h2 style="font-weight: 700; margin-bottom: 10px;">Thank You for Your Order!</h2>
                    <p style="font-size: 18px; color: #666; margin-bottom: 40px;">Your order <strong>#<?php echo $order_id; ?></strong> has been successfully placed.</p>
                    <div class="cart__btn">
                        <a href="../pages/index.php" class="site-btn">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
