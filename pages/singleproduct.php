<?php 
require_once __DIR__ . '/../includes/header.php';

// 1. Get Product ID from URL
if(isset($_GET['p_id'])) {
    $p_id = mysqli_real_escape_string($conn, $_GET['p_id']);
    
    // 2. Fetch Product Details with Average Rating
    $product_sql = "SELECT p.*, AVG(r.rating_value) as avg_rating, COUNT(r.rating_id) as total_reviews 
                    FROM product p 
                    LEFT JOIN product_ratings r ON p.p_id = r.p_id 
                    WHERE p.p_id = '$p_id' 
                    GROUP BY p.p_id";
    
    $product_res = mysqli_query($conn, $product_sql);
    $product = mysqli_fetch_assoc($product_res);

    if(!$product) {
        echo "<script>window.location.href='../pages/shop.php';</script>";
        exit();
    }

    // Average rating ko round karna
    $avg_rating = round($product['avg_rating'], 1);
    $total_reviews = $product['total_reviews'];

    // Category ka naam nikalne k liye
    $scat_id = $product['scat_id'];
    $cat_query = mysqli_query($conn, "SELECT sub_cat_name FROM sub_cat WHERE sub_cat_id = '$scat_id'");
    $cat_data = mysqli_fetch_assoc($cat_query);
    $category_name = ($cat_data) ? $cat_data['sub_cat_name'] : "General";

} else {
    echo "<script>window.location.href='../pages/shop.php';</script>";
    exit();
}
?>

<style>
    /* Image Size Control */
    .product__details__pic__slider img {
        height: 500px; 
        object-fit: cover;
        width: 100%;
        border-radius: 5px;
    }
    .product__thumb img {
        height: 120px; 
        object-fit: cover;
    }

    /* Golden Rating Stars Selection */
    .rating-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        margin: 10px 0;
    }
    .rating-input input { display: none; }
    .rating-input label {
        cursor: pointer;
        width: 35px;
        font-size: 30px;
        color: #ccc;
    }
    .rating-input label:before { content: '\f005'; font-family: FontAwesome; }
    .rating-input input:checked ~ label,
    .rating-input label:hover,
    .rating-input label:hover ~ label {
        color: #f3d001; /* Gold color */
    }

    /* Static Stars display color */
    .product__details__text .rating i {
        color: #f3d001;
    }
    .related__img {
        height: 300px;
        object-fit: cover;
    }
</style>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="../pages/index.php"><i class="fa fa-home"></i> Home</a>
                    <a href="../pages/shop.php">Shop</a>
                    <span><?php echo $product['p_name']; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="product-details spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="product__details__pic">
                    <div class="product__details__pic__left product__thumb nice-scroll">
                        <a class="pt active" href="#product-1">
                            <img src="../assets/images/shop/<?php echo $product['p_image']; ?>" alt="">
                        </a>
                    </div>
                    <div class="product__details__slider__content">
                        <div class="product__details__pic__slider owl-carousel">
                            <img data-hash="product-1" class="product__big__img" src="../assets/images/shop/<?php echo $product['p_image']; ?>" alt="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="product__details__text">
                    <h3><?php echo $product['p_name']; ?> <span>Brand: Jenny Beauty</span></h3>
                    
                    <div class="rating">
                        <?php 
                        for($i = 1; $i <= 5; $i++) {
                            if($i <= $avg_rating) {
                                echo '<i class="fa fa-star"></i> ';
                            } else {
                                echo '<i class="fa fa-star-o"></i> ';
                            }
                        }
                        ?>
                        <span>( <?php echo $total_reviews; ?> Reviews )</span>
                    </div>

                    <div class="product__details__price">Rs. <?php echo number_format($product['p_price']); ?></div>
                    <p><?php echo $product['pro_description']; ?></p>
                    
                    <div class="product__details__button">
                        <form action="../actions/add_to_cart.php" method="GET">
                            <input type="hidden" name="p_id" value="<?php echo $product['p_id']; ?>">
                            <div class="quantity">
                                <span>Quantity:</span>
                                <div class="pro-qty"><input type="text" name="qty" value="1"></div>
                            </div>
                            <button type="submit" class="cart-btn" style="border:none; cursor:pointer;"><span class="icon_bag_alt"></span> Add to cart</button>
                        </form>
                    </div>

                    <div class="product__details__widget">
                        <ul>
                            <li><span>Availability:</span> <p>In Stock</p></li>
                            <li><span>Category:</span> <p><?php echo $category_name; ?></p></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="product__details__tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">Description</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Rate This Product</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tabs-1" role="tabpanel">
                            <h6>Description</h6>
                            <p><?php echo $product['pro_description']; ?></p>
                        </div>
                        
                        <div class="tab-pane" id="tabs-2" role="tabpanel">
                            <h6>Leave a Rating</h6>
                            <p>Tap on the stars to select your rating:</p>
                            <form action="../actions/submit_rating.php" method="POST">
                                <input type="hidden" name="p_id" value="<?php echo $p_id; ?>">
                                <input type="hidden" name="user_id" value="1"> <div class="rating-input">
                                    <input type="radio" name="rating_value" id="star5" value="5" required><label for="star5"></label>
                                    <input type="radio" name="rating_value" id="star4" value="4"><label for="star4"></label>
                                    <input type="radio" name="rating_value" id="star3" value="3"><label for="star3"></label>
                                    <input type="radio" name="rating_value" id="star2" value="2"><label for="star2"></label>
                                    <input type="radio" name="rating_value" id="star1" value="1"><label for="star1"></label>
                                </div>
                                
                                <button type="submit" name="submit_rating" class="site-btn">Submit Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="related-product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="section-title">
                    <h4>Products You May Like</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <?php
            // Sub-category (scat_id) ke mutabiq same products lana
            $related_res = mysqli_query($conn, "SELECT * FROM product WHERE scat_id = '$scat_id' AND p_id != '$p_id' LIMIT 4");
            
            if(mysqli_num_rows($related_res) > 0) {
                while($rel = mysqli_fetch_assoc($related_res)) {
                    $rel_id = $rel['p_id'];
                    $rel_path = "../assets/images/shop/" . $rel['p_image'];
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product__item">
                    <div class="product__item__pic" style="background-image: url('<?php echo $rel_path; ?>'); height: 270px; background-size: cover; background-position: center; position: relative; cursor: pointer;" onclick="window.location.href='../pages/singleproduct.php?p_id=<?php echo $rel_id; ?>'">
                        <ul class="product__hover">
                            <li><a href="../pages/singleproduct.php?p_id=<?php echo $rel_id; ?>"><span class="icon_search"></span></a></li>
                        </ul>
                    </div>
                    <div class="product__item__text">
                        <h6><a href="../pages/singleproduct.php?p_id=<?php echo $rel_id; ?>"><?php echo $rel['p_name']; ?></a></h6>
                        <div class="product__price">Rs. <?php echo number_format($rel['p_price']); ?></div>
                    </div>
                </div>
            </div>
            <?php 
                } 
            } else {
                echo "<p class='text-center w-100'>No similar products found.</p>";
            }
            ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>