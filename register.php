<?php
  session_start();
  include 'config.php';
  include 'components/header.php';
?>

  <main class="main">
    <section class="section">
      <div class="container section-title mt-5 pt-5">
        <h2>Registration Form</h2>

        <div class="d-flex justify-content-center">
          <div class="col-lg-8">
            <form id="registrationForm" data-aos="fade-up" data-aos-delay="400">
              <div class="row gy-3 mb-3">
                <div class="col-md-4">
                  <input type="text" name="fname" id="fname" class="form-control" placeholder="Parent/Guardian First Name" required />
                </div>
                <div class="col-md-4">
                  <input type="text" name="mname" id="mname" class="form-control" placeholder="Parent/Guardian Middle Name" />
                </div>
                <div class="col-md-4">
                  <input type="text" name="lname" id="lname" class="form-control" placeholder="Parent/Guardian Last Name" required />
                </div>
              </div>
              
              <!-- Send otp button -->
              <div class="row gy-1 mb-2">
                <div class="col-md-4">
                  <input type="tel" class="form-control" name="phone" id="phone" maxlength="10" placeholder="Mobile Number" required />
                  <div id="phoneMessage"></div>
                </div>
                <div class="col-md-5">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required />
                  <div id="emailMessage"></div>
                </div>
                <div class="col-md-3 col-12">
                  <button type="button" id="sendOtp" class="form-btn">Send OTP</button>
                </div>
              </div>

              <!-- verify button --> 
              <div class="row verifyOtp">
                <div class="col-md-9 col-9">
                  <input type="text" name="otp" id="otp" class="form-control shadow" placeholder="Enter OTP" required />
                </div>
                <div class="col-md-3 col-3">
                  <button type="button" id="verifyOtp" class="form-btn">Verify</button>
                </div>
              </div>              

              <div id="applicantSection">
                <h6 class="text_left_bold mx-1 my-3">Additional Application Details</h6>
                <!-- dynamic application rows will be appended here -->
              </div>
              <button id="addRow" type="button" class="form-btn">+ Add Row</button>

              <!-- display payment section -->
              <div id="priceSection">
                <hr>

                <div style="border-radius: 5px; height: auto; padding: 1px 16px 16px 16px; background-color:#F3E6DC;">
                  <h6 class="text_left_bold">Payment</h6>
                  <div id="paymentDetails"></div>
                  <p id="subtotal" style="font-weight:bold; text-align:right; margin-top: 10px;">Subtotal: ₹0.00</p>
                  <input type="hidden" name="category_sum" id="category_sum">
                </div>
                
                <hr>
              </div>

              <!-- <div class="row"> -->
                <!-- Payment section -->          
                <!-- <div class="form-group" id="priceSection">
                  <label><strong>-- Select Payment Method -- </strong></label>
                  <br />
                  <input type="radio" id="phonepe" name="payment" value="PhonePe" disabled required/> 
                  <label for="phonepe" style="margin-right: 15px; opacity: .8;">PhonePe</label>
                  <input type="radio" id="razorpay" name="payment" value="Razorpay" required /> 
                  <label for="razorpay">Razorpay</label>
                </div> -->

                <!-- display save msg -->
                <div id="registerMessage"></div>
                
                <!-- register button -->
                <button type="submit" id="registerBtn" class="btn" disabled>Register</button>
              <!-- </div> -->
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
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    $(document).ready(function () {
      // Name validation: Allow only letters, spaces, and periods, and auto-capitalize input
      $('#fname, #mname, #lname').on('keypress', function (event) {
        if (!/^[a-zA-Z. ]+$/.test(event.key)) event.preventDefault();
      }).on('input', function () {
        const value = $(this).val().toLowerCase().replace(/\b\w/g, function(char) {
          return char.toUpperCase();
        });
        $(this).val(value);
      });

      // Phone validation (10 digits, starts with 6-9, and no repeated digits)
      $('#phone').on('keypress input', function (event) {
        const phone = $(this).val();
        const isValid = /^[6-9]\d{9}$/.test(phone) && !/^(.)\1{9}$/.test(phone);

        if (event.type === 'keypress' && (!/^\d$/.test(event.key) || phone.length >= 10)) {
          event.preventDefault();
        }

        $(this).toggleClass('green', isValid).toggleClass('red', !isValid);
        $('#phoneMessage').text(isValid ? '' : phone.length < 10 ? 'Enter Valid Mobile Number!' : 'Invalid mobile number!');
      });

      // Email validation
      $('#email').on('input', function () {
        const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($(this).val());
        $(this).toggleClass('green', isValid).toggleClass('red', !isValid);
        $('#emailMessage').text(isValid ? '' : 'Enter a valid email!');
      });
      
      // Send OTP
      $('#sendOtp').click(function () {
        const fname = $('#fname').val().trim();
        const mname = $('#mname').val().trim();
        const lname = $('#lname').val().trim();
        const phone = $('#phone').val().trim();
        const email = $('#email').val().trim();
        const $this = $(this); // Cache the button selector

        if (fname && lname && phone.length === 10 && email.length > 8 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          $this.text('Sending.. (5s)').css('background-color', '#6c757d').prop('disabled', true);
          $('#fname, #mname, #lname, #phone, #email').prop('readonly', true);

          $.post('send_otp', { fname, mname, lname, phone, email }, function(response) {
            if (response === 'Email already registered!') {
              alert(response);
              $('#fname, #mname, #lname, #phone, #email').prop('readonly', false);
            } else if (response) {
              alert(response);
              // $('#otp').focus();
              $this.text('OTP Sent').css('background-color', '#6c757d').prop('disabled', true);
              $('.verifyOtp').slideDown().css('display', 'flex');
              // $('#fname, #lname, #phone, #email').prop('readonly', true);
            } else {
              alert("Failed to send OTP. Please try again.");
              $this.prop('disabled', false).text('Send OTP').css('background-color', '');
              // $('#fname, #lname, #phone, #email').prop('readonly', false);
            }
          }).fail(function() {
            console.log('Error occurred while saving data.');
            $('#fname, #mname, #lname, #phone, #email').prop('readonly', false);
          });

          // Countdown timer logic
          let seconds = 4;
          const timerInterval = setInterval(function () {
            if (seconds <= 0) {
              clearInterval(timerInterval);
              $this.html('Send OTP').css('background-color', '').prop('disabled', false);
            } else {
              $this.html(`Sending.. (${seconds--}s)`);
            }
          }, 1000);
        } else {
          alert("Please fill in all fields.");
          $this.prop('disabled', true);

          setTimeout(() => $this.prop('disabled', false).html('Send OTP').css('background-color', ''), 2000);
        }
      });
      
      $('#otp').on('input click blur', function () {
        $('#sendOtp').text('OTP Sent').css('background-color', '#6c757d').prop('disabled', true);
        
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
      });

      // $('#otp').on('click blur', function () {
      //   $('#sendOtp').text('OTP Sent').css('background-color', '#6c757d').prop('disabled', true);
      // });
      // $('#otp').on('keypress', function(e) {
      //   // Allow only numbers (0-9)
      //   if (e.which < 48 || e.which > 57) {
      //     e.preventDefault();
      //   }
      // });

      // OTP verification
      $('#verifyOtp').click(function () {
        const otp = $('#otp').val().trim();
        const $this = $(this);

        if (/^\d{6}$/.test(otp)) {
          $.post('verify-otp', { otp }, function (response) {
            if (response === 'verified') {
              $this.html('Verified').css('background-color', '#6c757d').prop('disabled', true);
              $('#otp').prop('readonly', true);
              
              // Show the next section
              $('#applicantSection').slideDown();
              $('#addRow').css('display', 'block').slideDown();

              setTimeout(function () {
                $('#registerBtn').prop('disabled', false);
              }, 3000);

            } else {
              alert("Invalid OTP. Please try again.");
              $this.prop('disabled', false).html('Verify');
            }
          });
        } else {
          alert("OTP must be exactly 6 digits.");
        }
      });
      
      // display payment section
      $('.category-select').change(function () {
        $('#priceSection').slideDown();
        setTimeout(function () {  $('#registerBtn').prop('disabled', false);  }, 2000);
      });

      // Event delegation for dynamically added .uniqueId-input elements
      $('#applicantSection').on('blur', '.uniqueId-input', function () {
        const aadhar_uid = $(this).val().trim();
        const $inputField = $(this);

        // if (aadhar_uid.length >= 8 && aadhar_uid.length <= 12) {
          $.post('validate_uniqueId', { aadhar_uid }, function (response) {
            if (response === 'exists') {
              alert("ID '" + aadhar_uid + "' is already in use.");
              $inputField.val('');
            }
          });
        // } else alert("Please Enter Correct Unique ID");
      });

      // Form submission validation
      $('#registrationForm').submit(function (event) {
        event.preventDefault();
        $('#registerBtn').text('Processing.. ').prop('disabled', true);
        $('#addRow').prop('disabled', true);

        const fname = $('#fname').val().trim();
        const mname = $('#mname').val().trim();
        const lname = $('#lname').val().trim();
        const phone = $('#phone').val().trim();
        const email = $('#email').val().trim();
        
        const isPhoneValid = /^\d{10}$/.test(phone);
        const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

        // Phone number validation && Email validation        
        if (!isPhoneValid || !isEmailValid) {
          $('#phoneMessage').text(!isPhoneValid ? 'Exactly 10 digits required!' : '').toggleClass('red', !isPhoneValid).toggleClass('green', isPhoneValid);
          $('#emailMessage').text(!isEmailValid ? 'Enter a valid email!' : '').toggleClass('red', !isEmailValid).toggleClass('green', isEmailValid);
          // $('#registerBtn').text('Register').prop('disabled', false);
          return;
        }

        // Form submission
        $.post('register_user', $(this).serialize(), function(response) { 
          if (response.trim().toLowerCase() === 'success') {
            $('#registrationForm')[0].reset();
            $('#registerMessage').text('');
            $('#registerBtn').text('Registration Successful').prop('disabled', true);

            // Redirect immediately
            window.location.href = 'pay';
          } else {
            $('#registerMessage').html(response);
          }
        }).fail(function() {
          $('#registerMessage').html('<div class="alert alert-danger">Something went wrong. Please try again later.</div>');
          $('#registerBtn').text('Register').prop('disabled', false);
        });
      });
    });

    // document load
    document.addEventListener('DOMContentLoaded', () => {
      let applicantCount = 0;

      function createApplicantRow() {
        applicantCount++;

        const newRow = document.createElement('div');
        newRow.className = 'applicant-row mt-3 p-3 rounded';
        newRow.style.display = 'none'; // Initially hide the row

        newRow.innerHTML = `
          <div class="d-flex justify-content-between">
            <h6 class="applicant-number">Applicant ${applicantCount}</h6>
            <button type="button" class="remove-applicant">×</button>
          </div>
          <div class="row">
            <div class="col-md-4 col-7">
              <label for="applicantName-${applicantCount}">Applicant Name</label>
              <input type="text" name="applicantName[]" id="applicantName-${applicantCount}" class="form-control" placeholder="Enter Name" required />
            </div>
            <div class="col-md-3 col-5">
              <label for="categorySelect-${applicantCount}">Category</label>
              <select name="category[]" id="categorySelect-${applicantCount}" class="form-control category-select" required>
                <option value="" selected disabled>Select Category</option>
                <!-- Categories will be populated from the database -->
              </select>
            </div>
            <div class="col-md-3 col-7">
              <label for="uniqueId-${applicantCount}">Unique ID</label>
              <input type="text" name="uniqueId[]" id="uniqueId-${applicantCount}" class="form-control uniqueId-input" minlength="8" maxlength="12" placeholder="PAN / AADHAR / PASSPORT" required />
              <div class="invalid_id">This ID is already in use!</div>
            </div>
            <div class="col-md-2 col-5">
              <label class="no-select text-center">Amount</label>
              <p class="applicant-amount">₹0.00</p>
            </div>
          </div>
        `;

        // Append the new row and slide it down
        $('#applicantSection').append(newRow);
        $(newRow).fadeIn();

        // Load any dynamic categories or other logic
        loadCategories(newRow.querySelector('.category-select'));
        updateSerialNumbers();
        newRow.querySelector('.uniqueId-input').addEventListener('input', checkAadhaarUniqueness);
      }

      function loadCategories(selectElement) {
        fetch('fetch_categories')
          .then(response => response.json())
          .then(categories => {
            categories.forEach(category => {
              const option = document.createElement('option');
              option.value = category.category_id;
              option.textContent = category.category_name;
              option.dataset.price = category.category_price;
              selectElement.appendChild(option);
            });
          });
      }

      function updateSerialNumbers() {
        document.querySelectorAll('.applicant-number').forEach((element, index) => {
          element.textContent = `Applicant ${index + 1}`;
        });
      }

      function calculateTotalAmount() {
        let totalAmount = 0;

        document.querySelectorAll('.applicant-row').forEach((row) => {
          const amount = parseFloat(row.querySelector('.applicant-amount').textContent.replace('₹', '')) || 0;
          totalAmount += amount;
        });

        document.getElementById('subtotal').textContent = `Subtotal: ₹${totalAmount.toFixed(2)}`;
        $('#category_sum').val(`${totalAmount.toFixed(2)}`);

        // Update payment details section
        let paymentDetails = '';
        document.querySelectorAll('.applicant-row').forEach((row, index) => {
          const amount = row.querySelector('.applicant-amount').textContent;
          paymentDetails += `
            <div class="d-flex justify-content-between">
              <p>Applicant ${index + 1}</p>
              <p>${amount}</p>
            </div>
          `;
        });
        document.getElementById('paymentDetails').innerHTML = paymentDetails;
      }

      // unique id      
      function checkAadhaarUniqueness(e) {
        const enteredAadhaar = e.target.value;
        const allAadhaarInputs = document.querySelectorAll('.uniqueId-input');
        let isDuplicate = [...allAadhaarInputs].some(input => input !== e.target && input.value === enteredAadhaar);

        const errorDiv = e.target.nextElementSibling;
        errorDiv.style.display = isDuplicate ? 'block' : 'none';
        e.target.classList.toggle('is-invalid', isDuplicate);
        document.getElementById('registerBtn').disabled = isDuplicate;
      }

      
      document.getElementById('applicantSection').addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-applicant')) {
          if (document.querySelectorAll('.applicant-row').length > 1) {            
            const rowToRemove = e.target.closest('.applicant-row');
            
            // Slide up the row and remove it after the animation completes
            $(rowToRemove).fadeOut('', function() {
              rowToRemove.remove();
              applicantCount--;
              updateSerialNumbers();
              calculateTotalAmount();
            });
          } else {
            alert('At least one applicant is required.');
          }
        }
      });

      
      document.getElementById('applicantSection').addEventListener('change', (e) => {
        if (e.target.classList.contains('category-select')) {
          const row = e.target.closest('.applicant-row');
          const price = parseFloat(e.target.selectedOptions[0].dataset.price) || 0;
          row.querySelector('.applicant-amount').textContent = `₹${price.toFixed(2)}`;
          calculateTotalAmount();
        }
      });

      // Bind event handler for creating a new applicant row
      document.getElementById('addRow').addEventListener('click', createApplicantRow);

      // Initial applicant row display
      createApplicantRow();
    });
    
  </script>

</body>
</html>