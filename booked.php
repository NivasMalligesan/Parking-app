<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php 
  require('inc/links.php');
  ?>
  <title><?php echo $settings_r['site_title']?> - BOOKING CONFIRMED</title>
  <link rel="stylesheet" href="css/common.css">
  <style>
    .confirmation-bg {
      background-color: #d4edda;
      border: 1px solid #c3e6cb;
    }
    .confirmation-text {
      color: #155724;
    }
  </style>
</head>
<body class="bg-light">

<?php 
require('inc/header.php');

$booking = $_SESSION['booking'];

// Define the query to fetch booking details along with parking name and user name
$query = "
  SELECT bp.*, p.name AS parking_name, u.name AS user_name
  FROM `book_parking` bp
  JOIN `parkings` p ON bp.parking_id = p.id
  JOIN `user_cred` u ON bp.user_id = u.id
  WHERE bp.user_id = ?
";
// Execute the query with the user ID parameter
$result = select($query, [$_SESSION['uId']], 'i');

// Initialize booking ID
$booking_id = null;

while($data = mysqli_fetch_assoc($result)){
  $booking_id = $data['booking_id'];
}
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card confirmation-bg">
        <div class="card-body">
          <h3 class="card-title text-center confirmation-text">Booking Confirmed!</h3>
          <p class="card-text text-center confirmation-text">Thank you for your booking.</p>
          <hr>
          <h5 class="card-subtitle mb-2">Booking Details:</h5>
          <p><strong>Parking Name:</strong> <?php echo $booking['name']; ?></p>
          <p><strong>Parking Slot:</strong> <?php echo $booking_id; ?></p>
          <p><strong>Check-In Time:</strong> <?php echo $booking['check_in']; ?></p>
          <p><strong>Check-Out Time:</strong> <?php echo $booking['check_out']; ?></p>
          <p><strong>Total Hours:</strong> <?php echo $booking['hours']; ?></p>
          <p><strong>Total Payment:</strong> ₹<?php echo $booking['payment']; ?></p>
          <div class="text-center mt-4">
            <a href="bookings.php" class="btn btn-success">View My Bookings</a>
            <a href="parkings.php" class="btn btn-primary">Book Another Parking</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php 
require('inc/footer.php');
?>

</body>
</html>
