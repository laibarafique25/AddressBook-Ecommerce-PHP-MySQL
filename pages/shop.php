<?php 
require_once __DIR__ . '/../includes/header.php';
?>

<style>
    /* Rating stars styling */
    .product__item__text .rating i {
        color: #f3d001; /* Golden Color */
        font-size: 12px;
        margin-right: -2px;
    }
    .product__item__text .rating {
        margin-bottom: 4px;
    }
    .product__item__text .rating span {
        font-size: 12px;
        color: #b2b2b2;
        margin-left: 4px;
    }
</style>

<section class="shop spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <div class="shop__sidebar">
                    <div class="sidebar__categories">
                        <div class="section-title">
                            <h4>Categories</h4>
                        </div>
                        <div class="categories__accordion">
                            <div class="accordion" id="accordionExample">
                                <?php
                                $main_res = mysqli_query($conn, "SELECT * FROM main_cat");
                                while ($main = mysqli_fetch_assoc($main_res)) {
                                    $m_id = $main['mcat_id'];
                                ?>
                                <div class="card">
                                    <div class="card-heading">
                                        <a data-toggle="collapse" data-target="#collapse<?php echo $m_id; ?>">
                                            <?php echo $main['mcat_name']; ?>
                                        </a>
                                    </div>
                                    <div id="collapse<?php echo $m_id; ?>" class="collapse" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <ul>
                                                <?php
                                                $sub_res = mysqli_query($conn, "SELECT * FROM sub_cat WHERE mcat_id = '$m_id'");
                                                while ($sub = mysqli_fetch_assoc($sub_res)) {
                                                    $s_id = $sub['sub_cat_id'];
                                                    $child_res = mysqli_query($conn, "SELECT * FROM child_cat WHERE scat_id = '$s_id'");
                                                    
                                                    if ($child_res && mysqli_num_rows($child_res) > 0) {
                                                        echo "<li style='font-weight:bold; color:#111; margin-top:10px; list-style:none;'>".$sub['sub_cat_name']."</li>";
                                                        while ($child = mysqli_fetch_assoc($child_res)) {
                                                            echo "<li style='padding-left:15px;'><a href='../pages/shop.php?ccat=".$child['ccat_id']."'>- ".$child['ccat_name']."</a></li>";
                                                        }
                                                    } else {
                                                        echo "<li><a href='../pages/shop.php?scat=$s_id'>".$sub['sub_cat_name']."</a></li>";
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9 col-md-9">
                <div class="row">
                    <?php
                    if (!function_exists('get_shade_group')) {
                        function get_shade_group($p_name) {
                            $name = strtolower($p_name);
                            if (strpos($name, 'highlighter') !== false) return 'Highlighter';
                            if (strpos($name, 'lipstick') !== false) return 'Lipstick';
                            if (strpos($name, 'lip tint') !== false) return 'Lip Tint';
                            if (strpos($name, 'foundation') !== false || strpos($name, 'concealer') !== false || strpos($name, 'conceler') !== false) return 'Base Makeup';
                            if (strpos($name, 'blush') !== false) return 'Blush 1';
                            return null;
                        }
                    }
                    
                    // JOIN query to get average rating for each product
                    $product_sql = "SELECT p.*, AVG(r.rating_value) as avg_rating, COUNT(r.rating_id) as total_reviews 
                                    FROM product p 
                                    LEFT JOIN product_ratings r ON p.p_id = r.p_id";

                    $where = [];
                    if(isset($_GET['mcat'])) {
                        $m_id = mysqli_real_escape_string($conn, $_GET['mcat']);
                        $where[] = "p.scat_id IN (SELECT sub_cat_id FROM sub_cat WHERE mcat_id = '$m_id')";
                    } elseif(isset($_GET['scat'])) {
                        $s_id = mysqli_real_escape_string($conn, $_GET['scat']);
                        $where[] = "p.scat_id = '$s_id'";
                    } elseif(isset($_GET['ccat'])) {
                        $c_id = mysqli_real_escape_string($conn, $_GET['ccat']);
                        $where[] = "p.ccat_id = '$c_id'";
                    }
                    
                    if(isset($_GET['search']) && !empty($_GET['search'])) {
                        $s = mysqli_real_escape_string($conn, $_GET['search']);
                        $where[] = "(p.p_name LIKE '%$s%' OR p.pro_description LIKE '%$s%')";
                    }

                    if(count($where) > 0) {
                        $product_sql .= " WHERE " . implode(" AND ", $where);
                    }

                    $product_sql .= " GROUP BY p.p_id";
                    $product_res = mysqli_query($conn, $product_sql);

                    if (mysqli_num_rows($product_res) > 0) {
                        while($row = mysqli_fetch_assoc($product_res)) {
                            $path = product_image_url($row['p_image']);
                            $p_id = $row['p_id'];
                            $avg_rating = round($row['avg_rating']); // Rounding for star display
                            $total_reviews = $row['total_reviews'];
                            
                            $shade_group = get_shade_group($row['p_name']);
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="product__item">
                            <div class="product__item__pic" style="position: relative;">
                                <img src="<?php echo $path; ?>" alt="" style="width:100%; height:360px; object-fit:cover;">
                                <ul class="product__hover">
                                    <li><a href="<?php echo $path; ?>" class="image-popup"><span class="arrow_expand"></span></a></li>
                                    <li><a href="../pages/singleproduct.php?p_id=<?php echo $p_id; ?>"><span class="icon_search"></span></a></li>
                                </ul>
                                <a href="../pages/singleproduct.php?p_id=<?php echo $p_id; ?>" style="position:absolute; top:0; left:0; width:100%; height:100%; z-index:1;"></a>
                            </div>
                            <div class="product__item__text">
                                <div class="rating">
                                    <?php 
                                    for($i = 1; $i <= 5; $i++) {
                                        if($i <= $avg_rating) {
                                            echo '<i class="fa fa-star"></i>';
                                        } else {
                                            echo '<i class="fa fa-star-o"></i>';
                                        }
                                    }
                                    ?>
                                    <span>(<?php echo $total_reviews; ?>)</span>
                                </div>
                                <h6><a href="../pages/singleproduct.php?p_id=<?php echo $p_id; ?>"><?php echo $row['p_name']; ?></a></h6>
                                <div class="product__price">Rs. <?php echo number_format($row['p_price']); ?></div>
                                <?php if ($shade_group) { ?>
                                    <a href="#" data-toggle="modal" data-target="#shadeModal<?php echo $p_id; ?>" class="site-btn" style="padding: 8px 16px; font-size: 12px; height: auto; line-height: normal; margin-top: 10px; width: 100%; text-align: center;">Add to Cart</a>
                                <?php } else { ?>
                                    <a href="../actions/add_to_cart.php?p_id=<?php echo $p_id; ?>" class="site-btn" style="padding: 8px 16px; font-size: 12px; height: auto; line-height: normal; margin-top: 10px; width: 100%; text-align: center;">Add to Cart</a>
                                <?php } ?>
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
                    } else {
                        echo "<div class='col-lg-12 text-center'><h4>No products available.</h4></div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>