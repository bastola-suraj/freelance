<?php
require_once 'db-conn.php';
require_once 'functions.php';
session_start();
$salt="_FrEe_";
if(isset($_POST['reg_name'])&&isset($_POST['reg_address'])&&isset($_POST['reg_email'])&&isset($_POST['reg_phone'])&&isset($_POST['reg_pass'])&&isset($_POST['reg_type'])&&isset($_POST['confirm_pass'])&&isset($_POST['submit'])){
    $password=hash('md5',$_POST['reg_pass']);
    $_SESSION['auth']=hash('md5',rand());
    $validatereg=validate_registration($pdo);
    if(is_string($validatereg)){
        $_SESSION['error']=$validatereg;
        header("Location:register.php");
        return;
    }
    else{
        //validated data insertion
        $stmt=$pdo->prepare("insert into users (u_name,u_address,u_mobile,u_email,u_auth,u_pass,u_status) values (:nm,:ad,:ph,:em,:au,:pw,:st)");
        $stmt->execute(array(
            ':nm'=>$_POST['reg_name'],
            ':ph'=>$_POST['reg_phone'],
            ':em'=>$_POST['reg_email'],
            ':au'=>$_SESSION['auth'],
            ':ad'=>$_POST['reg_address'],
            ':st'=>'unverified',
            ':pw'=>$password
        ));
        $uid=$pdo->lastInsertId();
        if($_POST['reg_type']=="freelancer"){
            $stmt=$pdo->prepare("insert into freelancer (users_id,join_date) values (:uid,:date)");
            $stmt->execute(array(':uid'=>$uid,':date'=>date('Y-m-d')));
        }
        if($_POST['reg_type']=="employer"){
            $stmt=$pdo->prepare("insert into employer (users_id,join_date) values (:uid,:date)");
            $stmt->execute(array(':uid'=>$uid,':date'=>date('Y-m-d')));
        }
        $mail_body="<p>Hi ".$_POST['reg_name'].",</p>
            <p>Thanks for registering for an account on Freelance Nepal! Before we get started, we just need to confirm that this is you. Click below to verify your email address:</p>
            <p align='center'><a style='text-decoration:none;' href='http://localhost/freelance/verify.php?auth=".$_SESSION['auth']."'><span style='padding:10px;background-color:grey;color:white;font-weight:bold;'>Verify Account</span></a></p><p>Best Regards Freelance Nepal</p>";
        $subject="Email Verification";
        $email=$_POST['reg_email'];
        $uname=$_POST['reg_name'];
        $mailstat=mailer($email,$uname,$subject,$mail_body);
        if(is_string($mailstat)){
            $_SESSION['success']="Registered Sucessfully. Please verify your email address";
        }
    }
}
?>
<html>
    <head>
        <?php require_once 'scriptyle.php'?>
        <title>Freelance Nepal:Register</title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <?=flashmsg();?>
                    <div class="card card-body">
                        <form method="post" class="form-group">
                            <p><span style="color:red;">*</span>Required fields</p>
                            <label for="reg_name">Name<span style="color:red;">*</span></label>
                            <input class="form-control" name="reg_name" id="reg_name"/>
                            <label for="reg_email">Email<span style="color:red;">*</span></label>
                            <input class="form-control" name="reg_email" id="reg_email"/>
                            <label for="reg_address">Address<span style="color:red;">*</span></label>
                            <input class="form-control" name="reg_address" id="reg_address"/>
                            <label for="reg_type">User Type<span style="color:red;">*</span></label>
                            <select class="form-control" name="reg_type" id="reg_type">
                                <option value="freelancer" selected>Freelancer</option>
                                <option value="employer">Employer</option>
                            </select>    
                            <label for="reg_phone">Phone<span style="color:red;">*</span></label>
                            <input class="form-control" name="reg_phone" id="reg_phone"/>
                            <label for="reg_pass">Password<span style="color:red;">*</span></label>
                            <input class="form-control" name="reg_pass" id="reg_pass"/>
                            <label for="confirm_pass">Confirm Password<span style="color:red;">*</span></label>
                            <input class="form-control" name="confirm_pass" id="confirm_pass"/>
                            <br/>
                            <input type="submit" id="reg_btn" name="submit" value="Register" onclick="validatepassword();"/>
                        </form>
                        <p>Already have an account?<a href="login.php">Login here</a></p>
                    </div>    
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </body>
    <script>
       $(document).ready(function(){
          $('#confirm_pass').keyup(function(event){
              if($('#reg_pass').val()!=$('#confirm_pass').val()){
                  $('#confirm_pass').css("border-color","red");
              }
              else{
                  $('#confirm_pass').css("border-color","#80bdff");
              }
          }); 
       });
    </script>
</html>