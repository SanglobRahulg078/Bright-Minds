<?php
  session_start();
	include 'config.php';

  // Redirect to login if any session data is missing
  if (!isset($_SESSION['application_id'], $_SESSION['price'])) {
    header("Location: password-reset");
    exit();
  }

  // Retrieve session data
  $appId = $_SESSION['application_id'] ?? '';
  $email = $_SESSION['email'] ?? '';
  $phone = $_SESSION['phone'] ?? '';
  $price = $_SESSION['price'] ?? '';
?>

<?php include 'components/header.php'; ?>

<!-- jQuery and Razorpay script -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
  // Dynamic data from PHP session
  var appId = <?php echo json_encode($appId); ?>;
  var email = <?php echo json_encode($email); ?>;
  var phone = <?php echo json_encode($phone); ?>;
  var amount = <?php echo json_encode($price); ?>;

  // Initiate payment process
  $(document).ready(function() {
    initiatePayment();
  });

  function initiatePayment() {  
    $.post('payment_process.php', { amount: amount, email: email, appId: appId, phone: phone }, function(response) {
      var options = {
        "key": "rzp_live_oClM7IjmTi98Hd", // Razorpay live key
        "amount": amount * 100, // Razorpay processes amounts in paise (INR)
        "currency": "INR",
        "name": "Bright Minds",
        "description": "Bright Minds Transaction",
        "image": "assets/img/logo.png",
        "handler": function(response) {
          // Capture payment ID and process it in payment_process.php
          processPayment(response.razorpay_payment_id);
        },
        "prefill": {
          "email": email,
          "contact": phone
        },
        "theme": {
          "color": "#3399cc"
        },
        "method": {
          "amazonpay": false,
          "netbanking": true,
          "card": true,
          "upi": true,
          "wallet": false  // Allow other wallets (like Paytm, Mobikwik, etc.)
        },
        "modal": {
          "escape": false, // Prevents modal from being closed using escape key
          "ondismiss": function() {
            // Redirect to registration page if user cancels or closes the modal
            window.location.href = "login";
          }
        }
      };

      var rzp1 = new Razorpay(options);
      rzp1.open();

    }).fail(function() {
      alert("Payment initiation failed, please try again.");
      console.log('Payment initiation failed, please try again.');
      window.location.href = "login"; // Redirect to login if payment initiation fails
    });
  }

  // Post-payment process function
  function processPayment(payment_id) {
    $.ajax({
      type: 'POST',
      url: 'payment_process.php',
      data: { payment_id: payment_id, appId: appId },
      success: function(result) {
        window.location.href = "payment_success?appId=" + appId; // Redirect on successful payment
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.error("Error processing payment:", textStatus, errorThrown);
        alert("Payment failed, please try again.");
        window.location.href = "login"; // Redirect to login if payment fails
      }
    });
  }
</script>

</body>
</html>