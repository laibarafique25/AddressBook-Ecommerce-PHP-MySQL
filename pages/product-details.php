<?php
require_once __DIR__ . '/../includes/header.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='../pages/index.php';</script>";
    exit();
}

$p_id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT p.*, s.sub_cat_name FROM product p LEFT JOIN sub_cat s ON p.scat_id = s.sub_cat_id WHERE p.p_id = '$p_id'";
$res = mysqli_query($conn, $query);

if (mysqli_num_rows($res) == 0) {
    echo "<script>window.location.href='../pages/index.php';</script>";
    exit();
}

$product = mysqli_fetch_assoc($res);
$img_path = product_image_url($product['p_image']);

if (!function_exists('get_shade_group')) {
    function get_shade_group($product_name) {
        $name = strtolower($product_name);
        if (strpos($name, 'highlighter') !== false) return 'Highlighter';
        if (strpos($name, 'lipstick') !== false) return 'Lipstick';
        if (strpos($name, 'lip tint') !== false) return 'Lip Tint';
        if (strpos($name, 'foundation') !== false || strpos($name, 'concealer') !== false || strpos($name, 'conceler') !== false || strpos($name, 'base makeup') !== false) return 'Base Makeup';
        if (strpos($name, 'blush') !== false) return 'Blush 1';
        return null;
    }
}

$shade_group = get_shade_group($product['p_name']);
$has_shades = false;
$shades_res = null;
if ($shade_group) {
    $shades_res = mysqli_query($conn, "SELECT * FROM shade_cards WHERE group_name = '$shade_group'");
    if ($shades_res && mysqli_num_rows($shades_res) > 0) {
        $has_shades = true;
    }
}

// Fetch average rating
$rating_query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM product_reviews WHERE p_id = '$p_id'";
$rating_res = mysqli_query($conn, $rating_query);
$rating_data = mysqli_fetch_assoc($rating_res);
$avg_rating = round($rating_data['avg_rating'], 1);
$total_reviews = $rating_data['total_reviews'];

// User's own rating if logged in
$user_rating = 0;
if (isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    $user_rating_q = "SELECT rating FROM product_reviews WHERE p_id = '$p_id' AND user_id = '$u_id'";
    $user_rating_res = mysqli_query($conn, $user_rating_q);
    if ($user_rating_row = mysqli_fetch_assoc($user_rating_res)) {
        $user_rating = $user_rating_row['rating'];
    }
}
?>

<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="../pages/index.php"><i class="fa fa-home"></i> Home</a>
                    <?php if($product['sub_cat_name']) { ?>
                    <a href="../pages/shop.php?scat=<?php echo $product['scat_id']; ?>"><?php echo $product['sub_cat_name']; ?></a>
                    <?php } ?>
                    <span><?php echo $product['p_name']; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Product Details Section Begin -->
