<?php
session_start();
require 'config.php';
require 'sendEmail.php';
include 'components/header.php';
?>

<main class="main">
  <img src="assets/img/hero-bg-light.webp" alt="bg-img" style="position:absolute; width: 100%; height: 100%;">
  
  <!-- form Section start -->
  <section class="section">

    <div class="container section-title mt-5 pt-5">
      <h2 class="mt-5">Forgot Password</h2>
      
      <div class="container d-flex justify-content-center flex-wrap">
        <div class="col-md-6">
          <!-- message -->
          <div id="forgotMessage"></div>
          
          <form id="resetPasswordForm" method="POST" autocomplete="off" data-aos="fade-up" data-aos-delay="400">
            <div class="row gy-4  bg-dark">              
              <div class="col">
                <input type="text" class="form-control text-uppercase" name="uniqueId" id="uniqueId" maxlength="12" placeholder="Enter Parent/Guardian User ID" required>
              </div>
            </div>
            
            <button type="submit" name="pass-reset-btn" id="pass-reset-btn" class="form-btn mt-2">Reset Password</button>
          </form>
        </div>
      </div>
    </div>
      
  </section>
  <!-- form Section end -->

</main>


<?php include 'components/footer.php'; ?>


<!-- Preloader -->
<div id="preloader"></div>


<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Main JS File -->
<script src="assets/js/main.js"></script>
<script>
  $(document).ready(function() {

    // Handle form submission with AJAX
    $('#pass-reset-btn').on('click', function(e) {
      e.preventDefault();
      
      // $('#pass-reset-btn').css('background-color', '#6c757d').prop('disabled', true);

      const uniqueId = $('#uniqueId').val().trim();

      if (uniqueId) {
        $.ajax({
          type: 'POST',
          url: 'forgot_password.php',
          data: { uniqueId },
          success: function(response) {
            if (response.trim().toLowerCase() !== 'success') {
              $('#forgotMessage').html(response);
            } else {
              $('#forgotMessage').html('<div class="alert alert-success">An email has been sent to reset your password.</div>');
              $('#resetPasswordForm')[0].reset();
              
              $('#pass-reset-btn').text('Mail Sent').css('background-color', '');          
              
              setTimeout(() => {
                window.location.href = 'login'; // Redirect to the login page after 2 seconds
              }, 2000);
            }
          },
          error: function() {
            $('#forgotMessage').html('<div class="alert alert-danger">Something went wrong. Please try again later.</div>');
          }
        });
      } else {
        $('#forgotMessage').html('<div class="alert alert-danger">Please Enter Correct ID</div>');
      }
    });
  });

  
  // Function to trim spaces when pasting or typing
  function trimSpaces(element) {
    // Get the element's value, trim it, and replace multiple spaces with a single space
    let trimmedValue = $(element).val().trim().replace(/\s+/g, ' ');
    $(element).val(trimmedValue);
  }

  // Apply trimSpaces function on input and textarea
  $('#uniqueId').on('input paste', function () {
    setTimeout(() => trimSpaces(this), 0); // Use timeout to handle paste event properly
  });
</script>

</body>
</html>
