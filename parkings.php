<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require('inc/links.php')?>
    <title><?php echo $settings_r['site_title']?> - Parkings </title>
    <link rel="stylesheet" href="css/common.css"> 
    <style>
        .check-bg {
            background-color: black;
            border: 1px solid black;
        }
        .check-bg:hover {
            background-color: rgb(0, 76, 255);
            border: 1px solid rgb(0, 76, 255);
        }
        .h-line {
            width: 150px;
            margin: 0 auto;
            height: 1.7px;
        }
        .custom-bg {
            background-color: blue;
            border: 1px solid blue;
        }
        .custom-bg:hover {
            background-color: black;
            border: 1px solid black;
        }
    </style>
</head>
<body class="bg-light">

<?php require('inc/header.php');
$checkin_default="";
$checkout_default="";
$cars_default="";
$bikes_default="";

if(isset($_GET['check_availability'])){
    $frm_data = filteration($_GET);

    $checkin_default=$frm_data['checkin'];
    $checkout_default=$frm_data['checkout'];
    $cars_default=$frm_data['cars'];
    $bikes_default=$frm_data['bikes'];
}



?>

<div class="my-5 px-4">
    <h2 class="text-center fw-bold">Our Parkings</h2>
    <div class="h-line bg-dark"></div>
</div>

<!--filters--->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-mb-12 mb-lg-0 ps-4 mb-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
                <div class="container-fluid flex-lg-column align-items-stretch">
                    <h4 class="mt-2">FILTERS</h4>
                    <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterDropdown" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse flex-column mt-2 align-items-stretch" id="filterDropdown">
                        <div class="border bg-light p-3 rounded mb-3">
                            <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                                <span>Check For Parking</span>
                                <button id="chk_avail_btn" onclick="chk_avail_clear()" class="btn btn-sm shadow-none text-secondary">Reset</button>
                            </h5>
                            <label class="form-label">Check-in</label>
                            <input type="time" class="form-control shadow-none mb-3" value="<?php echo $checkin_default?>" id="checkin"  onchange="chk_avail_filter()">
                            <label class="form-label">Check-out</label>
                            <input type="time" class="form-control shadow-none" id="checkout" value="<?php echo $checkout_default?>"  onchange="chk_avail_filter()">
                        </div>

                        <div class="border bg-light p-3 rounded mb-3">
                            <h5 class="mb-3" style="font-size: 18px;">
                                <span>Facility</span>
                                <button id="facilities_btn" onclick="facilities_clear()" class="btn btn-sm shadow-none text-secondary">Reset</button>
                            </h5>
                            <?php
                                $facilities_q = selectAll('facilities');
                                while($row = mysqli_fetch_assoc($facilities_q)) {
                                    echo <<<facilities
                                        <div class="mb-2">
                                            <input type="checkbox" onclick="fetch_parking()" name="facilities" value="{$row['id']}" class="form-check-input shadow-none me-1" id="{$row['id']}">
                                            <label class="form-label" for="{$row['id']}">{$row['name']}</label>
                                        </div>
                                    facilities;
                                }
                            ?>
                        </div>
                        
                        <div class="border bg-light p-3 rounded mb-3">
                            <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                                <span>Vehicle</span>
                                <button id="guest_btn" onclick="guest_clear()" class="btn btn-sm shadow-none text-secondary">Reset</button>
                            </h5>
                            <div class="d-flex">
                                <div class="me-3">
                                    <label class="form-label">Cars</label>
                                    <input type="number" min="1" id="cars" value="<?php echo $cars_default?>" oninput="guest_filter()" class="form-control shadow-none">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Bikes</label>
                                    <input type="number" min="1" id="bikes" value="<?php echo $bikes_default?>" oninput="guest_filter()" class="form-control shadow-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    
        <div class="col-lg-9 col-md-12 px-4" id="parking-data">
            <div class="spinner-border text-primary d-block mb-3 mx-auto" id="info_loader" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>
<script>
    let parkings_data = document.getElementById('parking-data');
    let checkin = document.getElementById('checkin');
    let checkout = document.getElementById('checkout');
    let chk_avail_btn = document.getElementById('chk_avail_btn');
    
    let cars = document.getElementById('cars');
    let bikes = document.getElementById('bikes');
    let guest_btn = document.getElementById('guest_btn');
    
    let facilities_btn = document.getElementById('facilities_btn');

    function fetch_parking() {
        let chk_avail = JSON.stringify({
            checkin: checkin.value,
            checkout: checkout.value
        });

        let guests = JSON.stringify({
            cars: cars.value,
            bikes: bikes.value
        });

        let facility_list = {"facilities":[]};
        let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
       
        get_facilities.forEach((facility) => {
        facility_list.facilities.push(facility.value);
        });

        if(get_facilities.length > 0) {
            facilities_btn.classList.remove('d-none');
        } else {
            facilities_btn.classList.add('d-none');
        }

        facility_list = JSON.stringify(facility_list);

        let xhr = new XMLHttpRequest();
        xhr.open("GET", "ajax/parkings.php?fetch_parkings&chk_avail=" + chk_avail + "&guests=" + guests + "&facility_list=" + facility_list, true);
        xhr.onprogress = function() {
            parkings_data.innerHTML = '<div class="spinner-border text-primary d-block mb-3 mx-auto" id="info_loader" role="status"><span class="visually-hidden">Loading...</span></div>';
        };
        xhr.onload = function() {
            parkings_data.innerHTML = this.responseText;
        };
        xhr.send();
    }

    function chk_avail_filter() {
        if (checkin.value !== '' && checkout.value !== '') {
            fetch_parking();
            chk_avail_btn.classList.remove('d-none');
        }
    }

    function chk_avail_clear() {
        checkin.value = '';
        checkout.value = '';
        chk_avail_btn.classList.add('d-none');
        fetch_parking();
    }

    function guest_filter() {
        if (cars.value > 0 || bikes.value > 0) {
            fetch_parking();
            guest_btn.classList.remove('d-none');
        }
    }

    function guest_clear() {
        cars.value = '';
        bikes.value = '';
        guest_btn.classList.add('d-none');
        fetch_parking();
    }

    function facilities_clear() {
        let get_facilities = document.querySelectorAll('[name="facilities"]:checked');
        
        get_facilities.forEach((facility) => {
            facility.checked = false;
            facilities_btn.classList.add('d-none');
        });

        fetch_parking();
    }

    fetch_parking();
</script>
<?php require('inc/footer.php')?>

</body>
</html>