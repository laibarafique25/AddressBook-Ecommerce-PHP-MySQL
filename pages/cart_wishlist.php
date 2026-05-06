<?php 
require_once __DIR__ . '/../includes/header.php'; 
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="../pages/index.php"><i class="fa fa-home"></i> Home</a>
                    <span>Wishlist</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="shop-cart spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h4>My Liked Products</h4>
                </div>
                <div class="shop__cart__table">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Stock Status</th>
                                <th>Action</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $user_id = $_SESSION['user_id'] ?? null;
                            
                            function render_wishlist_item($row) {
                                $img_path = product_image_url($row['p_image']);
                                ?>
                                <tr>
                                    <td class="cart__product__item">
                                        <img src="<?php echo $img_path; ?>" alt="" style="width: 90px;">
                                        <div class="cart__product__item__title">
                                            <h6><?php echo $row['p_name']; ?></h6>
                                        </div>
                                    </td>
                                    <td class="cart__price">Rs. <?php echo number_format($row['p_price']); ?></td>
                                    <td class="cart__total" style="color: green;">Available</td>
                                    <td>
                                        <a href="../actions/add_to_cart.php?p_id=<?php echo $row['p_id']; ?>" class="primary-btn" style="padding: 10px 18px; font-size: 12px;">Add to Cart</a>
                                    </td>
                                    <td class="cart__close">
                                        <a href="../actions/wishlist_handler_simple.php?del_id=<?php echo $row['p_id']; ?>">
                                            <span class="icon_close"></span>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }

                            if ($user_id) {
                                // Wishlist aur Product table ko join kar ke data lana
                                $wish_query = "SELECT w.wishlist_id, p.p_id, p.p_name, p.p_price, p.p_image 
                                               FROM wishlist w 
                                               JOIN product p ON w.p_id = p.p_id 
                                               WHERE w.user_id = '$user_id'";
                                $wish_res = mysqli_query($conn, $wish_query);
                                
                                if (mysqli_num_rows($wish_res) > 0) {
                                    while ($row = mysqli_fetch_assoc($wish_res)) {
                                        render_wishlist_item($row);
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>You haven't liked any products yet!</td></tr>";
                                }
                            } else {
                                if (isset($_SESSION['guest_wishlist']) && count($_SESSION['guest_wishlist']) > 0) {
                                    foreach ($_SESSION['guest_wishlist'] as $p_id) {
                                        $p_id = mysqli_real_escape_string($conn, $p_id);
                                        $prod_query = "SELECT p_id, p_name, p_price, p_image FROM product WHERE p_id = '$p_id'";
                                        $prod_res = mysqli_query($conn, $prod_query);
                                        if ($row = mysqli_fetch_assoc($prod_res)) {
                                            render_wishlist_item($row);
                                        }
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>You haven't liked any products yet!</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>