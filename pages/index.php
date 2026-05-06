<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

// Function to determine shade group based on product name or category
function get_shade_group($product_name) {
    $name = strtolower($product_name);
    if (strpos($name, 'highlighter') !== false) return 'Highlighter';
    if (strpos($name, 'lipstick') !== false) return 'Lipstick';
    if (strpos($name, 'lip tint') !== false) return 'Lip Tint';
    if (strpos($name, 'foundation') !== false || strpos($name, 'concealer') !== false || strpos($name, 'base makeup') !== false) return 'Base Makeup';
    if (strpos($name, 'blush') !== false) return 'Blush 1';
    return null;
}

function slug_class($name) {
    $name = strtolower((string)$name);
    $name = preg_replace('/[^a-z0-9]+/','-', $name);
    $name = trim($name, '-');
    if ($name === '') $name = 'uncategorized';
    // CSS class should not start with a digit for some selectors
    if (preg_match('/^[0-9]/', $name)) $name = 'c-' . $name;
    return $name;
}
?>

<section class="categories">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 p-0">
                <div class="categories__item categories__large__item set-bg" data-setbg="../assets/images/womens_fashion_banner_transparent.png" style="background-color: #f4f4f4; background-size: contain; background-repeat: no-repeat; background-position: right center;">
                    <div class="categories__text">
                        <h1>Women’s fashion</h1>
                        <p>From sparkling jewels to flawless finishes,<br> elevate your everyday look with pieces that<br> speak for themselves.</p>

                        <a href="../pages/shop.php">Shop now</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <?php
                    // Fetch counts dynamically
                    $makeup_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM product WHERE scat_id = 11"))['total'] ?? 0;
                    $jewellery_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM product WHERE scat_id IN (SELECT sub_cat_id FROM sub_cat WHERE mcat_id = 3)"))['total'] ?? 0;
                    $skincare_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM product WHERE scat_id = 10"))['total'] ?? 0;
                    $accessories_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM product WHERE scat_id = 14"))['total'] ?? 0;
                    ?>
                    <div class="col-lg-6 col-md-6 col-sm-6 p-0">
                        <div class="categories__item set-bg" data-setbg="../assets/images/mkbanner-removebg-preview.png"
                            style="background-color: #f4f4f4; background-size: contain; background-repeat: no-repeat; background-position: right center;">
                            <div class="categories__text">
                                <h4>Makeup</h4>
                                <p><?php echo $makeup_count; ?> items</p>
                                <a href="../pages/shop.php?scat=11">Shop now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 p-0">
                        <div class="categories__item set-bg" data-setbg="../assets/images/jew-removebg-preview.png" style="background-color: #f4f4f4; 
                background-size: contain; 
                background-repeat: no-repeat; 
                background-position: 155% center;">
                            <div class="categories__text">
                                <h4>Jewellery</h4>
                                <p><?php echo $jewellery_count; ?> items</p>
                                <a href="../pages/shop.php?mcat=3">Shop now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 p-0">
                        <div class="categories__item set-bg" data-setbg="../assets/images/categories/category-4.jpg">
                            <div class="categories__text">
                                <h4>Skin Care</h4>
                                <p><?php echo $skincare_count; ?> items</p>
                                <a href="../pages/shop.php?scat=10">Shop now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 p-0">
                        <div class="categories__item set-bg" data-setbg="../assets/images/categories/category-5.jpg">
                            <div class="categories__text">
                                <h4>Accessories</h4>
                                <p><?php echo $accessories_count; ?> items</p>
                                <a href="../pages/shop.php?scat=14">Shop now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <div class="section-title">
                    <h4>New product</h4>
                </div>
            </div>
            <div class="col-lg-8 col-md-8">
                <ul class="filter__controls">
                    <li class="active" data-filter=".latest-12">All</li>
                    <?php
                    // Display filters only for categories that are actually in the products list
                    $displayed_cats = [];
                    $cat_query = "SELECT s.sub_cat_name FROM product p LEFT JOIN sub_cat s ON p.scat_id = s.sub_cat_id ORDER BY p.p_id DESC";
                    $cat_res = mysqli_query($conn, $cat_query);
                    if ($cat_res) {
                        while ($cat_row = mysqli_fetch_assoc($cat_res)) {
                            if (!empty($cat_row['sub_cat_name'])) {
                                $displayed_cats[] = $cat_row['sub_cat_name'];
                            }
                        }
                    }
                    $displayed_cats = array_unique($displayed_cats);
                    $displayed_cats = array_slice($displayed_cats, 0, 5); // Sirf top 5 categories rakhne ke liye
                    
                    foreach ($displayed_cats as $label) {
                        $filter_name = slug_class($label);
                        echo "<li data-filter='." . htmlspecialchars($filter_name) . "'>" . htmlspecialchars($label) . "</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="row property__gallery">
            <?php
            $prod_query = "SELECT p.*, s.sub_cat_name
                           FROM product p 
                           LEFT JOIN sub_cat s ON p.scat_id = s.sub_cat_id 
                           GROUP BY p.p_id
                           ORDER BY p.p_id DESC
                           LIMIT 8";
            
            $prod_res = mysqli_query($conn, $prod_query);
            $product_counter = 0;
            
            if(mysqli_num_rows($prod_res) > 0) {
                while ($prod_row = mysqli_fetch_assoc($prod_res)) {
                    $product_counter++;
                    $p_id = $prod_row['p_id'];
                    $cat_class = slug_class($prod_row['sub_cat_name'] ?? '');
                    
                    // Pehle 12 products ko latest-12 class do
                    if ($product_counter <= 12) {
                        $cat_class .= ' latest-12';
                    }
                    $img_path = product_image_url($prod_row['p_image']); 
                    
                    $shade_group = get_shade_group($prod_row['p_name']);

                    // Heart icon logic (Empty vs Filled)
                    $wishlist_class = "icon_heart_alt";
                    $wishlist_style = "";
                    if (isset($_SESSION['user_id'])) {
                        $u_id = $_SESSION['user_id'];
                        $check_wish = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_id = '$u_id' AND p_id = '$p_id'");
                        if (mysqli_num_rows($check_wish) > 0) {
                            $wishlist_class = "icon_heart"; 
                            $wishlist_style = "color: #ca1515;";
                        }
                    }
                    ?>
                    
                    <div class="col-lg-3 col-md-4 col-sm-6 mix <?php echo $cat_class; ?>">
                        <div class="product__item">
                            <div class="product__item__pic set-bg" data-setbg="<?php echo $img_path; ?>" style="background-image: url('<?php echo $img_path; ?>');">
                                <ul class="product__hover">
                                    <li><a href="<?php echo $img_path; ?>" class="image-popup"><span class="arrow_expand"></span></a></li>
                                    
                                    <li>
                                        <a href="javascript:void(0);" class="wishlist-btn" data-id="<?php echo $p_id; ?>">
                                            <span class="<?php echo $wishlist_class; ?>" style="<?php echo $wishlist_style; ?>"></span>
                                        </a>
                                    </li>

                                    <li>
                                        <?php if ($shade_group) { ?>
                                            <a href="#" data-toggle="modal" data-target="#shadeModal<?php echo $p_id; ?>">
                                                <span class="icon_bag_alt"></span>
                                            </a>
                                        <?php } else { ?>
                                            <a href="../actions/add_to_cart.php?p_id=<?php echo $p_id; ?>">
                                                <span class="icon_bag_alt"></span>
                                            </a>
                                        <?php } ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="product__item__text">
                                <h6><a href="../pages/product-details.php?id=<?php echo $p_id; ?>"><?php echo $prod_row['p_name']; ?></a></h6>
                                <div class="rating">
                                    <?php
                                    $rating_q = mysqli_query($conn, "SELECT AVG(rating_value) as avg_r FROM product_ratings WHERE p_id = '$p_id'");
                                    $r_row = mysqli_fetch_assoc($rating_q);
                                    $avg = round($r_row['avg_r'] ?? 5);
                                    for($i=1; $i<=5; $i++) {
                                        echo ($i <= $avg) ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-o"></i>';
                                    }
                                    ?>
                                </div>
                                <div class="product__price">Rs. <?php echo number_format($prod_row['p_price']); ?></div>
                                <a href="../actions/add_to_cart.php?p_id=<?php echo $p_id; ?>" class="site-btn" style="padding: 8px 16px; font-size: 12px; height: auto; line-height: normal; margin-top: 10px; width: 100%; text-align: center;">Add to Cart</a>
                            </div>
                        </div>
                    </div>

                    <?php if ($shade_group) { ?>
                    <div class="modal fade" id="shadeModal<?php echo $p_id; ?>" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 9999;">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <form action="../actions/add_to_cart.php" method="GET">
                                    <input type="hidden" name="p_id" value="<?php echo $p_id; ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Select Shade</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <?php 
                                            $shade_res = mysqli_query($conn, "SELECT * FROM shade_cards WHERE group_name = '$shade_group'");
                                            while($shade = mysqli_fetch_assoc($shade_res)) { ?>
                                                <div class="col-4 text-center mb-3">
                                                    <label style="cursor: pointer;">
                                                        <input type="radio" name="scard_id" value="<?php echo $shade['scard_id']; ?>" required>
                                                        <div style="width:35px; height:35px; background:<?php echo $shade['shade_color_code']; ?>; border-radius:50%; margin:5px auto; border: 1px solid #ddd;"></div>
                                                        <span style="font-size: 12px; display: block;"><?php echo $shade['shade_name']; ?></span>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-danger" style="background-color: #ca1515; border: none; width: 100%;">Add to Cart</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

            <?php 
                }
            }
            ?>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center mt-4">
                <a href="../pages/shop.php" class="btn btn-dark btn-lg rounded-pill" style="padding: 10px 30px; font-weight: bold; background: #ca1515; border: none; color: white;">
                    View All Products <i class="fa fa-angle-double-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>


<!-- Banner Section Begin -->

<?php
function render_small_product_item($conn, $query) {
    $res = mysqli_query($conn, $query);
    while($r = mysqli_fetch_assoc($res)) {
        $img = product_image_url($r['p_image']);
        $p_id = $r['p_id'];
        $name = htmlspecialchars($r['p_name']);
        $price = number_format($r['p_price'], 2);
        
        $rating_q = mysqli_query($conn, "SELECT AVG(rating_value) as avg_r FROM product_ratings WHERE p_id = '$p_id'");
        $r_row = mysqli_fetch_assoc($rating_q);
        $avg = round($r_row['avg_r'] ?? 5);
        
        echo '<div class="trend__item">';
        echo '<div class="trend__item__pic" style="width: 90px; height: 90px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f8f8f8;">';
        echo '<img src="'.$img.'" alt="" style="width: 100%; height: 100%; object-fit: cover;">';
        echo '</div>';
        echo '<div class="trend__item__text">';
        echo '<h6><a href="../pages/product-details.php?id='.$p_id.'" style="color:#111;">'.mb_strimwidth($name,0,25,'...').'</a></h6>';
        echo '<div class="rating">';
        for($i=1; $i<=5; $i++) echo ($i <= $avg) ? '<i class="fa fa-star" style="color: #f3d001;"></i>' : '<i class="fa fa-star-o" style="color: #f3d001;"></i>';
        echo '</div>';
        echo '<div class="product__price">$ '.$price.'</div>';
        echo '</div>';
        echo '</div>';
    }
}
?>
<!-- Trend Section Begin -->
<section class="trend spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="trend__content">
                    <div class="section-title">
                        <h4>Hot Trend</h4>
                    </div>
                    <?php render_small_product_item($conn, "SELECT * FROM product GROUP BY p_name ORDER BY p_id DESC LIMIT 3"); ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="trend__content">
                    <div class="section-title">
                        <h4>Best seller</h4>
                    </div>
                    <?php render_small_product_item($conn, "SELECT p.*, AVG(r.rating_value) as avg_rating FROM product p LEFT JOIN product_ratings r ON p.p_id = r.p_id GROUP BY p.p_name ORDER BY avg_rating DESC, p.p_id DESC LIMIT 3"); ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="trend__content">
                    <div class="section-title">
                        <h4>Feature</h4>
                    </div>
                    <?php render_small_product_item($conn, "SELECT * FROM product GROUP BY p_name ORDER BY RAND() LIMIT 3"); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Trend Section End -->

<!-- Discount Section Begin -->
<?php
$disc_q = mysqli_query($conn, "SELECT * FROM product ORDER BY RAND() LIMIT 1");
$disc_p = mysqli_fetch_assoc($disc_q);
$disc_img = $disc_p ? product_image_url($disc_p['p_image']) : "../assets/images/discount.jpg";
$disc_id = $disc_p ? $disc_p['p_id'] : 0;
?>
<section class="discount">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 p-0">
                <div class="discount__pic" style="height: 100%; min-height: 400px; background-image: url('<?= htmlspecialchars($disc_img) ?>'); background-size: cover; background-position: center;">
                </div>
            </div>
            <div class="col-lg-6 p-0">
                <div class="discount__text">
                    <div class="discount__text__title">
                        <span>Discount</span>
                        <h2>Summer Sale</h2>
                        <h5><span>Sale</span> 50%</h5>
                    </div>
                    <div class="discount__countdown" id="countdown-time">
                        <div class="countdown__item">
                            <span>22</span>
                            <p>Days</p>
                        </div>
                        <div class="countdown__item">
                            <span>18</span>
                            <p>Hour</p>
                        </div>
                        <div class="countdown__item">
                            <span>46</span>
                            <p>Min</p>
                        </div>
                        <div class="countdown__item">
                            <span>05</span>
                            <p>Sec</p>
                        </div>
                    </div>
                    <a href="../pages/product-details.php?id=<?= $disc_id ?>">Shop now</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Discount Section End -->

<!-- Services Section Begin -->
<section class="services spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-car"></i>
                    <h6>Free Shipping</h6>
                    <p>For all oder over $99</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-money"></i>
                    <h6>Money Back Guarantee</h6>
                    <p>If good have Problems</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-support"></i>
                    <h6>Online Support 24/7</h6>
                    <p>Dedicated support</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="services__item">
                    <i class="fa fa-headphones"></i>
                    <h6>Payment Secure</h6>
                    <p>100% secure payment</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Services Section End -->

<!-- Instagram Begin -->
<div class="instagram">
    <div class="container-fluid">
        <div class="row">
            <?php
            $insta_query = "SELECT p_image FROM product ORDER BY RAND() LIMIT 6";
            $insta_res = mysqli_query($conn, $insta_query);
            while($insta_row = mysqli_fetch_assoc($insta_res)) {
                $insta_img = product_image_url($insta_row['p_image']);
            ?>
            <div class="col-lg-2 col-md-4 col-sm-4 p-0">
                <div class="instagram__item set-bg" data-setbg="<?= htmlspecialchars($insta_img) ?>">
                    <div class="instagram__text">
                        <i class="fa fa-instagram"></i>
                        <a href="https://www.instagram.com/" target="_blank">@ address_book</a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<!-- Instagram End -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>