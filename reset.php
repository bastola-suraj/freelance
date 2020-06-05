<?php
if(!isset($_GET['resetpw'])){
  die('The page you are looking for have been moved or inaccessible.');
}
require_once 'db-conn.php';
require_once 'functions.php';
session_start();
$stmt=$pdo->prepare('select * from users where u_auth=:auth');
$stmt->execute(array('auth'=>$_GET['resetpw']));
$row=$stmt->fetch();
if($row==false){
    $_SESSION['error']="This link may have expired. Please <a href='login.php?#mymodal'>login</a> to continue.";
    flashmsg();
    die();
    }
if(isset($_POST['reset'])&&isset($_POST['reg_pass'])&&isset($_POST['confirm_pass'])){
    if(strlen($_POST['reg_pass'])<8){
        $_SESSION['error']="Password too short";
    }
    if($_POST['confirm_pass']!=$_POST['reg_pass']){
        $_SESSION['error']="Password confirmation doesn't match";
    }
    else{
        $pass=hash('md5',$_POST['reg_pass']);
        $auth="";
        $stmt=$pdo->prepare('update users set u_pass=:pass, u_auth=:empty where u_auth=:auth');
        $stmt->execute(array('auth'=>$_GET['resetpw'],':pass'=>$pass,':empty'=>$auth));
        $_SESSION['success']="Password Changed Sucessfully.";
        header("Location:login.php");
        return;
    }
}
?>
<html>
    <head>
        <?php require_once 'scriptyle.php';?>
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
                            <label for="reg_pass">Password<span style="color:red;">*</span></label>
                            <input class="form-control" name="reg_pass" id="reg_pass"/>
                            <label for="confirm_pass">Confirm Password<span style="color:red;">*</span></label>
                            <input class="form-control" name="confirm_pass" id="confirm_pass"/>
                            <br/>
                            <input class="col-xs-2" type="submit" id="reset_btn" name="reset" value="Reset Password" onclick="validatepassword();"/>
                        </form>
                    <p><a href="register.php">Create an Account</a><br/>
                    <a href="reset.php">Login Here</a></p>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
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
    </body>
</html>