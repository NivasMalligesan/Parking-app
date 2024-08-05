<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title']?> - PROFILE</title>
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

$u_exist = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1",[$_SESSION['uId']],'s');

if(mysqli_num_rows($u_exist)==0){
  redirect('index.php');
}

$u_fetch = mysqli_fetch_assoc($u_exist);

?>
<div class="container">
  <div class="row">
    <div class="col-12 my-5 px-4 mb-5">
      <h2 class="fw-bold">PROFILE</h2>
      <div style="font-size: 14px;">
        <a href="index.php" class="text-decoration-none text-secondary">HOME</a>
        <span class="text-secondary"> > </span>
        <a href="#" class="text-decoration-none text-secondary">PROFILE</a>
      </div>
    </div>
    <div class="col-12 my-5 px-4 mb-5">
      <div class="bg-white p-3 p-md-4 rounded shadow-sm">
        <form id="info-form">
              <h5 class="mb-3 fw-bold">Basic Information</h5>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" name="name" value="<?php echo $u_fetch['name']?>" class="form-control shadow-none" required >
                  </div>
                  <div class="col-md-4 mb-3">
                  <label class="form-label">Phone</label>
                  <input type="number" name="phonenum" value="<?php echo $u_fetch['phonenum']?>" class="form-control shadow-none" required >
                  </div>
                  <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input name="dob" type="date" value="<?php echo $u_fetch['dob']?>" class="form-control shadow-none" required>
                  </div>
                  <div class="col-md-4 mb-3">
                            <label class="form-label">Pin code</label>
                            <input name="pincode" value="<?php echo $u_fetch['pincode']?>" type="number" class="form-control shadow-none" required>
                  </div>
                  <div class="col-md-8 mb-4">
                            <label class="form-label">Address</label>
                            <textarea name="address"  class="form-control shadow-none"rows="1" required><?php echo $u_fetch['address']?></textarea>
                  </div>
                </div>
                <button type="submit" class="btn text-white check-bg shadow-none">Save Changes</button>
              </div>
        </form>
      </div>  
      
      <div class="col-md-4 my-5 px-4 mb-5">
      <div class="bg-white p-3 p-md-4 rounded shadow-sm">
        <form id="profile-form">
              <h5 class="mb-3 fw-bold">PROFILE</h5>
              <img src="<?php echo USERS_IMG_PATH.$u_fetch['profile'] ?>" class=" rounded-circle img-fluid mb-3 shadow-sm" >
                   <label class="form-label">New Profile Picture</label>
                    <input name="profile" accept=".jpg , .jpej , .png , .webp" type="file" class="form-control shadow-none mb-4" required>
                       
             
                <button type="submit" class="btn text-white check-bg shadow-none">Save Changes</button>
              </div>
        </form>
      </div> 

      <div class="col-md-8 my-5 px-4 mb-5">
      <div class="bg-white p-3 p-md-4 rounded shadow-sm">
        <form id="pass-form">
          <div class="row">
          <h5 class="mb-3 fw-bold">Change Password</h5>
              <div class="col-md-6 mb-4">
                  <label class="form-label">New Password</label>
                  <input type="password" name="new_pass"  class="form-control shadow-none" required >
              </div>
              <div class="col-md-6 mb-4">
                  <label class="form-label">Confirm Password</label>
                  <input type="password" name="confirm_pass"  class="form-control shadow-none" required >
              </div>
          </div>          
             
                <button type="submit" class="btn text-white check-bg shadow-none">Save Changes</button>
              </div>
        </form>
      </div> 


    </div>
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
    $btn = "";

    if($data['booking_status'] == 1){
        $status_bg = "bg-success";
        if($current_time < $checkin_time){
            $btn = "
              <button type='button' class='btn btn-warning shadow-none'>Rate And Reviews</button>
              <button type='button' onclick='remove_booking({$data['booking_id']})' class='btn btn-danger shadow-none'>Cancel bookings</button>";
        } else if($checkout_time < $current_time){
          $btn = "<button type='button' class='btn btn-warning shadow-none'>Rate And Reviews</button>";
        }else{
            $btn = "<button type='button' class='btn btn-danger shadow-none'>Cancel bookings</button>";
        }
    } else {
        $status_bg = "bg-dark";
    }

    echo <<<bookings
      <div class='col-md-4 px-4 mb-4'>
        <div class='bg-white p-3 rounded shadow-sm $status_bg'>
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
              console.log(this.responseText);         
            } else {
                console.log(this.responseText);
            }
        }
        xhr.send(data);
    }
  }

  let info_form = document.getElementById('info-form');
  

  info_form.addEventListener('submit', function(e) {
    e.preventDefault();

    let data = new FormData();
    data.append('info_form', '');
    data.append('name', info_form.elements['name'].value);
    data.append('phonenum', info_form.elements['phonenum'].value);
    data.append('address', info_form.elements['address'].value);
    data.append('pincode', info_form.elements['pincode'].value);
    data.append('dob', info_form.elements['dob'].value);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/profile.php", true);
    
    xhr.onload = function() {
        if (this.responseText == 'phone_already') {
            console.log('phone already there');
        } else if (this.responseText == 0) {
            console.log('NO changes made');
        } else {
            console.log('changes Made');
            window.location.href =window.location.pathname;
            
        }
    }

    xhr.send(data);
  });


  let profile_form = document.getElementById('profile-form');

profile_form.addEventListener('submit', function(e) {
    e.preventDefault();

    let data = new FormData();
    data.append('profile_form', '');
    data.append('profile', profile_form.elements['profile'].files[0]); // Corrected typo

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/profile.php", true);

    xhr.onload = function() {
        if (this.responseText === 'inv_img') {
            console.log('error', "Only JPG, WEBP & PNG images are allowed");
        } else if (this.responseText === 'upd_failed') {
            console.log('error', "Image upload failed");
        } else if (this.responseText == 0) {
            console.log('Error, No changes Done');
        } else {
            console.log('success');
            window.location.href =window.location.pathname;
        }
    }

    xhr.send(data);
});

let pass_form = document.getElementById('pass-form');

pass_form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    let new_pass = pass_form.elements['new_pass'].value;
    let confirm_pass = pass_form.elements['confirm_pass'].value;

    if(new_pass != confirm_pass){
      console.log('pass mismatch');
      return false;
    }


    let data = new FormData();
    data.append('pass_form', '');
    data.append('new_pass', new_pass);
    data.append('confirm_pass',confirm_pass);
    

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/profile.php", true);
    
    xhr.onload = function() {
        if (this.responseText == 'mismatch') {
            console.log('password not matching');
        } else if (this.responseText == 0) {
            console.log('NO changes made');
        } else {
            console.log('changes Made');
            
            
        }
    }

    xhr.send(data);
  });
 


</script>

<?php require('inc/footer.php')?>

</body>
</html>
