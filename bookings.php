<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title']?> - BOOKINGs</title>
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

<?php require('inc/header.php'); ?>

<?php

if (!(isset($_SESSION['login'])) && $_SESSION['login'] == true) {
  redirect('parkings.php');
}
?>
<div class="container">
  <div class="row">
    <div class="col-12 my-5 px-4 mb-5">
      <h2 class="fw-bold">BOOKING</h2>
      <div style="font-size: 14px;">
        <a href="index.php" class="text-decoration-none text-secondary">HOME</a>
        <span class="text-secondary"> > </span>
        <a href="#" class="text-decoration-none text-secondary">BOOKINGS</a>
      </div>
    </div>
<?php 
$query = "
  SELECT bp.*, p.name AS parking_name, u.name AS user_name
  FROM `book_parking` bp
  JOIN `parkings` p ON bp.parking_id = p.id
  JOIN `user_cred` u ON bp.user_id = u.id
  WHERE bp.user_id = ?
";
$result = select($query, [$_SESSION['uId']], 'i');
while($data = mysqli_fetch_assoc($result)){
    $current_time = new DateTime();
    $checkin_time = new DateTime($data['check_in']);
    $checkout_time = new DateTime($data['check_out']);
    $status_bg = "";
    $btn = ""; $rating_q = "SELECT `review` FROM `rating_review` WHERE `user_id`='$_SESSION[uId]' ";
                    
    $rating_res = mysqli_query($con,$rating_q);
    $rating_fetch = mysqli_fetch_assoc($rating_res);


    if($data['booking_status'] == 1){
        $status_bg = "bg-success";
        if($current_time < $checkin_time){
            $btn.= "
              <button type='button' class='btn btn-warning shadow-none' onclick='review_parking($data[booking_id],$data[parking_id])' data-bs-toggle='modal' data-bs-target='#reviewModal'>
                    Rate And Review
                    </button>
              <button type='button' onclick='remove_booking({$data['booking_id']})' class='btn btn-danger shadow-none'>Cancel bookings</button>";
        } else if($checkout_time < $current_time){
          $btn.= "<button type='button' onclick='review_parking($data[booking_id],$data[parking_id])' class='btn btn-warning shadow-none' data-bs-toggle='modal' data-bs-target='#reviewModal'>
                    Rate And Review
                    </button>";
        }else{
            $btn.= "<button type='button' class='btn btn-danger shadow-none'>Cancel bookings</button>";
        }
    } else {
        $status_bg = "bg-dark";
    }

    echo <<<bookings
      <div class='col-md-4 px-4 mb-4'>
        <div class='bg-white p-3 rounded shadow-sm '>
          <h5 class='fw-bold'>Parking: {$data['parking_name']}</h5>
          <p>User: {$data['user_name']}</p>
          <p>Slot : {$data['booking_id']}</p>
          <p>Check-in: {$checkin_time->format('Y-m-d H:i:s')}</p>
          <p>Check-out: {$checkout_time->format('Y-m-d H:i:s')}</p>
          $btn
        </div>
      </div>
    bookings;
}
?>

<div class="modal fade" id="reviewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form id="review-form">
        <div class="modal-header">
            <h5 class="modal-title d-flex align-item-center" >
            <i class="bi bi-chat-square fs-5 me-2" ></i>  Rate And Review
        </h5>
            <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
        <div class="modal-body">
          
            <div class="mb-3">
                <label class="form-label">Rating</label>
                <select class="form-select shadow-none" name="rating">
                  <option selected>Open this select menu</option>
                  <option value="5">Excellent</option>
                  <option value="4">Good</option>
                  <option value="3">Ok</option>
                  <option value="2">Poor</option>
                  <option value="3">Bad</option>
                </select>
                
            </div>

            <div class="mb-4">
                <label class="form-label">Review</label>
                <textarea type="text" name="review" required class="form-control shadow-none" ></textarea>
            </div>
            <input type="hidden" name="booking_id">
            <input type="hidden" name="parking_id">
            <div class="text-end">
                <button type="submit"class="btn btn-dark shadow-none ">SUBMIT</button>
            </div>
  
        </div>
       
    </form>
      
    </div>
  </div>
</div>

  </div>
</div>

<script>
  function remove_booking(booking_id){
    if(confirm("Are You Sure , You Want To Delete This Booking?")) {
        let data = new FormData();
        data.append('booking_id', booking_id);
        data.append('remove_booking', '');
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/confirm_bookings.php", true);
            
        xhr.onload = function() {
            if(this.responseText == 1){
                location.reload();          
            } else {
                console.log(this.responseText);
            }
        }
        xhr.send(data);
    }
  }

let review_form =document.getElementById('review-form');

  function review_parking(bid,rid){
      review_form.elements['booking_id'].value = bid;
      review_form.elements['parking_id'].value = rid;
  }

  review_form.addEventListener('submit',function(e){
    e.preventDefault();

    
    let data = new FormData();
    data.append('review_form', '');
    data.append('rating', review_form.elements['rating'].value);
    data.append('review', review_form.elements['review'].value);
    data.append('booking_id', review_form.elements['booking_id'].value);
    data.append('parking_id',review_form.elements['parking_id'].value);
    

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/review_parking.php", true);
    
    xhr.onload = function() {
       if(this.responseText == 1){

        window.location.href = 'bookings.php?review_status=true'
        
       }else{
        var myModal = document.getElementById('reviewModal');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();
        console.log('ratring failed');
        
       }
    }

    xhr.send(data);
  });


</script>

<?php require('inc/footer.php')?>

</body>
</html>
