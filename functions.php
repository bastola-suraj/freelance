<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    /* Exception class. */
    require 'mail/Exception.php';

    /* The main PHPMailer class. */
    require 'mail/PHPMailer.php';

    /* SMTP class, needed if you want to use SMTP. */
    require 'mail/SMTP.php';
    //mailer import end
function validate_edu_entry(){
    if(strlen($_POST['institute'])<1||strlen($_POST['edustartyear'])<1||strlen($_POST['eduendyear'])<1||strlen($_POST['degree'])<1){
        return "Required field cannot be empty";
    }
    if(!is_numeric($_POST['edustartyear'])&&!is_numeric($_POST['eduendyear'])){
        return "Invalid date";
    }
    return true;
}

function validate_insert_institute($pdo){
    $stmt=$pdo->prepare("select name from institute where name=:in");
    $stmt->execute(array('in'=>$_POST['institute']));
    $row=$stmt->fetch();
    if($row===false){
        $stmt=$pdo->prepare("insert into institute (name) values (:in)");
        $stmt->execute(array(':in'=>$_POST['institute']));
        return $pdo->lastInsertId();
    }
    else{
        return $row['id'];
    }
}

function insert_edu($pdo){
    $stmt=$pdo->prepare("insert into education (start_year,end_year,description,degree_id,freelancer_id,major,institute_id) Values (:sy,:ey,:desc,:di,:fi,:ma,:ii)");
        $stmt->execute(array(
            ':ii'=>$_SESSION['institute_id'],
            ':sy'=>$_POST['edustartyear'],
            ':ey'=>$_POST['eduendyear'],
            ':desc'=>$_POST['edudescription'],
            ':di'=>$_POST['degree'],
            ':ma'=>$_POST['major'],
            ':fi'=>$_SESSION['f_id']
        ));
}

function validate_registration($pdo){
    if(strlen($_POST['reg_name'])<1 || strlen($_POST['reg_email'])<1 || strlen($_POST['reg_pass'])<1 ||strlen($_POST['confirm_pass'])<1 ||strlen($_POST['reg_type'])<1 || strlen($_POST['reg_phone'])<1 || strlen($_POST['reg_address'])<1){
        return "Required field cannot be empty";
    }
    if($_POST['confirm_pass']!=$_POST['reg_pass']){
        return "Password confirmation doesn't match";
    }
    if(strpos($_POST['reg_name'],'@')<0 || strpos($_POST['reg_name'],'.com')<0){
        return "Invalid email address";
    }
    if(strlen($_POST['reg_pass'])<8){
        return "Password too short";
    }
    if(strlen($_POST['reg_phone'])!=10){
        return "Invalid Mobile Number";
    }
    if($_POST['reg_type']!="freelancer" && $_POST['reg_type']!="employer"){
        return "Invalid User Type";
    }
    $stmt=$pdo->prepare('select u_email from users where u_email=:email');
    $stmt->execute(array(':email'=>$_POST['reg_email']));
    $row=$stmt->fetch();
    if($row!==false){
        return "Email address already exist.";
    }
    return true;
}
function flashmsg(){
    if(isset($_SESSION['error'])){
        echo "<p style='color:red;'>".$_SESSION['error']."</p>";
        unset($_SESSION['error']);
    }
    if(isset($_SESSION['success'])){
        echo "<p style='color:green;'>".$_SESSION['success']."</p>";
        unset($_SESSION['success']);
    }
    if(isset($_SESSION['edu_entry_error'])){
        echo "<p style='color:red;'>".$_SESSION['edu_entry_error']."</p>";
        unset($_SESSION['edu_entry_error']);
    }
}

function mailer($email,$name,$subject,$email_body){
    try {
    $mail = new PHPMailer(true);
    $mail->IsSMTP();
    $mail->Host='us2.smtp.mailhostbox.com';
    $mail->Port='587';
    $mail->SMTPAuth=true;
    $mail->Username='donotreply@bibhid.com';
    $mail->Password='eNOLrfO8';
    $mail->SMTPSecure='';
    $mail->setFrom('donotreply@bibhid.com','Freelance Nepal');
    $mail->addAddress($email,$name);
    $mail->IsHTML(true);
    $mail->Subject=$subject;
    $mail->Body=$email_body;
    if($mail->send()){
        return "success";
    }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
    return true;
}