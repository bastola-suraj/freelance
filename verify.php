<?php
if(!isset($_GET['auth'])){
  die('The page you are looking for have been moved or inaccessible.');
}
require_once 'db-conn.php';
require_once 'functions.php';
session_start();
$stmt=$pdo->prepare('select * from users where u_auth=:auth');
$stmt->execute(array('auth'=>$_GET['auth']));
$row=$stmt->fetch();
if($row!==false){
    if($row['u_status']=='verified'){
       $_SESSION['error']="Email already verified. Please login"; 
    }
    else{
        $_SESSION['uname']=$row['u_name'];
        $_SESSION['uemail']=$row['u_email'];
        $_SESSION['umobile']=$row['u_mobile'];
        $_SESSION['uname']=$row['u_name'];
        $stmt=$pdo->prepare('update users set u_status=:status where u_email=:em');
        $stmt->execute(array(':status'=>'verified','em'=>$_SESSION['uemail']));
        $_SESSION['success']="Email address sucessfully verified.";
        $_SESSION['stat']='verified';
    }
}
else{
    $_SESSION['error']="This page may have expired. Please <a href='login.php'>login</a> to continue.";
}
flashmsg();
?>