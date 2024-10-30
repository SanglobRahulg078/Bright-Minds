<?php include 'components/header.php'; ?>

<main class="main">
  <img src="assets/img/hero-bg-light.webp" alt="bg-img" style="position:absolute; width: 100%; height: 100%;">

  <!-- Login Section -->
  <section class="section">
    <div class="container section-title mt-5 pt-5">
      <h2 class="mt-5">Login</h2>

      <div class="container d-flex justify-content-center flex-wrap">
        <div class="col-md-6">
          <!-- message -->
          <div id="loginMessage"></div>

          <form id="loginForm" method="POST" data-aos="fade-up" data-aos-delay="400">
            <!-- CSRF Token -->
             
            
            <!-- Radio Buttons for Login Type -->
            <div class="d-flex justify-content-around">
              <label><input type="radio" name="login" value="new_user" id="new_user"> New User</label>
              <label><input type="radio" name="login" value="parent_login" id="parent_login" checked> Parent Login</label>
              <label><input type="radio" name="login" value="application_login" id="application_login"> Applicant Login</label>
            </div>

            <!-- Unique ID Input -->
            <div class="my-3">
              <input type="text" class="form-control text-uppercase" name="uniqueId" id="uniqueId" placeholder="Parent/Guardian User ID" required maxlength="30" pattern="^[a-zA-Z0-9@._-]+$" title="Only letters, numbers, and basic special characters (@, ., -, _)" autocomplete="off" />
            </div>

            <!-- Password Input -->
            <div class="my-3">
              <input type="password" class="form-control" name="password" id="password" placeholder="Password" required minlength="8" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" title="Password must be at least 8 characters long, and include uppercase, lowercase, number, and a special character." autocomplete="new-password" />
            </div>

            <!-- Submit Button and Forgot Password -->
            <div class="d-flex justify-content-between">
              <a href="password-reset" class="font-weight-bold" id="forgotPassword">Forgot Password?</a>
              <button id="loginBtn" class="form-btn">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
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

<!-- Modern JS for AJAX Login and Validation -->
<script>
  $(document).ready(function() {
    // Redirect to registration on new user selection
    $('#new_user').change(function() {
      window.location.href = 'register';
    });    

    // Update form fields dynamically based on login type
    $('input[name="login"]').change(function() {
      const options = {
        parent_login: { placeholder: "Parent/Guardian User ID", maxlength: 30 },
        application_login: { placeholder: "Applicant ID", maxlength: 12 }
      };

      const selected = $(this).attr('id');

      if (options[selected]) {
        $('#uniqueId').attr({
          placeholder: options[selected].placeholder,
          maxlength: options[selected].maxlength
        });
      }

      // Change visibility of "Forgot Password" link
      if (selected === 'parent_login') {
        $('#forgotPassword').css({ 'visibility': 'visible', 'opacity': '1' });
      } else {
        $('#forgotPassword').css({ 'visibility': 'hidden', 'opacity': '0' });
      }
      
    });

    // Handle form submission via AJAX
    $('#loginBtn').on('click', function(e) {
      e.preventDefault();

      const loginType = $('input[name="login"]:checked').attr('id');
      const uniqueId = $('#uniqueId').val().trim();
      const password = $('#password').val().trim();

      if (uniqueId && password) {
        $.ajax({
          type: 'POST',
          url: 'login_user.php',
          data: { loginType, uniqueId, password },
          success: function(response) {
            if (response.trim().toLowerCase() !== 'success') {
              $('#loginMessage').html(response);
              
              setTimeout(() => {
                $('#loginMessage').html('');
              }, 4000);
            } else {              
              if (loginType === 'parent_login') {
                window.location.href = 'admin/dashboard';
              } else {
                window.location.href = 'admin/welcomeDashboard';
              }
            }
          },
          error: function() {
            $('#loginMessage').html('<div class="alert alert-danger">Something went wrong. Please try again later.</div>');
          }
        });
      } else {
        $('#loginMessage').html('<div class="alert alert-danger">Please fill both fields!</div>');
      }
    });

    // Trim spaces when pasting or typing
    $('#uniqueId').on('input paste', function() {
      setTimeout(() => {
        let trimmedValue = $(this).val().trim().replace(/\s+/g, ' ');
        $(this).val(trimmedValue);
      }, 0);
    });
  });
</script>
