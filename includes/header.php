<?php
// Sabse pehle Output Buffering start karein
ob_start();

// Session check aur start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// Calculate Base URL dynamically
$script_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// Remove current subfolder to get project root
$base_url = preg_replace('/\/(pages|auth|actions|includes|admin)$/', '', $script_path);
$base_url = rtrim($base_url, '/') . '/';

$current_page = basename($_SERVER['PHP_SELF']);
$mcat_get = $_GET['mcat'] ?? '';
$scat_get = $_GET['scat'] ?? '';


/** @var mysqli $conn */
function product_image_url($p_image) {
    $raw = trim((string)$p_image);
    if ($raw === '') return '';

    $raw = str_replace('\\', '/', $raw);
    $basename = basename($raw);
    
    $basename_no_ts = preg_replace('/^[0-9]+_/', '', $basename);
    $candidates = [];
    $candidates[] = $basename;
    $candidates[] = rawurldecode($basename);
    
    if (strpos($basename, ' ') !== false) {
        $candidates[] = str_replace(' ', '_', $basename);
        $candidates[] = str_replace(' ', '-', $basename);
    }
    if (strpos($basename, '%') !== false) {
        $candidates[] = str_replace('%', '_', $basename);
    }
    
    if ($basename !== $basename_no_ts) {
        $candidates[] = $basename_no_ts;
        $candidates[] = rawurldecode($basename_no_ts);
        if (strpos($basename_no_ts, ' ') !== false) {
            $candidates[] = str_replace(' ', '_', $basename_no_ts);
        }
    }
    
    $candidates = array_values(array_unique(array_filter(array_map('trim', $candidates))));

    $folders = [
        ['fs' => __DIR__ . '/../assets/images/shop/',    'web' => '../assets/images/shop/'],
        ['fs' => __DIR__ . '/../assets/images/product/', 'web' => '../assets/images/product/'],
        ['fs' => __DIR__ . '/../assets/images/',         'web' => '../assets/images/'],
        ['fs' => __DIR__ . '/../assets/images/instagram/', 'web' => '../assets/images/instagram/'],
    ];

    foreach ($folders as $folder) {
        // 1. Try candidates first (fast)
        foreach ($candidates as $name) {
            if ($name === '') continue;
            if (file_exists($folder['fs'] . $name)) {
                $final_name = str_replace(' ', '%20', $name);
                return $folder['web'] . $final_name;
            }
        }
        
        // 2. Aggressive search: look for any file that matches the basename part
        $search_pattern = $folder['fs'] . '*' . $basename_no_ts;
        $matches = glob($search_pattern);
        if ($matches && count($matches) > 0) {
            $found_file = basename($matches[0]);
            $final_name = str_replace(' ', '%20', $found_file);
            return $folder['web'] . $final_name;
        }

        // 3. Try with common extensions if missing or wrong
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'JPG', 'PNG', 'JPEG'];
        $pure_name = pathinfo($basename_no_ts, PATHINFO_FILENAME);
        foreach ($extensions as $ext) {
            $test_name = $pure_name . '.' . $ext;
            if (file_exists($folder['fs'] . $test_name)) {
                $final_name = str_replace(' ', '%20', $test_name);
                return $folder['web'] . $final_name;
            }
        }
    }

    // Direct path check as final resort
    if (file_exists(__DIR__ . '/../' . $raw)) {
        return '../' . $raw;
    }

    $final_basename = str_replace(' ', '%20', $basename);
    return '../assets/images/shop/' . $final_basename;
}

// Counts initialize karein
$cart_count = 0;
$wishlist_count = 0;

