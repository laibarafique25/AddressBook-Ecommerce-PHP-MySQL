<?php 
require_once __DIR__ . '/../includes/header.php'; 
?>

<style>
    /* Map Section Styling */
    .contact-map {
        height: 450px;
        position: relative;
    }
    
    .contact-map iframe {
        width: 100%;
        height: 100%;
        border: none;
        filter: grayscale(20%) contrast(1.1); /* Map ko thora cinematic look dene ke liye */
    }

    /* Map ke upar wala floating address box */
    .map-widget {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        background: #ffffff;
        padding: 30px 40px;
        box-shadow: 0px 15px 50px rgba(0, 0, 0, 0.1);
        text-align: center;
        border-radius: 2px;
        z-index: 10;
    }

    .map-widget i {
        font-size: 30px;
        color: #ca1515; /* Theme Red Color */
        margin-bottom: 15px;
        display: block;
    }

    .map-widget h4 {
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 10px;
        letter-spacing: 2px;
    }

    .map-widget ul {
        list-style: none;
        padding: 0;
    }

    .map-widget ul li {
        font-size: 14px;
        color: #666;
        margin-bottom: 5px;
    }
</style>

<div class="contact-map">
    <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3618.140411306126!2d67.08752257595208!3d24.92724494258389!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3eb33f1469e38f1b%3A0xf63989ed62973801!2sLucky%20One%20Mall!5e0!3m2!1sen!2s!4v1714000000000!5m2!1sen!2s" 
        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
    </iframe>
    
    <div class="map-widget">
        <i class="fa fa-map-marker"></i>
        <h4>Karachi</h4>
        <ul>
            <li><strong>Lucky One Mall</strong></li>
            <li>Main Rashid Minhas Rd, Block 21, Gulberg Town</li>
            <li>Phone: +92 21 34567890</li>
        </ul>
    </div>
</div>
<section class="contact spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="contact__content">
                    <div class="contact__address">
                        <h5>Contact Info</h5>
                        <ul>
                            <li>
                                <h6><i class="fa fa-map-marker"></i> Address</h6>
                                <p>Jenny Beauty & Lifestyle, First Floor, Lucky One Mall, Karachi.</p>
                            </li>
                            <li>
                                <h6><i class="fa fa-phone"></i> Phone</h6>
                                <p><span>+92 300 1234567</span></p>
                            </li>
                            <li>
                                <h6><i class="fa fa-envelope"></i> Email</h6>
                                <p>info@jenny.com</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="contact__form">
                    <h5>Send Message</h5>
                    <form action="#" id="contactForm" novalidate>
                        <input type="text" id="name" placeholder="Name">
                        <span class="error-msg" id="name_err">Name must be at least 3 characters.</span>
                        
                        <input type="email" id="email" placeholder="Email">
                        <span class="error-msg" id="email_err">Please enter a valid email.</span>
                        
                        <textarea id="message" placeholder="Message"></textarea>
                        <span class="error-msg" id="msg_err">Message must be at least 10 characters.</span>
                        
                        <button type="submit" class="site-btn">Send Message</button>
                    </form>

<script src="../assets/js/jquery-3.3.1.min.js"></script>
<script>
$('#contactForm').on('submit', function(e) {
    let isValid = true;
    $('.error-msg').hide();
    $('input, textarea').removeClass('invalid');

    if($('#name').val().trim().length < 3) {
        $('#name').addClass('invalid');
        $('#name_err').show();
        isValid = false;
    }
    let email = $('#email').val().trim();
    if(email == "" || !email.includes('@')) {
        $('#email').addClass('invalid');
        $('#email_err').show();
        isValid = false;
    }
    if($('#message').val().trim().length < 10) {
        $('#message').addClass('invalid');
        $('#msg_err').show();
        isValid = false;
    }

    if(!isValid) e.preventDefault();
    else {
        e.preventDefault();
        alert('Message sent successfully!');
        location.reload();
    }
});
</script>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>