<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require('inc/links.php')?>
    <title><?php echo $settings_r['site_title']?> - Facility</title>
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
    <h2 class="text-center fw-bold">Our Facility</h2>
    <div class="h-line bg-dark"></div>
    <p class="text-center mt-3">The below mentioned are some of our 
        facilities to make your parking experience better.<br>
       Hope you have a happy shopping!!!.</p>
</div>


<div class="container">
    <div class="row">
    <?php 
        $res = selectAll('facilities');
        $path = FACILITIES_IMG_PATH;

        while($row =mysqli_fetch_assoc($res)){
            echo <<< data
                <div class="col-lg-4 col-md-6 mb-5 px-4">
                <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop">
                    <div class="d-flex align-items-center mb-2">
                        <img src="$path$row[icon]" width="40px">
                        <h5 class="fw-bold m-0 ms-2 ">$row[name]</h5>
                    </div>
                    <p>$row[desc]</p>
                </div>
            </div>
            data;

        }
        ?>

    </div>
</div>

<?php require('inc/footer.php')?>

</body>
</html>