if (isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    
    // 1. Cart Count Query
    $cart_q = "SELECT COUNT(*) as total FROM cart_items ci JOIN cart c ON ci.cart_id = c.cart_id WHERE c.user_id = '$u_id'";
    $cart_r = mysqli_query($conn, $cart_q);
    if ($cart_r) {
        $cart_d = mysqli_fetch_assoc($cart_r);
        $cart_count = $cart_d['total'];
    }

    // 2. Wishlist Count Query (Naya logic)
    $wish_q = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = '$u_id'";
    $wish_r = mysqli_query($conn, $wish_q);
    if ($wish_r) {
        $wish_d = mysqli_fetch_assoc($wish_r);
        $wishlist_count = $wish_d['total'];
    }
} else {
    // Guest Counts
    if (isset($_SESSION['guest_cart'])) {
        $cart_count = count($_SESSION['guest_cart']);
    }
    if (isset($_SESSION['guest_wishlist'])) {
        $wishlist_count = count($_SESSION['guest_wishlist']);
    }
}
?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Address Book Template">
    <meta name="keywords" content="Address Book, unique, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Address Book</title>

    <link href="https://fonts.googleapis.com/css2?family=Cookie&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/jquery-ui.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/style.css" type="text/css">
    <style>
        .error-msg { 
            color: #ca1515; 
            font-size: 12px; 
            font-weight: 500;
            display: none;
            margin-top: 3px;
        }
        input.invalid {
            border-color: #ca1515 !important;
        }
    </style>

   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Ye function default alert ko SweetAlert mein badal dega
    window.alert = function(message) {
        Swal.fire({
            text: message,
            icon: 'warning', 
            confirmButtonColor: '#ca1515', 
            confirmButtonText: 'OK'
        });
    };
</script>
</head>

<body>
    <?php require_once __DIR__ . '/alert.php'; ?>

    <div id="preloder">
        <div class="loader"></div>
    </div>

    <div class="offcanvas-menu-overlay"></div>
    <div class="offcanvas-menu-wrapper">
        <div class="offcanvas__close">+</div>
        <ul class="offcanvas__widget">
            <li><span class="icon_search search-switch"></span></li>
            <li><a href="../pages/cart_wishlist.php"><span class="icon_heart_alt"></span><div class="tip"><?php echo $wishlist_count; ?></div></a></li>
            <li><a href="../pages/shopcart.php"><span class="icon_bag_alt"></span><div class="tip"><?php echo $cart_count; ?></div></a></li>
        </ul>
        <div class="offcanvas__logo">
            <a href="../pages/index.php" class="site-logo-text">Address Book</a>
        </div>
        <div id="mobile-menu-wrap"></div>
        <div class="offcanvas__auth">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#"><?php echo $_SESSION['user_name']; ?></a>
                <a href="../auth/logout.php">Logout</a>
            <?php else: ?>
                <a href="../auth/login.php">Login</a>
                <a href="../auth/signup.php">SignUp</a>
            <?php endif; ?>
        </div>
    </div>

    <header class="header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-3 col-lg-2">
                    <div class="header__logo">
                        <a href="../pages/index.php" class="site-logo-text">Address Book</a>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-7">
                    <nav class="header__menu">
                        <ul>
                            <li class="<?= $current_page == 'index.php' ? 'active' : '' ?>"><a href="../pages/index.php">Home</a></li>
                            <li class="<?= ($current_page == 'shop.php' && $mcat_get == '3') ? 'active' : '' ?>"><a href="../pages/shop.php?mcat=3">Jewellery</a></li>
                            <li class="<?= ($current_page == 'shop.php' && ($scat_get == '10' || $scat_get == '11')) ? 'active' : '' ?>"><a href="#">Cosmetics <i class="fa fa-angle-down"></i></a>
                                <ul class="dropdown">
                                    <li class="<?= $scat_get == '10' ? 'active' : '' ?>"><a href="../pages/shop.php?scat=10">Skin Care</a></li>
                                    <li class="<?= $scat_get == '11' ? 'active' : '' ?>"><a href="../pages/shop.php?scat=11">Makeup</a></li>
                                </ul>
                            </li>
                            <li class="<?= ($current_page == 'shop.php' && $mcat_get == '' && $scat_get == '') ? 'active' : '' ?>"><a href="../pages/shop.php">Shop</a></li>
                            <li class="<?= $current_page == 'contact.php' ? 'active' : '' ?>"><a href="../pages/contact.php">Contact</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-3">
                    <div class="header__right">
                        <div class="header__right__auth">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="#"><?php echo $_SESSION['user_name']; ?></a>
                                <a href="../auth/logout.php">Logout</a>
                            <?php else: ?>
                                <a href="../auth/login.php">Login</a>
                                <a href="../auth/signup.php">Register</a>
                            <?php endif; ?>
                        </div>
                        <ul class="header__right__widget">
                            <li><span class="icon_search search-switch"></span></li>
                            <li><a href="../pages/cart_wishlist.php"><span class="icon_heart_alt"></span><div class="tip"><?php echo $wishlist_count; ?></div></a></li>
                            <li><a href="../pages/shopcart.php"><span class="icon_bag_alt"></span><div class="tip"><?php echo $cart_count; ?></div></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="canvas__open">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>