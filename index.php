<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require('inc/links.php')?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <title><?php echo $settings_r['site_title']?> - Home</title>
    <style> 
        .availability-form{
            margin-top: -130px;
            z-index: 2;
            position: relative;
        }
        @media screen and (max-width: 575px) {
        .availability-form{
            margin-top: 25px;
            padding: 0 35px;
        }
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

<!--carousel-->
<div class="container-fluid px-0">
    <div class="swiper swiper-container">
        <div class="swiper-wrapper">
            <?php  
                $res = selectAll('carousel');
                while($row = mysqli_fetch_assoc($res)) {
                    $path = CAROUSEL_IMG_PATH;
                    echo <<<data
                        <div class="swiper-slide">
                            <img src="{$path}{$row['image']}" class="d-block w-100">
                        </div>
                    data;
                }
            ?>
        </div>
    </div>
</div>
<br>

<!-- Check availability -->
<div class="container availability-form">
    <div class="row">
        <div class="col-lg-14 bg-white shadow p-4 rounded">
            <h5 class="mb-4">Check Parking Availability</h5>
            <form action="parkings.php">
                <div class="row align-item-end">
                    <div class="col-lg-3 mb-3">
                        <label class="form-label" style="font-weight: 500;">Check-in</label>
                        <input type="time" class="form-control shadow-none" name="checkin" required>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label class="form-label" style="font-weight: 500;">Check-Out</label>
                        <input type="time" class="form-control shadow-none" name="checkout" required>
                    </div>
                    <div class="col-lg-3 mb-2">
                        <label class="form-label" style="font-weight: 500;">Cars</label>
                        <select class="form-select shadow-none" name="cars">
                            <option value="">Select Cars</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="col-lg-3 mb-2">
                        <label class="form-label" style="font-weight: 500;">Bikes</label>
                        <select class="form-select shadow-none" name="bikes">
                            <option value="">Select Bikes</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <input type="hidden" name="check_availability">
                        </select>
                    </div>
                    <button type="submit" class="btn btn-dark shadow-none check-bg">Check For Parking space</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Our parkings -->
<h2 class="mt-5 pt-4 text-center fw-bold">Our Parking</h2>
<div class="container">
    <div class="row">
        <?php
            $parking_res = select("SELECT * FROM parkings WHERE status=? AND removed=? ORDER BY id DESC LIMIT 3", [1, 0], 'ii');

            while($parking_data = mysqli_fetch_assoc($parking_res)) {
                $fea_q = mysqli_query($con, "SELECT f.name FROM features f INNER JOIN parking_features pfea ON f.id = pfea.features_id WHERE pfea.parking_id = '$parking_data[id]'");
                $features_data = "";
                while($fea_row = mysqli_fetch_assoc($fea_q)) {
                    $features_data .= "<span class='badge rounded-pill bg-dark text-light text-wrap me-1'>$fea_row[name]</span>";
                }

                $fac_q = mysqli_query($con, "SELECT f.name FROM facilities f INNER JOIN parking_facilities pfac ON f.id = pfac.facilities_id WHERE pfac.parking_id = '$parking_data[id]'");
                $facilities_data = "";
                while($fac_row = mysqli_fetch_assoc($fac_q)) {
                    $facilities_data .= "<span class='badge rounded-pill bg-dark text-light text-wrap me-1'>$fac_row[name]</span>";
                }

                $parking_thumb = PARKING_IMG_PATH . "thumbnail.jpg";
                $thumb_q = mysqli_query($con, "SELECT * FROM parking_images WHERE parking_id='$parking_data[id]' AND thumb=1");
                if(mysqli_num_rows($thumb_q) > 0) {
                    $thumb_res = mysqli_fetch_assoc($thumb_q);
                    $parking_thumb = PARKING_IMG_PATH . $thumb_res['image'];
                }

                $book_btn = "";
                if(!$settings_r['shutdown']) {
                    $login = 0;
                    if(isset($_SESSION['login']) && $_SESSION['login'] == true) {
                        $login = 1;
                    }
                    $book_btn = "<button onclick='CheckLoginToBook($login, $parking_data[id])' class='btn btn-sm text-white shadow-none custom-bg'>Book Now</button>";
                }


                $rating_q = "SELECT AVG(rating) AS avg_rating FROM rating_review WHERE parking_id='$parking_data[id]' ORDER BY sr_no DESC LIMIT 20 ";
                
                $rating_res = mysqli_query($con,$rating_q);
                $rating_fetch = mysqli_fetch_assoc($rating_res);
                $rating_data = "";

               if($rating_fetch['avg_rating'] != NULL){
                
                                    for($i = 0 ; $i < $rating_fetch['avg_rating'] ; $i++){
                                        $rating_data.="<i class='bi bi-star-fill text-warning'></i> ";
                                    }

                                    $rating_data.="";
               }
                echo <<<data
                    <div class="col-lg-4 col-md-6 my-3">
                        <div class="card border-6 shadow" style="max-width: 350px; margin: auto;">
                            <img src="$parking_thumb" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5>$parking_data[name]</h5>
                                <h6 class="mb-4">₹$parking_data[price] per Hour</h6>
                                <div class="features mb-4">
                                    <h6 class="mb-1">Features</h6>
                                    $features_data
                                </div>
                                <div class="facilities mb-4">
                                    <h6 class="mb-1">Facilities</h6>
                                    $facilities_data
                                </div>
                                <div class="facilities mb-4">
                                    <h6 class="mb-1">Ratings</h6>
                                     $rating_data 
                                </div>
                               
                                <div class="d-flex justify-content-between mb-2">
                                    $book_btn
                                    <a href="parking_details.php?id=$parking_data[id]" class="btn btn-sm btn-outline-dark shadow-none">More details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                data;
            }
        ?>
        <div class="col-lg-12 text-center mt-5">
            <a href="parkings.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">MORE PARKING >>></a>
        </div>
    </div>
</div>

<!-- Our facility -->
<h2 class="mt-5 pt-4 text-center fw-bold">Our facility</h2>
<div class="container">
    <div class="row justify-content-evenly px-lg-0 px-md-0 px-5">
        <?php 
            $res = mysqli_query($con, "SELECT * FROM facilities ORDER BY id DESC LIMIT 5");
            $path = FACILITIES_IMG_PATH;

            while($row = mysqli_fetch_assoc($res)) {
                echo <<<data
                    <div class="col-mg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3">
                        <img src="{$path}{$row['icon']}" width="60px">
                        <h5 class="mt-3">{$row['name']}</h5>
                    </div>
                data;
            }
        ?>
        <div class="col-lg-12 text-center mt-5">
            <a href="facility.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">MORE FACILITY >>></a>
        </div>
    </div>
</div>

<!-- Testimonials -->
<h2 class="mt-5 pt-4 text-center fw-bold">Testimonial</h2>
<div class="container">
<div class="swiper swiper-testimonial">
        <div class="swiper-wrapper mb-5">
            <?php 
                  // Adjust the query to ensure correct field names
                  $review_q = "SELECT rr.*, uc.name AS uname, uc.profile AS profile, r.name AS rname 
                  FROM rating_review rr
                  INNER JOIN user_cred uc ON rr.user_id = uc.id
                  INNER JOIN parkings r ON rr.parking_id = r.id
                  ORDER BY sr_no DESC LIMIT 6";

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

                        echo <<<data
                            <div class="swiper-slide bg-white p-4 shadow">
                                <div class="profile d-flex align-items-center mb-3">
                                    <img src="$img_path$row[profile]" loadmin="lazy" class="rounded-circle" width="30px" />
                                    <h6 class="m-0 ms-3 mt-2">{$row['uname']}</h6>
                                </div>
                                <p>
                                    {$row['review']}
                                </p>
                                <div class="rating">
                                    $stars
                                </div>
                            </div>
                        data;
                    }
                }
            ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
    <div class="col-lg-12 text-center mt-5">
        <a href="about.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">Know More >>></a>
    </div>
