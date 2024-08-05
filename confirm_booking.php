<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php 
  require('inc/links.php');
  
  ?>
  <title><?php echo $settings_r['site_title']?> - CONFIRM BOOKING</title>
  <link rel="stylesheet" href="css/common.css">
   
  <style>
    .pop:hover {
      border-top-color: var(--blue-hover) !important;
      transform: scale(1.03);
      transition: 0.3s;
    }
    .check-bg {
      background-color: black;
      border: 1px solid black;
    }
    .check-bg:hover {
      background-color: rgb(0, 76, 255);
      border: 1px solid rgb(0, 76, 255);
    }
    .custom-bg {
      background-color: blue;
      border: 1px blue;
    }
    .custom-bg:hover {
      background-color: black;
      border: 1px solid black;
    }
  </style>
</head>
<body class="bg-light">

<?php require('inc/header.php');?>

<?php
if (!isset($_GET['id']) || $settings_r['shutdown'] == true) {
  redirect('parkings.php');
} else if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
  redirect('parkings.php');
}

// If the form is submitted, handle the booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $userId = $_SESSION['uId'];
  $parkingId = $_SESSION['parking']['id'];
  $checkIn = $_POST['checkin'];
  $checkOut = $_POST['checkout'];
}

$data = filteration($_GET);

$parking_res = select("SELECT * FROM `parkings` WHERE `id`=? AND `status`=? AND `removed`=?", [$data['id'], 1, 0], 'iii');

if (mysqli_num_rows($parking_res) == 0) {
  redirect('parkings.php');
}

$parking_data = mysqli_fetch_assoc($parking_res);

$_SESSION['parking'] = [
  "id" => $parking_data['id'],
  "name" => $parking_data['name'],
  "price" => $parking_data['price'],
  "payment" => null,
  "available" => false,
];

$user_res = select("SELECT * FROM `user_cred` WHERE `id` = ? LIMIT 1", [$_SESSION['uId']], "i");
$user_data = mysqli_fetch_assoc($user_res);
?>

<div class="container">
  <div class="row">
    <div class="col-12 my-5 px-4 mb-4">
      <h2 class="fw-bold">CONFIRM BOOKING</h2>
      <div style="font-size: 14px;">
        <a href="index.php" class="text-decoration-none text-secondary">HOME</a>
        <span class="text-secondary"> > </span>
        <a href="parkings.php" class="text-decoration-none text-secondary">PARKINGS</a>
        <span class="text-secondary"> > </span>
        <a href="#" class="text-decoration-none text-secondary">CONFIRM</a>
      </div>
    </div>

    <div class="col-lg-7 col-mg-12 px-4">
      <?php 
        $parking_thumb = PARKING_IMG_PATH . "thumbnail.jpg";
        $thumb_q = mysqli_query($con, "SELECT * FROM `parking_images` WHERE `parking_id`='$parking_data[id]' AND `thumb`=1");

        if (mysqli_num_rows($thumb_q) > 0) {
          $thumb_res = mysqli_fetch_assoc($thumb_q);
          $parking_thumb = PARKING_IMG_PATH . $thumb_res['image'];
        }

        echo <<<data
        <div class="card p-3 shadow-sm rounded">
          <img src="$parking_thumb" class="img-fluid rounded mb-3">
          <h5>$parking_data[name]</h5>
          <h6>₹ $parking_data[price] Per Hour</h6>
        </div>
        data;
      ?>
    </div>

    <div class="col-lg-5 col-md-12 px-4">
      <div class="card mb-4 border-0 shadow-sm rounded-3">
        <div class="card-body">
          <form action="" id="booking_form" method="post">
            <h6 class="mb-2">BOOKING DETAILS</h6>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="<?php echo $user_data['name']?>" class="form-control shadow-none">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-labe mb-2">Phone Number</label>
                <input type="phonenum" name="phonenum" value="<?php echo $user_data['phonenum']?>" class="form-control shadow-none">
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-labe mb-2">Address</label>
                <textarea name="address" class="form-control shadow-none" rows="1" required><?php echo $user_data['address']?></textarea>
              </div>
              <div class="col-md-6 mb-4">
                <label class="form-labe mb-2">Check-In</label>
                <input type="time" name="checkin" onchange="check_availability()" class="form-control shadow-none">
              </div>
              <div class="col-md-6 mb-4">
                <label class="form-labe mb-2">Check-Out</label>
                <input type="time" name="checkout" onchange="check_availability()" class="form-control shadow-none">
              </div>
              <div class="col-12">
                <div class="spinner-border text-primary mb-3 d-none" id="info_loader" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <h6 class="mb-3 text-danger" id="pay_info">Provide Check-In And Check-Out Time!</h6>
                <button name="pay_now"  type="submit" class="btn w-100 text-white check-bg shadow-none mb-1" disabled>Pay Now</button>
              </div>
            </div>
          </form>
          

        </div>
      </div>
    </div>
  </div>
  <div class="col-12 mt-4 px-4">
            <div class="mb-5">
                <h5 class="fw-bold">Description</h5>
                <p>
                    <?php
                        echo $parking_data['description'];
                    
                    ?>
                </p>
            </div>
            <div class="">
                    <h5 class="mb-3 fw-bold">Reviews & Ratings</h5>

                    <?php 
                    
                      // Adjust the query to ensure correct field names
                  $review_q = "SELECT rr.*, uc.name AS uname, uc.profile AS profile, r.name AS rname 
                  FROM rating_review rr
                  INNER JOIN user_cred uc ON rr.user_id = uc.id
                  INNER JOIN parkings r ON rr.parking_id = r.id
                  WHERE rr.parking_id = '$parking_data[id]'
                  ORDER BY sr_no DESC LIMIT 15";

                $review_res = mysqli_query($con, $review_q);
                $img_path = USERS_IMG_PATH;
                if (mysqli_num_rows($review_res) == 0) {
                    echo 'No reviews yet!';
                } else {
                   
                        while ($row = mysqli_fetch_assoc($review_res)) {

                            $stars = "";
                            for ($i = 0; $i < $row['rating']; $i++) {
                                $stars .= " <i class='bi bi-star-fill text-warning'></i>";
                            }
    
                         echo <<< reviews
                            <div class=" d-flex align-item-center mb-2">
                            <img src="$img_path$row[profile]" width="30px" />
                            <h6 class="m-0 ms-3 mt-2">{$row['uname']}</h6>
                        </div>
                    
                        <p class=mb-1>
                            {$row['review']}
                        </p>
            
                        <div class="rating mb-3">
                            $stars
                        </div>
                        
                    
                    reviews;
                }}
                    ?>
                   
            </div>
        </div>
