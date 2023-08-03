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
    </style>
</head>
<body>
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

    // create a random code and send it to next page to store in the database as well as send in email or by sms
    $pass= rand(100000, 999999); 
    
    

    // Insert otp record in database
    $ins = "INSERT INTO `otp_verification`(`otp_method`, `otp_code`, `user_id`, `status`, `created`) VALUES ('$method','$pass','$ifid','Active', now())";
    $insd = mysqli_query($conn, $ins);
    $last_id = $conn->insert_id;

    
    @session_start();
    $_SESSION["OTP"]=$pass;
    $_SESSION['USER']=$ifid;
    $_SESSION['INSERTED']=$last_id;
?>
<div class="container">
    <div class="Verification">
  <h2>Verification Method</h2>
  <p>Choose a method between Email / SMS for the verification purpose.</p>
  <form action="send_otp.php?insert=<?php echo $last_id; ?>" class="needs-validation" method="get" novalidate>
    <div class="form-group">
      <label for="uname">Choose Method</label>
      <select name="method" class="form-control" required="required">
        <option value="">-- CHOOSE METHOD --</option>
        <option value="email">EMAIL (<?php echo $myemail; ?>)</option>
        <option value="mobile">MOBILE NO. (<?php echo $phone_mobile; ?>)</option>
        <option value="office">OFFICE PHONE (<?php echo $phone_work; ?>)</option>
      </select>
      <div class="valid-feedback">Valid.</div>
      <div class="invalid-feedback">Please fill out this field.</div>
    </div>
    
    <button type="submit" class="btn btn-primary form-control">SEND OTP TO VERIFY</button>
  </form>
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
