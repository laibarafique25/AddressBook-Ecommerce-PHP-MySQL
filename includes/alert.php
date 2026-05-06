<?php
// Check karein ke session mein status set hai ya nahi
if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                // addslashes use karne se quotes wala masla hal ho jata hai
                title: "<?php echo addslashes($_SESSION['status_title']); ?>",
                text: "<?php echo addslashes($_SESSION['status']); ?>",
                icon: "<?php echo $_SESSION['status_icon']; ?>", // success, error, warning, info
                confirmButtonColor: "#ca1515",
                confirmButtonText: "OK"
            });
        });
    </script>
<?php
    // Alert ka kaam khatam, ab sessions delete kar dein
    unset($_SESSION['status']);
    unset($_SESSION['status_title']);
    unset($_SESSION['status_icon']);
}
?>