<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require('inc/links.php')?>
    <title><?php echo $settings_r['site_title']?> - About</title>
  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="css/common.css"> 
    <style>
        .pop:hover{
            border-top-color: var(--blue-hover) !important;
            transform: scale(1.03);
            transition: 0.3s;

        }
       
        .h-line{
    width: 150px;
    margin: 0 auto;
    height: 1.7px;
}
    </style>
  
</head>
<body class="bg-light">

<?php require('inc/header.php');?>

<div class="my-5 px-4">
    <h2 class="text-center fw-bold">About Us</h2>
    <div class="h-line bg-dark"></div>
    <p class="text-center mt-3">This site is for booking slots
        in the shopping center for convenient parking  <br>
        </p>
</div>

<div class="container  ">
    <div class="row justify-content-between align-items-center">
        <div class="col-lg-6 col-md-5 mb-4 order-lg-1 order-mb-1 order-2">
            <h3 class="mb-3">ABOUT US</h3>
            <p>Our parking system gives you the best way to park your vehicle
                without any frustration or stress by having a pre-booking method
                and also the features and facilities that are available at the shopping center.
            </p>
        </div>
        <div class="col-lg-5 col-md-5 mb-4 order-lg-2 order-mb-2 order-1">
            <img src="/Parkin/img/about/about.jpg" class="w-100">
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4 px-4">
            <div class="bg-white rounded shadow p-4 border-top border-4 text-center pop">
                <img src="/Parkin/img/about/parking.png" width="70px">
                <h4 class="mt-3">100+ Parkings</h4>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 px-4">
            <div class="bg-white rounded shadow p-4 border-top border-4 text-center pop">
                <img src="/Parkin/img/about/customers.png" width="70px">
                <h4 class="mt-3">200+ Customers</h4>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 px-4">
            <div class="bg-white rounded shadow p-4 border-top border-4 text-center pop ">
                <img src="/Parkin/img/about/city.png" width="70px">
                <h4 class="mt-3">10+ Cities</h4>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 px-4">
            <div class="bg-white rounded shadow p-4 border-top border-4 text-center pop">
                <img src="/Parkin/img/about/review.png" width="70px">
                <h4 class="mt-3">4.5 Reviews</h4>
            </div>
        </div>


    </div>
</div>

<h3 class="my-5 fw-bold text-center">MANAGEMENT TEAMS</h3>
<div class="container px-4">
<div class="swiper mySwiper">
    <div class="swiper-wrapper mb-5">
      <?php 
        $about_r = selectAll('team_details');
        $path = ABOUT_IMG_PATH;

        while($row = mysqli_fetch_assoc($about_r)){
            echo <<< data
            <div class="swiper-slide bg-white text-center overflow-hiddent rounded">
                <img src="$path$row[picture]" class="w-100">
                <h5 class="mt-2">$row[name]</h5>
            </div>
            data;
        }
      
      ?>

    </div>
   <div class="swiper-pagination"></div>
  </div>
</div>

<?php require('inc/footer.php')?>

<script>
     var swiper = new Swiper(".mySwiper", {
    slidesPerView : 4,
    spaceBetween : 40,
    
      pagination: {
        el: ".swiper-pagination",
        dynamicBullets: true,
      },
      breakpoints:{
     320:{
        slidesPerView:1,
      },
      640:{
        slidesPerView:1,
      },
      768:{
        slidesPerView:2,
      },
      1024:{
        slidesPerView:3,
      },
    }
    });
</script>
</body>
</html>