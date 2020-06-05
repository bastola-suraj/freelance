<?php
require_once 'db-conn.php';
require_once 'functions.php';
session_start();
//Send verification link
if(isset($_GET['action']) && $_GET['action']=='verify'){
    $mail_body="<p>Hi ".$_SESSION['uname'].",</p>
            <p>Thanks for registering for an account on Freelance Nepal! Before we get started, we just need to confirm that this is you. Click below to verify your email address:</p>
            <p align='center'><a href='http://localhost/freelance/verify.php?auth=".$_SESSION['auth']."'>Verify Account</a></p><p>Best Regards Freelance Nepal</p>";
    $subject="Email Verification";
    $email=$_SESSION['uemail'];
    $uname=$_SESSION['uname'];
    $mailstat=mailer($email,$uname,$subject,$mail_body);
    if(is_string($mailstat)){
        session_unset();
        $_SESSION['success']="Verification link sent. Please verify your email address";
        header('Location:login.php');
        return;
    }
}
//send password reset link
if(isset($_POST['resetpw'])&&isset($_POST['reset_email'])){
    $stmt=$pdo->prepare('select * from users where u_email=:em');
    $stmt->execute(array('em'=>$_POST['reset_email']));
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    if($row!=false){
        $_SESSION['auth']=hash('md5',rand());
        $stmt=$pdo->prepare('update users set u_auth=:auth where u_email=:em');
        $stmt->execute(array('em'=>$_POST['reset_email'],':auth'=>$_SESSION['auth']));
        $_SESSION['uname']=$row['u_name'];
        $_SESSION['uemail']=$row['u_email'];
        $_SESSION['umobile']=$row['u_mobile'];
        $_SESSION['stat']=$row['u_status'];
        $mail_body="<p>Hi ".$_SESSION['uname'].",</p>
            <p>Someone just requested to reset password for an account on Freelance Nepal! Before we get started, we just need to confirm that this is you. Click below to reset your password:</p>
            <p align='center'><a style='text-decoration:none;' href='http://localhost/freelance/reset.php?resetpw=".$_SESSION['auth']."'><span style='padding:10px;background-color:grey;color:white;font-weight:bold;'>Reset Password</span></a></p><p>Best Regards Freelance Nepal</p>";
        $subject="Password Reset";
        $email=$_SESSION['uemail'];
        $uname=$_SESSION['uname'];
        $mailstat=mailer($email,$uname,$subject,$mail_body);
        if(is_string($mailstat)){
            session_unset();
            $_SESSION['success']="Password Reset link sent to your email address."; 
        }
    }
    else{
        $_SESSION['error']="Email address not found.";
    }
}
//fetch data form user database
if(isset($_POST['log_email'])&&isset($_POST['log_pass'])&&isset($_POST['login'])){
    $pass=hash('md5',$_POST['log_pass']);
    $stmt=$pdo->prepare('select * from users where u_email=:em and u_pass=:pw');
    $stmt->execute(array('em'=>$_POST['log_email'],':pw'=>$pass));
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    if($row!=false){
        //if data fetch got data
        $_SESSION['uname']=$row['u_name'];
        $_SESSION['uemail']=$row['u_email'];
        $_SESSION['umobile']=$row['u_mobile'];
        $_SESSION['stat']=$row['u_status'];
        $_SESSION['profile']=$row['u_image'];
        $_SESSION['u_id']=$row['u_id'];
        //check if email is verified
        if($row['u_status']=="verified"){
            //fetch freelancer id
            $stmt=$pdo->prepare('select * from freelancer where users_id=:u_id');
            $stmt->execute(array(':u_id'=>$_SESSION['u_id']));
            $row=$stmt->fetch(PDO::FETCH_ASSOC);
            if($row!=false){
                $_SESSION['f_id']=$row['id'];
            }
            //fetch employer id
            //$stmt=$pdo->prepare('select * from employer where users_id=:u_id');
            //$stmt->execute(array(':u_id'=>$_SESSION['u_id']));
            //$row=$stmt->fetch(PDO::FETCH_ASSOC);
            //if($row!=false){
            //    $_SESSION['e_id']=$row['id'];
            //}
            header('Location:index.php');
        }
        else{
            //if email is not verified
            $_SESSION['auth']=hash('md5',rand());
            $stmt=$pdo->prepare("update users set u_auth=:auth where u_email=:em");
            $stmt->execute(array(':auth'=>$_SESSION['auth'],':em'=>$_SESSION['uemail']));
            $_SESSION['error']="Your Email address is not verified. <a href='login.php?action=verify'>Resend verification link</a>";
            header('Location:login.php');
            return;
        }
    }
    else{
    $_SESSION['error']="Incorrect Email address or Password";
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
                    <div class="card card-body">
                        <form method="post" class="form-group">
                            <?=flashmsg();?>
                            <label for="log_email">Email</label>
                            <input class="form-control" name="log_email" id="log_email"/>
                            <label for="log_pass">Password</label>
                            <input class="form-control" name="log_pass" id="log_pass"/>
                            <br/>
                            <input type="submit" id="login_btn" name="login" value="Login"/>
                        </form>
                    <p><a href="register.php">Create an Account</a><br/>
                        <a data-toggle="modal" href="#myModal" >Reset a password</a>
                    </p>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>

  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title">Reset a password</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <form method="post" class="form-group">
                            <?=flashmsg();?>
                            <label for="reset_email">Email</label>
                            <input class="form-control" name="reset_email" id="reset_email"/>
                            <br/>
                            <input class="col-xs-2" type="submit" id="reset_btn" name="resetpw" value="Send Verification Link"/>
                        </form>
          </div>
      </div>
      
    </div>
  </div>
</div>
     </body>
</html>