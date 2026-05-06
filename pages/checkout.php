<?php 
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['status'] = "Please create an account or login to proceed with checkout.";
    $_SESSION['status_title'] = "Login Required";
    $_SESSION['status_icon'] = "warning";
    header("Location: ../auth/login.php");
    exit();
}
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

    <!-- Checkout Section Begin -->
    <section class="checkout spad">
        <div class="container">

            <form action="../actions/place_order.php" method="POST" class="checkout__form" id="checkoutForm" novalidate>
                <div class="row">
                    <div class="col-lg-8">
                        <h5>Billing detail</h5>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="checkout__form__input">
                                    <p>First Name <span>*</span></p>
                                    <input type="text" name="first_name" id="first_name">
                                    <span class="error-msg" id="first_name_err">Please enter at least 2 letters.</span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="checkout__form__input">
                                    <p>Last Name <span>*</span></p>
                                    <input type="text" name="last_name" id="last_name">
                                    <span class="error-msg" id="last_name_err">Please enter at least 2 letters.</span>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="checkout__form__input">
                                    <p>Country <span>*</span></p>
                                    <input type="text" name="country" id="country">
                                    <span class="error-msg" id="country_err">Country is required.</span>
                                </div>
                                <div class="checkout__form__input">
                                    <p>Address <span>*</span></p>
                                    <input type="text" name="address1" id="address1" placeholder="Street Address">
                                    <span class="error-msg" id="address1_err">Address is required.</span>
                                    <input type="text" name="address2" placeholder="Apartment. suite, unite ect ( optinal )">
                                </div>
                                <div class="checkout__form__input">
                                    <p>Town/City <span>*</span></p>
                                    <input type="text" name="city" id="city">
                                    <span class="error-msg" id="city_err">City is required.</span>
                                </div>
                                <div class="checkout__form__input">
                                    <p>Country/State <span>*</span></p>
                                    <input type="text" name="state" id="state">
                                    <span class="error-msg" id="state_err">State is required.</span>
                                </div>
                                <div class="checkout__form__input">
                                    <p>Postcode/Zip <span>*</span></p>
                                    <input type="text" name="zip" id="zip">
                                    <span class="error-msg" id="zip_err">Valid 5+ digit zip code required.</span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="checkout__form__input">
                                    <p>Phone <span>*</span></p>
                                    <input type="tel" name="phone" id="phone">
                                    <span class="error-msg" id="phone_err">Valid 10-15 digit phone required.</span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="checkout__form__input">
                                    <p>Email <span>*</span></p>
                                    <input type="email" name="email" id="email">
                                    <span class="error-msg" id="email_err">Enter a valid email address.</span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-4">
                            <div class="checkout__order">
                                <h5>Your order</h5>
                                <div class="checkout__order__product">
                                    <ul>
                                        <li>
                                            <span class="top__text">Product</span>
                                            <span class="top__text__right">Total</span>
                                        </li>
                                        <?php
                                        $user_id = $_SESSION['user_id'] ?? null;
                                        $subtotal = 0;
                                        if ($user_id) {
                                            $order_query = "SELECT ci.*, p.p_name, p.p_price 
                                                           FROM cart_items ci 
                                                           JOIN cart c ON ci.cart_id = c.cart_id 
                                                           JOIN product p ON ci.p_id = p.p_id 
                                                           WHERE c.user_id = '$user_id'";
                                            $order_res = mysqli_query($conn, $order_query);
                                            $i = 1;
                                            while ($row = mysqli_fetch_assoc($order_res)) {
                                                $item_total = $row['p_price'] * $row['quantity'];
                                                $subtotal += $item_total;
                                                ?>
                                                <li><?php echo sprintf("%02d", $i++); ?>. <?php echo $row['p_name']; ?> <span>Rs. <?php echo number_format($item_total); ?></span></li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div class="checkout__order__total">
                                    <ul>
                                        <li>Subtotal <span>Rs. <?php echo number_format($subtotal); ?></span></li>
                                        <li>Total <span>Rs. <?php echo number_format($subtotal); ?></span></li>
                                    </ul>
                                </div>
                                <div class="checkout__order__widget">
                                    <label for="check-payment">
                                        Cash on Delivery
                                        <input type="radio" id="check-payment" name="payment_method" value="Cash on Delivery" checked>
                                        <span class="checkmark"></span>
                                    </label>
                                    <label for="cheque">
                                        Cheque payment
                                        <input type="radio" id="cheque" name="payment_method" value="Cheque Payment">
                                        <span class="checkmark"></span>
                                    </label>
                                    <label for="paypal">
                                        PayPal
                                        <input type="radio" id="paypal" name="payment_method" value="PayPal">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                                <button type="submit" class="site-btn">Place order</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <!-- Checkout Section End -->

        <!-- Instagram Begin -->
        
              
        <!-- Instagram End -->

        <?php 
require_once __DIR__ . '/../includes/footer.php';
?>

<script>
$(document).ready(function() {
    $('#checkoutForm').on('submit', function(e) {
        let isValid = true;
        $('.error-msg').hide();
        $('input').removeClass('invalid');

        function showError(id, msgId) {
            $(id).addClass('invalid');
            $(msgId).show();
            isValid = false;
        }

        // Validations
        if($('#first_name').val().trim().length < 2) showError('#first_name', '#first_name_err');
        if($('#last_name').val().trim().length < 2) showError('#last_name', '#last_name_err');
        if($('#country').val().trim() == "") showError('#country', '#country_err');
        if($('#address1').val().trim() == "") showError('#address1', '#address1_err');
        if($('#city').val().trim() == "") showError('#city', '#city_err');
        if($('#state').val().trim() == "") showError('#state', '#state_err');
        
        let zipRegex = /^[0-9]{5,}$/;
        if(!zipRegex.test($('#zip').val().trim())) showError('#zip', '#zip_err');
        
        let phoneRegex = /^[0-9]{10,15}$/;
        if(!phoneRegex.test($('#phone').val().trim())) showError('#phone', '#phone_err');
        
        let email = $('#email').val().trim();
        if(email == "" || !email.includes('@')) showError('#email', '#email_err');

        if(!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: ($('.invalid').first().offset().top - 100)
            }, 500);
        }
    });
});
</script>
?>