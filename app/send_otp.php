<!DOCTYPE html>
<html lang="en">
<head>
  <title>User Verification</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- jQuery library -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<!-- Script to move control on next input field -->
<script>
$(document).ready(function() {
    $("input.form-control.input-box").keyup(function() {
        if ($(this).val().length == $(this).attr("maxlength")) {
            $(this).next('input.form-control.input-box').focus();
        }
    });
});
</script>
  <style>
    .Verification {
    padding: 50px;
    margin: 50px 0px 50px 0px;
}
body {
    margin: 0;
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans","Liberation Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    text-align: left;
    background-color: #f5f5f5;
}
input.form-control.input-box {
    width: 50px;
    height: 50px;
    text-align: center;
    margin: auto;
}
    </style>
</head>
<body>
<script type="text/javascript">
    window.onbeforeunload = function() {
        return "Dude, are you sure you want to leave? Think of the kittens!";
    }
</script>


<div class="container">
    <div class="row">
    <?php
include('connect.php');
// to get the data from the url
$ifid = $_GET['profileid']; 
//filtering the user details based on the data fetched from the url
$sql = "SELECT * FROM `contacts` WHERE id='$ifid'";
$data = mysqli_query($conn, $sql);
while($drow = mysqli_fetch_array($data)) {
      $uid = $drow['id'];
      $phone_mobile = $drow['phone_mobile'];
      $phone_work = $drow['phone_work'];
      $first_name = $drow['first_name'];
      // echo $uid;
     // finding the email connection by using the primary id of a user
      $sqlt = "SELECT * FROM `email_addr_bean_rel` where bean_id='$uid'";
      $eabr = mysqli_query($conn, $sqlt) or die( mysqli_error($conn));
      while($earow = mysqli_fetch_array($eabr)){
        $umailid = $earow['email_address_id'];
      }
     // matching the record and fetching the email address from the table by using the fetched id, contact id and than the bean email address id
      $sesql = "SELECT * FROM `email_addresses` WHERE id='$umailid'";
      $edata = mysqli_query($conn, $sesql) or die(mysqli_error($conn));
      while($eedata = mysqli_fetch_array($edata)){
                    $myemail = $eedata['email_address'];
      }
     
    }

    $method = $_GET['method'];
    @session_start();
    //echo $_SESSION["OTP"];
    $otpcode = $_SESSION['OTP'];
    $ifidi = $_SESSION['USER'];
    $lastid = $_SESSION['INSERTED'];
    
    $upd = "UPDATE `otp_verification` SET `otp_method`='$method' WHERE id='$lastid'";
    mysqli_query($conn, $upd);



// email function...
//$to = 'user@example.com'; 
$from = 'sender@occom.com.au'; 
$fromName = 'Ocom - Optical, Communication Expert'; 
 
$subject = "Customer Verification"; 
 
$htmlContent = ' 
    <html> 
    <head> 
        <title>Hello .".$first_name.".</title> 
    </head> 
    <body> 
        <h1>Please share the OTP with our representative to verify your identity.!</h1> 
        <table cellspacing="0" style="border: 2px dashed #FB4314; width: 100%;"> 
            <tr> 
                <th>OTP</th> 
            </tr> 
            <tr style="background-color: #e0e0e0;"> 
                <h2><?php echo $_SESSION["OTP"]; ?></h2> 
            </tr> 
           
        </table> 
    </body> 
    </html>'; 
 
// Set content-type header for sending HTML email 
$headers = "MIME-Version: 1.0" . "\r\n"; 
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
 
// Additional headers 
$headers .= 'From: '.$fromName.'<'.$from.'>' . "\r\n"; 
$headers .= 'Cc: customercare@occom.com.au' . "\r\n"; 
 
// Send email 
if(mail($myemail, $subject, $htmlContent, $headers)){ 
    echo 'Email has sent successfully.'; 
}else{ 
  // echo 'Email sending failed.'; 
}

if(isset($_POST['submit'])) {
    include('connect.php');
    $otp1 = $_POST['otp1'];
    $otp2 = $_POST['otp2'];
    $otp3 = $_POST['otp3'];
    $otp4 = $_POST['otp4'];
    $otp5 = $_POST['otp5'];
    $otp6 = $_POST['otp6'];
    $otpfinal = $otp1 . '' . $otp2 . '' . $otp3 . '' . $otp4 . '' . $otp5 . '' . $otp6;
    //echo $otpfinal;
    // echo "SELECT * FROM `otp_verification` WHERE user_id='$ifidi' order by id desc limit 1";
    $otv = "SELECT * FROM `otp_verification` WHERE user_id='$ifidi' order by id desc limit 1";
    $otvf  = mysqli_query($conn, $otv);
    while($fro = mysqli_fetch_array($otvf)){
        $vf = $fro['otp_code'];
        //echo $vf;
    }
    @session_start();
    //echo $_SESSION["OTP"];
   // $otpcode = $_SESSION['OTP'];
   // echo $otpcode;

    if($otpfinal == $vf) {
        ?>
        <span class="alert alert-success" style="width:100%;">Congratulations! Your otp has been verified.</span>
        <meta http-equiv = "refresh" content = "10; url = index.php?action=ajaxui#ajaxUILoc=index.php%3Fmodule%3DContacts%26action%3Dindex%26parentTab%3DAll" />
        <?php
    }else{

        ?>
        <span class="alert alert-danger" style="width:100%;">Sorry! Your otp is wrong.</span>
        <?php
    }
                   
}
?>
</div>
    <div class="Verification">
  <h4>VERY THE OTP SHARED BY CUSTOMER</h4>
    <div class="form-group">
    </div>
    <form action="" method="post" enctype="multipart/form-data">
    <div class="row verifybox">
        <div class="col-sm-2"><input type="text" name="otp1" class="form-control input-box" placeholder="*" min="0" max="1" maxlength="1"></div>
        <div class="col-sm-2"><input type="text" name="otp2" class="form-control input-box" placeholder="*" min="0" max="1" maxlength="1"></div>
        <div class="col-sm-2"><input type="text" name="otp3" class="form-control input-box" placeholder="*" min="0" max="1" maxlength="1"></div>
        <div class="col-sm-2"><input type="text" name="otp4" class="form-control input-box" placeholder="*" min="0" max="1" maxlength="1"></div>
        <div class="col-sm-2"><input type="text" name="otp5" class="form-control input-box" placeholder="*" min="0" max="1" maxlength="1"></div>
        <div class="col-sm-2"><input type="text" name="otp6" class="form-control input-box" placeholder="*" min="0" max="1" maxlength="1"></div>
     </div>
     <div class="row m-3">
        <div class="col-sm-12">
        <input type="submit" name="submit" class="form-control btn btn-success" value="Verify Contacts" />
</div>
     </div>
    
   
</div>
</div>
<script>
// Disable form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Get the forms we want to add validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
</script>

</body>
</html>
