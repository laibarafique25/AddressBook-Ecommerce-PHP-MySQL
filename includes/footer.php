<?php
// Handle Newsletter Subscription
if (isset($_POST['subscribe_newsletter']) && isset($_POST['newsletter_email'])) {
    $email = trim($_POST['newsletter_email']);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Store in Session
        $_SESSION['newsletter_email'] = $email;
        
        // Store in Database if connection is available
        if (isset($conn)) {
            // Create table if not exists (Optional but good for robustness)
            $create_table = "CREATE TABLE IF NOT EXISTS newsletter (
                id INT AUTO_INCREMENT PRIMARY KEY, 
                email VARCHAR(255) NOT NULL UNIQUE, 
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            mysqli_query($conn, $create_table);
            
            // Insert email using prepared statement
            $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO newsletter (email) VALUES (?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }

        // Set success message for SweetAlert
        $_SESSION['status'] = "Subscribed successfully!";
        $_SESSION['status_title'] = "Success";
        $_SESSION['status_icon'] = "success";
    } else {
        // Set error message
        $_SESSION['status'] = "Please enter a valid email address.";
        $_SESSION['status_title'] = "Invalid Email";
        $_SESSION['status_icon'] = "error";
    }
    
    // Redirect to prevent form resubmission (works because of ob_start in header)
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
?>
<!-- Footer Section Begin -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-7">
                <div class="footer__about">
                    <div class="footer__logo">
                        <a href="../pages/index.php" class="site-logo-text">Address Book</a>
                    </div>
                    <p>Address Book is a modern e-commerce platform for fashion, jewelry, and cosmetics. It provides a smooth shopping experience with cart, wishlist, and secure ordering.</p>
                    <div class="footer__payment">
                        <a href="https://www.mastercard.us/en-us.html" target="_blank"><img src="../assets/images/payment/payment-1.png" alt="Mastercard"></a>
                        <a href="https://www.visa.com" target="_blank"><img src="../assets/images/payment/payment-2.png" alt="Visa"></a>
                        <a href="https://www.discover.com" target="_blank"><img src="../assets/images/payment/payment-3.png" alt="Discover"></a>
                        <a href="https://www.paypal.com" target="_blank"><img src="../assets/images/payment/payment-4.png" alt="PayPal"></a>
                        <a href="https://www.mastercard.com" target="_blank"><img src="../assets/images/payment/payment-5.png" alt="Cirrus"></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-5">
                <div class="footer__widget">
                    <h6>Quick links</h6>
                    <ul>
                        <li><a href="../pages/contact.php">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4">
                <div class="footer__widget">
                    <h6>Account</h6>
                    <ul>
                        <li><a href="../pages/checkout.php">Checkout</a></li>
                        <li><a href="../pages/cart_wishlist.php">Wishlist</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-8 col-sm-8">
                <div class="footer__newslatter">
                    <h6>NEWSLETTER</h6>
                    <form action="" method="POST">
                        <input type="email" name="newsletter_email" placeholder="Email" required>
                        <button type="submit" name="subscribe_newsletter" class="site-btn">Subscribe</button>
                    </form>
                    <div class="footer__social">
                        <a href="https://www.facebook.com" target="_blank"><i class="fa fa-facebook"></i></a>
                        <a href="https://www.twitter.com" target="_blank"><i class="fa fa-twitter"></i></a>
                        <a href="https://www.youtube.com" target="_blank"><i class="fa fa-youtube-play"></i></a>
                        <a href="https://www.instagram.com" target="_blank"><i class="fa fa-instagram"></i></a>
                        <a href="https://www.pinterest.com" target="_blank"><i class="fa fa-pinterest"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="footer__copyright__text">
                    <p>Copyright &copy; <script>document.write(new Date().getFullYear());</script> Address Book — Built by Laiba Misbah and Alishbah — DISM eProject.</p>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Footer Section End -->

<!-- Search Begin -->
<div class="search-model">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="search-close-switch">+</div>
        <form action="../pages/shop.php" method="GET" class="search-model-form">
            <input type="text" name="search" id="search-input" placeholder="Search products here.....">
        </form>
    </div>
</div>
<!-- Search End -->

<!-- Js Plugins -->
<script src="../assets/js/jquery-3.3.1.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery.magnific-popup.min.js"></script>
<script src="../assets/js/jquery-ui.min.js"></script>
<script src="../assets/js/mixitup.min.js"></script>
<script src="../assets/js/jquery.countdown.min.js"></script>
<script src="../assets/js/jquery.slicknav.js"></script>
<script src="../assets/js/owl.carousel.min.js"></script>
<script src="../assets/js/jquery.nicescroll.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>


<?php ob_end_flush(); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // '.wishlist-btn' par click hone ka intezar karein
    $(document).on('click', '.wishlist-btn', function(e) {
        e.preventDefault();
        
        var p_id = $(this).data('id'); // Product ID uthayega
        var heartIcon = $(this);

        $.ajax({
            url: '../actions/wishlist_handler.php',
            method: 'POST',
            data: { p_id: p_id },
            success: function(response) {
                if (response.trim() == 'login_required') {
                    alert('Please login first to like products!');
                } else if (response.trim() == 'added') {
                    // Success message aur page reload taake header update ho
                    Swal.fire({
                        icon: 'success',
                        title: 'Added!',
                        text: 'Product added to wishlist',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(function(){ location.reload(); }, 1500);
                } else if (response.trim() == 'removed') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Removed!',
                        text: 'Product removed from wishlist',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(function(){ location.reload(); }, 1500);
                }
            },
            error: function() {
                alert("Something went wrong with the connection!");
            }
        });
    });
});
</script>
</html>