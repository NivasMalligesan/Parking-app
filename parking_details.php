<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require('inc/links.php')?>
    <title><?php echo $settings_r['site_title']?> - Details</title>
    <link rel="stylesheet" href="css/common.css"> 
    <style>
        .pop:hover{
            border-top-color: var(--blue-hover) !important;
            transform: scale(1.03);
            transition: 0.3s;
        }
        .check-bg{
          background-color: black;
          border: 1px solid black;
      }
      .check-bg:hover{
          background-color: rgb(0, 76, 255);
          border: 1px solid rgb(0, 76, 255);
      }
     


.custom-bg{
         background-color: blue;
          border: 1px blue;
}

.custom-bg:hover{
          background-color: black;
          border: 1px solid black;
      
      }


    </style>
  
</head>
<body class="bg-light">

<?php require('inc/header.php');?>

<?php

if(!isset($_GET['id'])){
    redirect('parkings.php');
}

$data = filteration($_GET);

$parking_res = select("SELECT * FROM `parkings` WHERE `id`=? AND `status`=? AND `removed`=? ",[$data['id'],1,0],'iii');

if(mysqli_num_rows($parking_res)==0){
    redirect('parkings.php');
}

$parking_data = mysqli_fetch_assoc($parking_res);


?>



<!--filters--->
<div class="container">
    <div class="row">


    <div class="col-12 my-5 px-4 mb-4">
        <h2 class="fw-bold"><?php echo $parking_data['name']?></h2>
        <div style="font-size : 14px;">
            <a href="index.php" class="text-decoration-none text-secondary">HOME</a>
            <span class="text-secondary"> > </span>
            <a href="parkings.php"  class="text-decoration-none text-secondary">PARKINGS</a>
            <span class="text-secondary"> > </span>


        </div>
        
    </div>

    <div class="col-lg-7 col-mg-12 px-4">
    <div id="parkingCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
                $parking_img = PARKING_IMG_PATH."thumbnail.jpg";
                $img_q = mysqli_query($con,"SELECT * FROM `parking_images` WHERE `parking_id`='$parking_data[id]'");

                if(mysqli_num_rows($img_q)>0){
                    $active_class = 'active';

                    while($img_res = mysqli_fetch_assoc($img_q)){
                    echo"
                    <div class='carousel-item $active_class'>
                        <img src='".PARKING_IMG_PATH.$img_res['image']."' class='d-block w-100'>
                    </div>
                    ";
                    $active_class ='';
                }
                }else{
                    echo"
                    <div class='carousel-item active'>
                    <img src='$parking_img' class='d-block w-100 rounded'>
                    </div>";
                }
            
            
            ?>
           
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#parkingCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#parkingCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        </div>


    </div>
    
    <div class="col-lg-5 col-md-12 px-4" >
        <div class="card mb-4 border-0 shadow-sm rounded-3">
                <div class="card-body">
                   <?php 
                   
                   echo <<< price
                        <h4>₹$parking_data[price] per Hour</h4>
                   price;

                   $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review` WHERE `parking_id`='$parking_data[id]' ORDER BY `sr_no` DESC LIMIT 20 ";
                
                   $rating_res = mysqli_query($con,$rating_q);
                   $rating_fetch = mysqli_fetch_assoc($rating_res);
                   $rating_data = "";
   
                  if($rating_fetch['avg_rating'] != NULL){
                     for($i = 0 ; $i < $rating_fetch['avg_rating'] ; $i++){
                                           $rating_data.="<i class='bi bi-star-fill text-warning'></i> ";
                                       }
                  }

                   echo <<< rating
                    <div class="rating mb-3">
                            $rating_data
                        </div>
                   rating;

                                          
                    $fea_q =  mysqli_query($con,"SELECT f.name FROM `features` f INNER JOIN `parking_features` pfea ON f.id = pfea.features_id WHERE pfea.parking_id = '$parking_data[id]'");
    
                    $features_data = "";
                    while($fea_row = mysqli_fetch_assoc($fea_q)){
                                $features_data.="<span class='badge rounded-pill bg-dark text-light text-wrap me-1 me-1 mb-1 '>
                                        $fea_row[name]
                        </span>";
                    }

                    echo <<< features

                    <div class="features mb-3">
                            <h6 class="mb-1">Features</h6>
                            $features_data
                    </div>
                    features;

                    $fac_q = mysqli_query($con,"SELECT f.name FROM `facilities` f INNER JOIN `parking_facilities` pfac ON f.id = pfac.facilities_id WHERE pfac.parking_id = '$parking_data[id]' ");
                    $facilities_data = "";
    
                    while($fac_row = mysqli_fetch_assoc($fac_q)){
                        $facilities_data.="<span class='badge rounded-pill bg-dark text-light text-wrap me-1 mb-1'>
                              $fac_row[name]
                               </span>";
                    }

                    echo <<< facilities
                        <div class="features mb-3">
                            <h6 class="mb-1">Facility</h6>
                            $facilities_data
                        </div>
                    facilities;
    
                    echo <<< floor
                    <div class="mb-3">
                            <h6 class="mb-1">Floor</h6>
                            <span class='badge rounded-pill bg-dark text-light text-wrap me-1 mb-1'>
                            $parking_data[floor] th Floor
                            </span>
                    </div>

                    floor;

                    $book_btn = "";

                    if(!$settings_r['shutdown']){
                        $login = 0 ;
                    if(isset($_SESSION['login']) && $_SESSION['login']==true){
                       $login = 1; 
                    }

                        $book_btn = "<button onclick='CheckLoginToBook($login,$parking_data[id])' class='btn w-100 text-white shadow-none custom-bg '>Book Now</button>";
                    }

                   
                    echo <<< book
                       $book_btn
                    book;
                   
                   ?> 
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
</div>

<?php require('inc/footer.php')?>

</body>
</html>