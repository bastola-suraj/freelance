<?php
require_once 'db-conn.php';
require_once 'functions.php';
session_start();
if(!isset($_SESSION['uemail'])&&!isset($_SESSION['f_id'])){
    header("Location:index.php");
}
//validate and insert education data
if(isset($_POST['institute'])&&isset($_POST['edustartyear'])&&isset($_POST['eduendyear'])&&isset($_POST['degree'])&&isset($_POST['major'])&&isset($_POST['addedu'])&&isset($_POST['edudescription'])){
    $validate_edu_entry=validate_edu_entry();
    if(is_string($validate_edu_entry)){
        $_SESSION['edu_entry_error']=$validate_edu_entry;
        header("Location:profile.php");
        return;
    }
    else{
        $_SESSION['institute_id']=validate_insert_institute($pdo);
        insert_edu($pdo);
        header("Location:profile.php");
        return;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once 'scriptyle.php'?>
        <title><?=$_SESSION['uname']?></title>
    </head>
    <body>
        <!--profile picture section-->
        <script>
            dig="<?=isset($_SESSION['picerror'])?$_SESSION['picerror']:'';?>"
            $(document).ready(function(){
                if(dig!=''){
                  $('#myModal').modal('show');
                    console.log(dig);
                }
            });
             eduerror="<?=isset($_SESSION['edu_entry_error'])?$_SESSION['edu_entry_error']:'';?>"
            $(document).ready(function(){
                if(eduerror!=''){
                  $('#eduModal').modal('show');
                    console.log(dig);
                }
            });
            
        </script>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="profile_picture_wrap">
                <img class="profile_image" src="<?php 
            if(isset($_SESSION['profile'])&& strlen($_SESSION['profile'])>5){
                echo $_SESSION['profile'].'?'.rand();
            }
            else{
                echo 'profileimg/default.png?'.rand();
            }
                                                ?>"/ >
                    <div class="profile_change_text_wrap">
                        <div class="profile_change_text " data-toggle="modal" data-target="#myModal">
                            	<i class="fas fa-camera"></i></div>
                    </div>
                </div>
                    
                    <!--Headline section-->
                    <div class="card">
                        <div class="card-header">
                            <h4>Headline</h4>
                        </div>
                        <div class="card-body">
                            <?php
                            $stmt=$pdo->prepare("select headline from freelancer where id=:id");
                            $stmt->execute(array(':id'=>$_SESSION['f_id']));
                            while($row=$stmt->fetch()){
                                echo htmlentities($row['headline']);
                                echo "<br/>";
                            }?>
                            <button type="button" class="btn btn-info" id="editheadlinemodal" data-toggle="modal" data-target="#headlineModal">Edit Headline</button>
                        </div>
                    </div>
                    <!--Description section-->
                    <div class="card">
                    <div class="card-header">
                        <h4>Description:</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $stmt=$pdo->prepare("select description from freelancer where id=:id");
                        $stmt->execute(array(':id'=>$_SESSION['f_id']));
                        while($row=$stmt->fetch()){
                            echo htmlentities($row['description']);
                            echo "<br/>";
                        }
                        ?>
                        <button type="button" class="btn btn-info" id="editdescriptionmodal" data-toggle="modal" data-target="#descriptionModal">Edit profile Description </button>
                    </div>
                </div>
                    
                    <!--Education section-->
                    <div class="card">
                        <div  class="card-header">
                            <h4>Education</h4>
                        </div>
                        <div class="card-body">
                            <?php
                            $stmt=$pdo->prepare("select * from education join institute on institute_id=institute.id join degree on degree_id=degree.id where freelancer_id=:fid");
                            $stmt->execute(array(':fid'=>$_SESSION['f_id']));
                            while($row=$stmt->fetch(PDO::FETCH_ASSOC)){?>
                                <div class="profile_list">
                                    <div class="inlist_edit btn-group">
                                        <button name="call_edit_edu"><i class="fas fa-pencil-alt"></i></button>
                                        <button name="call_delete_edu"><i class="fas fa-trash"></i></button>
                                    </div>
                                    <h4 class="h4"><?=$row['degree'];?></h4>
                                    <h5 class="h5"><?=$row['major'];?> | <?=$row['name'];?></h5>
                                </div>
                            <hr/>
                                
                            <?php }?>
                            <button type="button" class="btn btn-info" id="addedumodal" data-toggle="modal" data-target="#eduModal">Add Education</button>
                        </div>
                    </div>
                    <br/>
                    
                    <!--Jobs section-->
                    <div class="card">
                        <div  class="card-header">
                            <h4>Employment</h4>
                        </div>
                        <div class="card-body">
                         <button type="button" class="btn btn-info" id="addjobmodal" data-toggle="modal" data-target="#jobModal"><span class="glyphicon glyphicon-plus"></span> Add employment history</button>
                        </div>
                    </div>
                </div>
            </div>
         
        <!--Modal for headline-->
        <div class="modal fade" id="headlineModal" role="dialog">
            <div class="modal-dialog">
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">Edit your headline</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                  <div class="modal-body">
                      <form method="post" class="headline-form form-group">
                          <label for="headline">Headline:</label>
                          <input type="text" class="form-control" name="headline" id="headline" placeholder="PHP Developer, AJAX Developer, Business Writer"/>
                          <br/>
                          <input class='float-right btn-sm' type="submit" name="addheadline" value="Save">
                      </form>
                  </div>
                </div>
            </div>
        </div>
        
        <!--Modal for headline-->
        <div class="modal fade" id="descriptionModal" role="dialog">
            <div class="modal-dialog">
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">Profile Description</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                  <div class="modal-body">
                      <form method="post" class="headline-form form-group">
                          <label for="summary">Description:</label>
                          <textarea type="text" class="form-control" name="description" id="description" rows="7" spellcheck placeholder="Write a few lines that describes your skill and experiences _-Describe about your qualification _-Describe about project you have accomplished _-Describe about your skills and training"></textarea>
                          <br/>
                          <input class='float-right btn-sm' type="submit" name="adddescription" value="Save">
                      </form>
                  </div>
                </div>
            </div>
        </div>
        
        <!--Modal for employment-->
        <div class="modal fade" id="jobModal" role="dialog">
            <div class="modal-dialog">
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">Add Employment History</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="post" class="form-group">
                        <label for="company">Company</label>
                        <input type="text" name="company" class="form-control" id="company" placeholder="Unilever Limited"/>
                        
                        <!--Position-->
                                <label for="position">Position</label>
                                <input type="text" name="degree" class="form-control" id="position"/>
                        
                        <!--Years-->
                        <div class="row">
                            <div class="col">
                                <label for="jobstartyear">From:</label>
                                <select type="text" name="jobstartyear" class="form-control" id="jobstartyear">
                                    <?php
                                    for($i=1930;$i<=date('Y');$i++){
                                        echo "<option value='".$i."'";
                                        if($i==date('Y'))echo "selected";
                                        echo ">";
                                        echo $i;
                                        echo "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="jobendyear">To:</label>
                                <select type="text" name="jobendyear" class="form-control" id="jobendyear">
                                    <?php
                                    for($i=1930;$i<=date('Y')+5;$i++){
                                        echo "<option value='".$i."'";
                                        if($i==date('Y'))echo "selected";
                                        echo ">";
                                        echo $i;
                                        echo "</option>";
                                    }
                                    ?>
                                </select>
                                <input type="checkbox" name="currentwork">I currently work here.
                            </div>
                            <div>
                            </div>
                        </div>
                        
                        <!--Description-->
                        <label for="jobdescription">Description</label>
                        <textarea type="text" name="jobdesc" class="form-control" id="jobdescription" rows="6"></textarea>
                        <br/>
                        <input class='float-right btn-sm' type="submit" name="addjob" value="Save">
                    </form>
                  </div>
              </div>
            </div>
        </div>
        
        <!--Modal for education-->
        <div class="modal fade" id="eduModal" role="dialog">
            <div class="modal-dialog">
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">Add Education Degree</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <?=flashmsg()?>
                    <form method="post" class="edu-form form-group">
                        <label for="institute">University/Institution</label>
                        <p><input type="text" name="institute" class="institute form-control" id="institute" placeholder="Purwanchal University" value=""/></p>
                        
                        <!--degree-->
                        <div class="row">
                            <div class="col">
                                <label for="degree">Degree</label>
                                <select type="text" name="degree" class="form-control" id="degree">
                                    <option value="" selected >Select academic degree</option>
                                    <?php
                                    $stmt=$pdo->query("select * from degree order by id desc");
                                    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                                        echo "<option value='".$row['id']."'>";
                                        echo $row['degree'];
                                        echo "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="major">Major</label>
                                <input type="text" name="major" class="form-control" id="major"/>
                            </div>
                            <div>
                            </div>
                        </div>
                        
                        <!--Years-->
                        <div class="row">
                            <div class="col">
                                <label for="edustartyear">Start year</label>
                                <select type="text" name="edustartyear" class="form-control" id="edustartyear">
                                    <?php
                                    for($i=1930;$i<=date('Y');$i++){
                                        echo "<option value='".$i."'";
                                        if($i==date('Y'))echo "selected";
                                        echo ">";
                                        echo $i;
                                        echo "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="eduendyear">Graduation year</label>
                                <select type="text" name="eduendyear" class="form-control" id="eduendyear">
                                    <?php
                                    for($i=1930;$i<=date('Y')+5;$i++){
                                        echo "<option value='".$i."'";
                                        if($i==date('Y'))echo "selected";
                                        echo ">";
                                        echo $i;
                                        echo "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                            </div>
                        </div>
                        
                        <!--Description-->
                        <label for="edudescription">Description</label>
                        <textarea type="text" name="edudescription" class="form-control" id="edudescription" rows="6"></textarea>
                        <br/>
                        <input class='float-right btn-sm' type="submit" name="addedu" value="Save">
                    </form>
                  </div>
              </div>
            </div>
        </div>
        
        <!--profile picture upload modal section-->
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">

              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">Upload profile picture</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <?php
                        if(isset($_SESSION['picerror'])){
                            echo "<p style='color:red'>".$_SESSION['picerror']."</p>";
                            unset($_SESSION['picerror']);
                        }?>
                    <form enctype="multipart/form-data" method="post" class="form-group" action="upload.php">
                        <input type="file" size="32" name="image_field" value="Choose an image"><br/>
                        <input type="submit" name="uploadimage" value="Upload">
                    </form>
                  </div>
              </div>
            </div>
        </div>
        
        <!--Replace _ with new line in placeholder-->
        <script>
            $(document).ready(function(){
                $('#summary').attr('placeholder',function(i,v){
                    var q=(v.match(/_/g)||[]).length;
                    for(var j=1;j<=q;j++){
                       v= v.replace('_','\n'); 
                    }
                    return v;
                });
                //auto complete
                $('#institute').autocomplete({
                source:"institute.php"
            });
                $('#institute').autocomplete("option", "appendTo", ".edu-form" );
            });
        </script>
    </body>
</html>