<section class="product-details spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="product__details__pic">
                    <div class="product__details__slider__content">
                        <div class="product__details__pic__slider owl-carousel">
                            <img data-hash="product-1" class="product__big__img" src="<?php echo $img_path; ?>" alt="" style="height: 500px; object-fit: cover; border-radius: 10px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="product__details__text">
                    <h3><?php echo $product['p_name']; ?></h3>
                    <div class="rating">
                        <?php 
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= floor($avg_rating)) {
                                echo '<i class="fa fa-star"></i>';
                            } elseif ($i - 0.5 <= $avg_rating) {
                                echo '<i class="fa fa-star-half-o"></i>';
                            } else {
                                echo '<i class="fa fa-star-o"></i>';
                            }
                        }
                        ?>
                        <span>( <?php echo $total_reviews; ?> Reviews )</span>
                    </div>
                    <div class="product__details__price">Rs. <?php echo number_format($product['p_price']); ?></div>
                    <p style="margin-bottom: 25px;"><?php echo nl2br($product['pro_description']); ?></p>

                    <div class="product__details__widget" style="padding-top: 10px; border-top: none; margin-bottom: 20px;">
                        <h6 style="font-weight: 600; margin-bottom: 10px;">Your Rating:</h6>
                        <form action="../actions/rate_product.php" method="POST" id="ratingForm">
                            <input type="hidden" name="p_id" value="<?php echo $p_id; ?>">
                            <div class="rating-input">
                                <?php for($i=5; $i>=1; $i--): ?>
                                    <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo ($user_rating == $i) ? 'checked' : ''; ?> onchange="this.form.submit()">
                                    <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> stars"></label>
                                <?php endfor; ?>
                            </div>
                            <?php if(!isset($_SESSION['user_id'])): ?>
                                <small class="text-muted"><a href="../auth/login.php" class="text-danger">Login</a> to rate this product</small>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <form action="../actions/add_to_cart.php" method="GET">
                        <input type="hidden" name="p_id" value="<?php echo $product['p_id']; ?>">
                        
                        <div class="product__details__button" style="margin-bottom: 10px;">
                            <div class="quantity">
                                <span>Quantity:</span>
                                <div class="pro-qty">
                                    <input type="text" name="quantity" value="1" readonly>
                                </div>
                            </div>
                        </div>

                        <?php if($has_shades) { ?>
                        <div class="product__details__widget" style="padding-top: 15px; border-top: 1px solid #ebebeb;">
                            <ul style="margin-bottom: 0;">
                                <li>
                                    <span style="display: block; font-weight: 600; color: #111; margin-bottom: 15px;">Available Shades:</span>
                                    <div class="color__checkbox" style="display: flex; flex-wrap: wrap; gap: 15px;">
                                        <?php while($shade = mysqli_fetch_assoc($shades_res)) { ?>
                                        <label for="shade_<?php echo $shade['scard_id']; ?>" style="cursor: pointer; text-align: center; width: 60px;">
                                            <input type="radio" name="scard_id" id="shade_<?php echo $shade['scard_id']; ?>" value="<?php echo $shade['scard_id']; ?>" required style="display: none;">
                                            <div style="width: 40px; height: 40px; background-color: <?php echo $shade['shade_color_code']; ?>; border-radius: 50%; border: 2px solid #ddd; margin: 0 auto; transition: 0.3s;" class="shade-circle"></div>
                                            <span style="font-size: 11px; font-weight: 500; display: block; margin-top: 5px; line-height: 1.2; color: #666;"><?php echo $shade['shade_name']; ?></span>
                                        </label>
                                        <?php } ?>
                                    </div>
                                    <style>
                                        input[type="radio"]:checked + .shade-circle {
                                            border: 3px solid #ca1515 !important;
                                            box-shadow: 0 0 5px rgba(202,21,21,0.5);
                                            transform: scale(1.1);
                                        }
                                    </style>
                                </li>
                            </ul>
                        </div>
                        <?php } ?>
                        
                        <div style="margin-top: 30px;">
                            <button type="submit" class="cart-btn" style="border: none; cursor: pointer; background-color: #ca1515; color: white; padding: 14px 30px; text-transform: uppercase; font-weight: 600; border-radius: 50px;">
                                <span class="icon_bag_alt"></span> Add to cart
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12 text-center" style="margin-top: 80px; margin-bottom: 40px;">
                <div class="related__title">
                    <h5>YOU MIGHT ALSO LIKE</h5>
                </div>
            </div>
            <?php
            $scat_id = $product['scat_id'];
            $rel_query = "SELECT p.*, AVG(r.rating) as avg_rating 
                         FROM product p 
                         LEFT JOIN product_reviews r ON p.p_id = r.p_id 
                         WHERE p.scat_id = '$scat_id' AND p.p_id != '$p_id' 
                         GROUP BY p.p_id 
                         ORDER BY RAND() LIMIT 4";
            $rel_res = mysqli_query($conn, $rel_query);
            if(mysqli_num_rows($rel_res) > 0) {
                while($rel_prod = mysqli_fetch_assoc($rel_res)) {
                    $rel_img = product_image_url($rel_prod['p_image']);
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product__item">
                    <div class="product__item__pic" style="height: 360px; overflow: hidden; position: relative;">
                        <img src="<?php echo $rel_img; ?>" onerror="this.src='../assets/images/shop/shop-1.jpg'" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                        <ul class="product__hover">
                            <li><a href="<?php echo $rel_img; ?>" class="image-popup"><span class="arrow_expand"></span></a></li>
                            <li><a href="../pages/product-details.php?id=<?php echo $rel_prod['p_id']; ?>"><span class="icon_bag_alt"></span></a></li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6><a href="../pages/product-details.php?id=<?php echo $rel_prod['p_id']; ?>"><?php echo $rel_prod['p_name']; ?></a></h6>
                        <div class="rating">
                            <?php 
                            $rel_avg = round($rel_prod['avg_rating'], 1);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= floor($rel_avg)) echo '<i class="fa fa-star"></i>';
                                elseif ($i - 0.5 <= $rel_avg) echo '<i class="fa fa-star-half-o"></i>';
                                else echo '<i class="fa fa-star-o"></i>';
                            }
                            ?>
                        </div>
                        <div class="product__price">Rs. <?php echo number_format($rel_prod['p_price']); ?></div>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
                echo "<div class='col-12 text-center text-muted'>No related products found.</div>";
            }
            ?>
        </div>
    </div>
</section>
<!-- Product Details Section End -->

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
