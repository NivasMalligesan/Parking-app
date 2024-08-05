<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require('inc/links.php')?>
    <title><?php echo $settings_r['site_title']?> - Contact</title>
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
        .h-line{
    width: 150px;
    margin: 0 auto;
    height: 1.7px;
}

    .custom-alert{
            position: fixed;
            top: 80px;
            right: 25px;
    }
    </style>
  
</head>
<body class="bg-light">

<?php require('inc/header.php');?>

<div class="my-5 px-4">
    <h2 class="text-center fw-bold">Contact Us</h2>
    <div class="h-line bg-dark"></div>
    <p class="text-center mt-3">For any queries or complaint feel
         free to contact us. <br>
        We are here to make your shopping experience better.</p>
</div>



<div class="container">
    <div class="row">
        <div class="col-lg-6 col-md-6 mb-5 px-4">

            <div class="bg-white rounded shadow p-4 pop">
                <iframe class="w-100 rounded"  height="320px" src="<?php echo $contact_r['iframe']?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    
                    <h5>Address</h5>
                    <a href="<?php echo $contact_r['gmap']?>" class="text-decoration-none d-inline text-dark" target="_blank"><i class="bi bi-geo-alt-fill"></i>Nexus Mall ,
                    <?php echo $contact_r['address']?>
                    </a>
                   
                    <h5 class="mt-4">Call US</h5>
                    <a href="tel: +<?php echo $contact_r['pn1']?>" class="d-inline-block mb-2 text-decoration-none text-dark">
                        <i class="bi bi-telephone-fill me-1"></i>+<?php echo $contact_r['pn1']?>
                    </a>
                    <br>
                    <?php 
                    if($contact_r['pn2']==0){
                        echo <<< data
                            <a href="tel: +<?php$contact_r[pn2] ?>" class="d-inline-block text-decoration-none text-dark"
                                <i class="bi bi-telephone-fill me-1"></i>+<? echo php$contact_r[pn2] ?>
                            </a>
                        data;
                     }
                    ?>
                   
                    <h5 class="mt-4">Email</h5>
                    <a href="mailto: <?php echo $contact_r['email']?>" class="d-inline-block text-decoration-none text-dark">
                    <i class="bi bi-envelope-at me-1"></i><?php echo $contact_r['email']?>
                    </a>
                    
                    <h5 class="mt-4">Follow Us on</h5>

                    <?php 
                        if($contact_r['tw']!=''){
                            echo <<< data
                            <a href="$contact_r[tw]" class="d-inline-block text-dark fs-5 me-2 ">
                            <i class="bi bi-twitter-x"></i>
                            </a>
                            data;
                        }
                    ?>

                    <?php 
                        if($contact_r['fb']!=''){
                            echo <<< data
                            <a href="$contact_r[fb]" class="d-inline-block text-dark fs-5 me-2 ">
                            <i class="bi bi-facebook"></i>
                            </a>
                            data;
                        }
                    ?>
                       
                        <a href="$contact_r['fb']" class="d-inline-block text-dark fs-5">
                            <i class="bi bi-instagram"></i>
                        </a>
                    </div>
                </div>
        <div class="col-lg-6 col-md-6 px-4">
            <div class="bg-white rounded shadow p-4 ">
              <form method="POST">
    
                <h5>Send A Message</h5>
                <div class="mt-3">
                    <label class="form-label font-weight : 500">Name</label>
                    <input name="name"required type="text" class="form-control shadow-none"  aria-describedby="emailHelp">
                </div>
                <div class="mt-3">
                    <label class="form-label font-weight : 500">Email</label>
                    <input name="email"requried type="email"class="form-control shadow-none"aria-describedby="emailHelp">
                </div>
                <div class="mt-3">
                    <label class="form-label font-weight : 500">Subject</label>
                    <input name="subject" required type="text" class="form-control shadow-none"  aria-describedby="emailHelp">
                </div>
                <div class="col-md-12 mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" required class="form-control shadow-none"rows="5" style="resize: none;"></textarea>
                </div>
                <button type="submit" name="send"class="btn text-white check-bg shadow-none">Send</button>
              </form>  
        </div>
 
    </div>
</div>
<?php
if(isset($_POST["send"]))
{
    $frm_data = filteration($_POST);
    $q ="INSERT INTO `user_queries`(`name`,`email`,`subject`,`message`) VALUES(?,?,?,?)";
    $values = [$frm_data['name'],$frm_data['email'],$frm_data['subject'],$frm_data['message']];
    $res = insert($q,$values,'ssss');
    if($res==1){
      alert('success','Mail sent!');  
    }
else{
 alert ('error','Server Down! Try again later.');  
}
}?>


<?php require('inc/footer.php')
?>

</body>
</html>