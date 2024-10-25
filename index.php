<?php include './includes/connect.php';
$username_msg="Username should be between 3 and 30 characters";
$place_msg="Place should be between 3 and 30 characters";
$invalid_email="Invalid email address";
$phone_msg="Phone number at most 10 digits";
$fill_all_fields="Please fill up all fields";
$error_messages=array();

if(isset($_POST['submit'])){
    $username=htmlspecialchars($_POST['username']);
    $email=$_POST['email'];
    $place=htmlspecialchars($_POST['place']);
    $phone=preg_replace('/[^0-9]/','',$_POST['phone']);
    
    // str_replace
    $username=str_replace(" ","",$username);
    $place=str_replace(" ","",$place);
    // ucfirst and string lower case
    $username=ucfirst(strtolower($username));
    $place=ucfirst(strtolower($place));

    if(empty($username) || empty($email) || empty($phone) || empty($place)){
      $error_messages[]=$fill_all_fields;
    }else{

    if(!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)){
        $error_messages[]=$invalid_email;
    }

    if(strlen($phone)!==10 || !ctype_digit($phone) || $phone<=0){
      $error_messages[]=$phone_msg;
    }

    if(strlen($username)<3 || strlen($username)>30){
      $error_messages[]= $username_msg;
    };

    if(strlen($place)<3 || strlen($place)>30){
      $error_messages[]= $place_msg;
    };
    
    if(empty($error_messages)){
      $check_query="select * from validate where username='$username' or email='$email'";
      $check_query=mysqli_query($con, $check_query);
      if(!$check_query){
        echo "<script>alert('Error checking existing data')</script>";
      }
      if(mysqli_num_rows($check_query) > 0){
        $existing_user_email_error="Username or Email already exist";
        $error_messages[]=$existing_user_email_error;
      }else{
        // validate and sanitizing input
        $username=mysqli_real_escape_string($con,$username);
        $email=mysqli_real_escape_string($con,$email);
        $phone=mysqli_real_escape_string($con,$phone);
        $place=mysqli_real_escape_string($con,$place);
        $insert_query="insert into validate (username,email,phone,place) values ('$username','$email','$phone','$place')";
        $result=mysqli_query($con,$insert_query);
        if($result){
          echo "<script>alert('Data inserted successfully')</script>";
          echo "<script>window.open('index.php','_self')</script>";
          
        }else{
          die(mysqli_error($con));
        }
      }
    }


    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Validation</title>
    <link rel="stylesheet" href="style.css?v1=a">
</head>
<body>
  <div class="form_container">
    
      <div class="form_group" id="user_email_exists">
        <span class="error_messages"><?php echo isset($existing_user_email_error)? $existing_user_email_error:""; ?></span>
      </div>
      <form action="" method="post">

      <h1 class="heading">Form Validation</h1>
    <?php 
          if(in_array($fill_all_fields, $error_messages)) 
          echo '<span class="error_messages">'.$fill_all_fields.'</span>';
        ?>
    <div>

        <input type="text" name="username" 
        placeholder="Enter your username"  
        class="input_field"  
        autocomplete="off" />
        <?php 
          if(in_array($username_msg, $error_messages)) 
          echo '<span class="error_messages">'.$username_msg.'</span>';
        ?>
                
        <input type="email" name="email" placeholder="Enter your email"  class="input_field" autocomplete="off" />
        <?php 
          if(in_array($invalid_email, $error_messages)) 
          echo '<span class="error_messages">'.$invalid_email.'</span>';
        ?>

        <input type="number" name="phone" placeholder="Enter your phone" class="input_field" autocomplete="off" />
        <?php 
          if(in_array($phone_msg, $error_messages)) 
          echo '<span class="error_messages">'.$phone_msg.'</span>';
        ?>

        <input type="place" name="place" placeholder="Enter your place"  class="input_field" autocomplete="off" />
        <?php 
          if(in_array($place_msg, $error_messages)) 
          echo '<span class="error_messages">'.$place_msg.'</span>';
        ?>

        <button type="submit" class="btn" name="submit">Submit</button>

      </form>
    </div>
  </div>

  <!-- JavaScript code to clear input fields -->
<script>
      // Clear error messages when input fields are focused
      document.addEventListener('DOMContentLoaded', function () {
          const form = document.querySelector('form');
          const inputFields = form.querySelectorAll('input');
          const allFieldsError = document.getElementById('all_fields_error');
          const userEmailError = document.getElementById('user_email_exist');

          inputFields.forEach(function(input) {
              input.addEventListener('focus', function() {
                  if (allFieldsError) {
                      allFieldsError.textContent = ''; // Clear the "Please fill in all fields" error
                      
                  }
                  if(userEmailError){
                      userEmailError.textContent='';
                  }

                  const errorSpan = this.nextElementSibling;
                  if (errorSpan && errorSpan.classList.contains('error_messages')) {
                      errorSpan.textContent = '';
                  }
              });
          });
      });
  </script>
</body>
</html>