</div>

<!-- Reach us -->
<h2 class="mt-5 pt-4 text-center fw-bold">Reach us</h2>
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-md-8 p-4 mb-lg-0 mb-2">
            <iframe class="w-100 rounded" height="320px" src="<?php echo $contact_r['iframe'] ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="col-lg-4 col-md-4">
            <div class="bg-white p-4 rounded mb-4">
                <h5>Call US</h5>
                <a href="tel:+<?php echo $contact_r['pn1'] ?>" class="d-inline-block mb-2 text-decoration-none text-dark">
                    <i class="bi bi-telephone-fill me-1"></i>+<?php echo $contact_r['pn1'] ?>
                </a>
                <br>
                <?php 
                    if ($contact_r['pn2'] != 0) {
                        echo <<<data
                            <a href="tel:+{$contact_r['pn2']}" class="d-inline-block text-decoration-none text-dark">
                                <i class="bi bi-telephone-fill me-1"></i>+{$contact_r['pn2']}
                            </a>
                        data;
                    }
                ?>
            </div>
            <div class="bg-white p-4 rounded mb-4">
                <h5>Follow Us</h5>
                <?php 
                    if ($contact_r['tw'] != '') {
                        echo <<<data
                            <a href="{$contact_r['tw']}" class="d-inline-block">
                                <span class="badge bg-light text-dark fs-6 mb-2 p-2">
                                    <i class="bi bi-twitter"></i> Twitter
                                </span>
                            </a>
                            <br>
                        data;
                    }
                ?>
                <a href="<?php echo $contact_r['fb'] ?>" class="d-inline-block">
                    <span class="badge bg-light text-dark fs-6 mb-2 p-2">
                        <i class="bi bi-facebook"></i> Facebook
                    </span>
                </a>
                <br>
                <?php 
                    if ($contact_r['insta'] != '') {
                        echo <<<data
                            <a href="{$contact_r['insta']}" class="d-inline-block">
                                <span class="badge bg-light text-dark fs-6 p-2">
                                    <i class="bi bi-instagram"></i> Instagram
                                </span>
                            </a>
                        data;
                    }
                ?>
            </div>
        </div>
    </div>
</div>

<?php require('inc/footer.php')?>


<script>
    var swiper = new Swiper(".swiper-testimonial", {
        effect: "coverflow",
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: "3",
        loop: true,
        coverflowEffect: {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: false,
        },
        pagination: {
            el: ".swiper-pagination",
        },
        breakpoints: {
            320: { slidesPerView: 1 },
            640: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
        }
    });
</script>
</body>
</html>