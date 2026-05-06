<?php 
require_once __DIR__ . '/../includes/header.php';
?>
    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="./index.html"><i class="fa fa-home"></i> Home</a>
                        <span>Shopping cart</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Shop Cart Section Begin -->
    <section class="shop-cart spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <form action="../actions/update_cart.php" method="POST">
                    <div class="shop__cart__table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Shade</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $user_id = $_SESSION['user_id'] ?? null;
                                $subtotal = 0;
                                
                                // Fetch function to dry up the code inside the loop
                                function render_cart_item($row, &$subtotal) {
                                    $total_price = $row['p_price'] * $row['quantity'];
                                    $subtotal += $total_price;
                                    $img_path = product_image_url($row['p_image']);
                                    ?>
                                    <tr>
                                        <td class="cart__product__item">
                                            <img src="<?php echo $img_path; ?>" alt="" style="width: 100px;">
                                            <div class="cart__product__item__title">
                                                <h6><?php echo $row['p_name']; ?></h6>
                                                <div class="rating">
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if(isset($row['shade_name']) && $row['shade_name']){ ?>
                                                <div style="font-size: 14px; color: #111;">
                                                    <span style="display:inline-block; width:15px; height:15px; border-radius:50%; background-color:<?php echo $row['shade_color_code']; ?>; border:1px solid #ccc; margin-right:5px; vertical-align: middle;"></span>
                                                    <span style="vertical-align: middle;"><?php echo $row['shade_name']; ?></span>
                                                </div>
                                            <?php } else { ?>
                                                <span class="text-muted">-</span>
                                            <?php } ?>
                                        </td>
                                        <td class="cart__price">Rs. <?php echo number_format($row['p_price']); ?></td>
                                        <td class="cart__quantity">
                                            <div class="pro-qty">
                                                <input type="number" name="qty[<?php echo $row['cart_item_id']; ?>]" value="<?php echo $row['quantity']; ?>" min="1">
                                            </div>
                                        </td>
                                        <td class="cart__total">Rs. <?php echo number_format($total_price); ?></td>
                                        <td class="cart__close">
                                            <a href="../actions/remove_from_cart.php?id=<?php echo $row['cart_item_id']; ?>">
                                                <span class="icon_close"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }

                                if ($user_id) {
                                    $cart_query = "SELECT ci.*, p.p_name, p.p_price, p.p_image, s.shade_name, s.shade_color_code 
                                                   FROM cart_items ci 
                                                   JOIN cart c ON ci.cart_id = c.cart_id 
                                                   JOIN product p ON ci.p_id = p.p_id 
                                                   LEFT JOIN shade_cards s ON ci.scard_id = s.scard_id
                                                   WHERE c.user_id = '$user_id'";
                                    $cart_res = mysqli_query($conn, $cart_query);
                                    
                                    if (mysqli_num_rows($cart_res) > 0) {
                                        while ($row = mysqli_fetch_assoc($cart_res)) {
                                            render_cart_item($row, $subtotal);
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>Your cart is empty!</td></tr>";
                                    }
                                } else {
                                    if (isset($_SESSION['guest_cart']) && count($_SESSION['guest_cart']) > 0) {
                                        foreach ($_SESSION['guest_cart'] as $item) {
                                            $p_id = mysqli_real_escape_string($conn, $item['p_id']);
                                            $scard_id = $item['scard_id'] != 'NULL' ? mysqli_real_escape_string($conn, $item['scard_id']) : 'NULL';
                                            
                                            $prod_query = "SELECT p.p_name, p.p_price, p.p_image";
                                            if ($scard_id != 'NULL') {
                                                $prod_query .= ", s.shade_name, s.shade_color_code FROM product p LEFT JOIN shade_cards s ON s.scard_id = '$scard_id' WHERE p.p_id = '$p_id'";
                                            } else {
                                                $prod_query .= ", NULL as shade_name, NULL as shade_color_code FROM product p WHERE p.p_id = '$p_id'";
                                            }
                                            
                                            $prod_res = mysqli_query($conn, $prod_query);
                                            if ($prod_row = mysqli_fetch_assoc($prod_res)) {
                                                $row = array_merge($item, $prod_row);
                                                render_cart_item($row, $subtotal);
                                            }
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>Your cart is empty!</td></tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="cart__btn">
                        <a href="../pages/index.php">Continue Shopping</a>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="cart__btn update__btn">
                        <button type="submit" name="update_cart" style="background: none; border: none; display: inline-block; font-size: 14px; color: #111111; font-weight: 700; text-transform: uppercase;">
                            <span class="icon_loading"></span> Update cart
                        </button>
                    </div>
                </div>
                </form>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="discount__content">
                        <h6>Discount codes</h6>
                        <form action="#">
                            <input type="text" placeholder="Enter your coupon code">
                            <button type="submit" class="site-btn">Apply</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4 offset-lg-2">
                    <div class="cart__total__procced">
                        <h6>Cart total</h6>
                        <ul>
                            <li>Subtotal <span>Rs. <?php echo number_format($subtotal); ?></span></li>
                            <li>Total <span>Rs. <?php echo number_format($subtotal); ?></span></li>
                        </ul>
                        <a href="../pages/checkout.php" class="primary-btn">Proceed to checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Shop Cart Section End -->

    <!-- Instagram Begin -->
    <div class="instagram">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                    <div class="instagram__item set-bg" data-setbg="../assets/images/instagram/insta-1.jpg">
                        <div class="instagram__text">
                            <i class="fa fa-instagram"></i>
                            <a href="#">@ address_book</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                    <div class="instagram__item set-bg" data-setbg="../assets/images/instagram/insta-2.jpg">
                        <div class="instagram__text">
                            <i class="fa fa-instagram"></i>
                            <a href="#">@ address_book</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                    <div class="instagram__item set-bg" data-setbg="../assets/images/instagram/insta-3.jpg">
                        <div class="instagram__text">
                            <i class="fa fa-instagram"></i>
                            <a href="#">@ address_book</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                    <div class="instagram__item set-bg" data-setbg="../assets/images/instagram/insta-4.jpg">
                        <div class="instagram__text">
                            <i class="fa fa-instagram"></i>
                            <a href="#">@ address_book</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                    <div class="instagram__item set-bg" data-setbg="../assets/images/instagram/insta-5.jpg">
                        <div class="instagram__text">
                            <i class="fa fa-instagram"></i>
                            <a href="#">@ address_book</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                    <div class="instagram__item set-bg" data-setbg="../assets/images/instagram/insta-6.jpg">
                        <div class="instagram__text">
                            <i class="fa fa-instagram"></i>
                            <a href="#">@ address_book</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Instagram End -->
    <?php 
require_once __DIR__ . '/../includes/footer.php';
?>