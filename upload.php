<?php
require_once 'db-conn.php';
require_once 'functions.php';
session_start();
use Verot\Upload;
require_once 'upload/class.upload.php';
$handle = new \Verot\Upload\Upload($_FILES['image_field']);
if(isset($_SESSION['uemail'])){
    $finame= 'xfd8'.hash('md5',$_SESSION['uemail']);
}
else{
    $finame= 'xfd8'.hash('md5',rand());
}
if ($handle->uploaded) {
    $handle->allowed = array('image/*');
    $handle->forbidden = array('application/*');
    $handle->file_new_name_body   = $finame;
    $handle->image_resize         = true;
    $handle->image_x              = 150;
    $handle->image_ratio_y        = true;
    $handle->image_convert = 'webp';
    $handle->file_overwrite = true;
    $handle->webp_quality = 80;
    $handle->file_new_name_ext = 'webp';
    $handle->process('profileimg/');
    $handle->dir_auto_create = true;
    if ($handle->processed) {
        $stmt=$pdo->prepare('update users set u_image=:img where u_email=:em');
        $stmt->execute(array('img'=>'profileimg/'.$finame.'.webp',':em'=>$_SESSION['uemail']));
        $_SESSION['profile']='profileimg/'.$finame.'.webp';
        $handle->clean();
    } else {
        $_SESSION['picerror']='Error : ' . $handle->error;
    }
        header("Location:profile.php");
        return;
}
?>