</div>


<?php require('inc/footer.php')?>
<script>




let booking_form = document.getElementById('booking_form');
let info_loader = document.getElementById('info_loader');
let pay_info = document.getElementById('pay_info');

function check_availability() {
    let checkin_val = booking_form.elements['checkin'].value;
    let checkout_val = booking_form.elements['checkout'].value;

    booking_form.elements['pay_now'].setAttribute('disabled', true);

    if (checkin_val != '' && checkout_val != '') {
        let data = new FormData();

        data.append('check_availability', '');
        data.append('check_in', checkin_val);
        data.append('check_out', checkout_val);

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/confirm_bookings.php", true);

        xhr.onload = function () {
            console.log(this.responseText);  // Log the response for debugging
            try {
                let data = JSON.parse(this.responseText);

                if (data.status == 'available') {
                    pay_info.classList.remove('text-danger');
                    pay_info.classList.add('text-success');
                    pay_info.innerText = `Parking Available...! \n Total Hours: ${data.hours} \n Payment: ₹${data.payment}`;
                    booking_form.elements['pay_now'].removeAttribute('disabled');
                } else {
                    pay_info.classList.add('text-danger');
                    pay_info.classList.remove('text-success');
                    if (data.status == 'check_in_out_equal') {
                        pay_info.innerText = 'Check-in and Check-out times cannot be the same!';
                    } else if (data.status == 'check_out_earlier') {
                        pay_info.innerText = 'Check-out time cannot be earlier than Check-in time!';
                    } else if (data.status == 'check_in_earlier') {
                        pay_info.innerText = 'Check-in time cannot be earlier than the current time!';
                    } else {
                        pay_info.innerText = 'Booking is not available for the selected time!';
                    }
                }
            } catch (e) {
                console.error('Parsing error:', e);  // Log the error
                pay_info.innerText = 'An error occurred. Please try again later.';
            }
            info_loader.classList.add('d-none');
        };

        info_loader.classList.remove('d-none');
        xhr.send(data);
    } else {
        pay_info.innerText = 'Provide Check-In and Check-Out Time!';
    }
}

booking_form.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
    add_booking(); // Call the function to handle the booking
});


function add_booking() {
    const booking_form = document.getElementById('booking_form');
    
    let data = new FormData();
    data.append('user_id', <?php echo $_SESSION['uId']; ?>);
    data.append('parking_id', <?php echo $_SESSION['parking']['id']; ?>);
    data.append('check_in', booking_form.elements['checkin'].value);
    data.append('check_out', booking_form.elements['checkout'].value);
    data.append('add_booking', ''); // Add a flag to indicate the purpose of the request

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/confirm_bookings.php', true);

    xhr.onload = function() {
        if (this.responseText == 1) {
            console.log('Booking successful');
            window.location.href = 'booked.php'; 
        } else {
            console.log('Booking failed');
        }
    };

    xhr.send(data);
}



</script>
</body>
</html>
