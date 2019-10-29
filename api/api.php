<?php

include('config.php');

$login_error_message = '';
$register_error_message = '';
$register_success_message = '';
$newpub_error_message = '';
$newpub_success_message = '';
$pubupdate_error_message = '';
$pubupdate_success_message = '';
$newstaff_success_message = '';
$newstaff_error_message = '';
$changePwd_error_message = '';
$changePwd_success_message = '';
$profileupdate_error_message = '';
$profileupdate_success_message = '';
$resetpwd_error_message = '';
$resetpwd_success_message = '';
$addcomment_error_message = '';
$addcomment_success_message = '';


//Check if a code comes with the request
if(isset($_REQUEST['code'])){
    $code = $_REQUEST['code'];
    //$dbh = new dbconnect();
    $response = array();

    //Check content of request if it posesses code
    if($code == 'login'){
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];
        echo "<script>console.log('$username')</script>";

        $sql = "SELECT userID, userName, userEmail, userFirstName, userLastName FROM users WHERE (userName='$username' and userPassword='$password')";
        $query= $dbh -> prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        
        if($query->rowCount() > 0) {

            $result = $results[0];
            $array = json_decode(json_encode($result), True);
            //print_r($array);
            $_SESSION['email'] = $array['userEmail'];
            $_SESSION['username'] = $array['userName'];
            $_SESSION['firstname'] = $array['userFirstName'];
            $_SESSION['lastname'] = $array['userLastName'];
            $_SESSION['id'] = $array['userID'];
            header('Location:index.php');

        } else {
          $login_error_message = 'Username or Password Incorrect';
        }
        
    } else if ($code == 'forgotpwd') { 
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $sql0 = "SELECT userFirstName, userLastName FROM users WHERE (userEmail = '$email')";
        $query0 = $dbh -> prepare($sql0);
        $query0 -> execute();
        $results0 = $query0->fetchAll(PDO::FETCH_OBJ);
        $mynumber = $query0->rowCount();

        if ($mynumber > 0){
            $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz!?@#';
            //$newpwd = substr(str_shuffle($data), 0, 7);
            $newpwd = "admin";
            $newencrypted = hash('sha256', $newpwd);
            $sqlz = "UPDATE users SET userPassword = '$newencrypted' WHERE userEmail = '$email'";
            $queryz = $dbh -> prepare($sqlz);
            $resultz = $queryz->execute();
            if ($resultz){
                //$calls->sendMail();
            } else {
                $resetpwd_error_message = mysqli_error();
            }
        } else {
            $resetpwd_error_message = "Email doesn't exist in database";
        }

    } else if ($code == 'signup'){
        $firstname = $_REQUEST["firstname"];
        $username = $_REQUEST["username"];
        $lastname = $_REQUEST["lastname"];
        $email = $_REQUEST["email"];
        //$phonenumber = $_REQUEST["firstname"];
        $password = $_REQUEST["password"];
        $repassword = $_REQUEST["confirmpassword"];
        //$encrypted = hash('sha256', $password);

        if ($password != $repassword) {
            $register_error_message = 'Both Passwords are not the same';
            echo "<script>window.alert('$register_error_message')</script>";
        } else if ($firstname == "") {
            $register_error_message = 'First Name field is required!';
            echo "<script>window.alert('$register_error_message')</script>";
        } else if ($lastname == "") {
            $register_error_message = 'Last Name field is required!';
            echo "<script>window.alert('$register_error_message')</script>";
        } else if ($email == "") {
            $register_error_message = 'Email field is required!';
            echo "<script>window.alert('$register_error_message')</script>";
        } else {
            $sql0 = "SELECT userFirstName, userLastName FROM users WHERE (userEmail = '$email')";
            $query0 = $dbh -> prepare($sql0);
            $query0 -> execute();
            $results0 = $query0->fetchAll(PDO::FETCH_OBJ);
            $mynumber = $query0->rowCount();

            if ($mynumber > 0){
                $register_error_message = 'Account already exists in database';
                echo "<script>window.alert('$register_error_message')</script>";
            } else {
                //Create Profile
                $sql = "INSERT INTO users (userName, userFirstName, userLastName, userEmail, userPassword) VALUES('$username', '$firstname', '$lastname', '$email', '$password')";
                $query = $dbh->prepare($sql);
                $result = $query->execute();
               
                if ($result){
                    $register_success_message = "Registration Successful";
                    echo "<script>window.alert('$register_success_message')</script>";
                } else {
                    
                    $register_error_message = mysqli_error();
                    echo "<script>window.alert('$register_error_message')</script>";
                }
            }
        }

    } else if ($code == 'changePwd') {
        $oldpwd = filter_input(INPUT_POST, "oldpassword", FILTER_SANITIZE_STRING);
        $newpwd = filter_input(INPUT_POST, "newpassword", FILTER_SANITIZE_STRING);
        $renewpwd = filter_input(INPUT_POST, "renewpassword", FILTER_SANITIZE_STRING);
        $oldencrypted = hash('sha256', $oldpwd);
        $newencrypted = hash('sha256', $newpwd);
        $renewencrypted = hash('sha256', $renewpwd);
        $user_Email = $_SESSION['email'];


        if ($oldpwd == ""){
            $changePwd_error_message = "Old Password Field is empty";
        } else if ($newpwd == ""){
            $changePwd_error_message = "New Password Field is empty";
        } else if ($renewpwd == "") {
            $changePwd_error_message = "Confirm New Password Field is empty";
        } else {
            $sql = "SELECT userPassword from users WHERE (userEmail = '$user_Email' AND userPassword = '$oldencrypted')";
            $query= $dbh -> prepare($sql);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);

            //Check if password inputted is same as that in DB for current user
            if($query->rowCount() > 0) {

                //Check If Both New Password Fields Have Same Content
                if ($newencrypted == $renewencrypted){
                    $sqlz = "UPDATE users SET userPassword = '$newencrypted' WHERE userEmail = '$user_Email'";
                    $queryz = $dbh -> prepare($sqlz);
                    $resultz = $queryz->execute();

                    //Check if password was changed successfully in the DB
                    if ($resultz > 0){
                        $changePwd_success_message = "Password Changed Successfully";
                    } else {
                        $changePwd_error_message = "Password Change Unsuccessful";
                    }

                } else {
                    $changePwd_error_message = "Old and New Password are not the same";
                }

            } else{
              
              $changePwd_error_message = 'Incorrect Password';

            }
        } 
    } else if ($code == 'updateprofile'){
        $firstname = filter_input(INPUT_POST, "firstname", FILTER_SANITIZE_STRING);
        $lastname = filter_input(INPUT_POST, "lastname", FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_STRING);
        $phonenumber = filter_input(INPUT_POST, "phonenumber", FILTER_SANITIZE_STRING);

        if ($firstname == "") {
            $profileupdate_error_message = 'First Name field is required!';
        } else if ($lastname == "") {
            $profileupdate_error_message = 'Last Name field is required!';
        } else if ($email == "") {
            $profileupdate_error_message = 'Email field is required!';
        } else if ($phonenumber == "") {
            $profileupdate_error_message = 'Phone Number field is required!';
        } else {

            //Update Profile
            $sql = "UPDATE users SET userFirstName = '$firstname', userLastName = '$lastname', userPhoneNumber = '$phonenumber' WHERE userEmail = '$email'";
            //echo $sql;
            $query = $dbh->prepare($sql);
            $result = $query->execute();
           
            if ($result){
                $profileupdate_success_message = "Client Info Updated Successfully";
                $_SESSION['email'] = $email;
                $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;
                $_SESSION['phonenumber'] = $phonenumber;
            } else {
                
                $profileupdate_error_message = mysqli_error();
            }
        }

    } else if ($code == 'addimage') {
        $target_dir = "staffpics/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
                $sql = "INSERT into images (image_file) VALUES('$target_file')";
                $query = $dbh->prepare($sql);
                $query->execute();
            } else {
                echo $php_errormsg;
            }
        }

    } else if ($code == 'addmycomment') {
        $docowner = filter_input(INPUT_POST, "docowner", FILTER_SANITIZE_EMAIL); //docowner
        $usercomments = filter_input(INPUT_POST, "usercomments", FILTER_SANITIZE_STRING); //content
        $docfiledate = filter_input(INPUT_POST, "docfiledate", FILTER_SANITIZE_STRING);
        //date_default_timezone_set('Africa/Lagos');
        $timestamp = time(); 
        $datetime = date("F d, Y h:i:s A", $timestamp); //feedbackdate
        $docid = $_POST['docid'];
        
        $target_dir = "userdocuments/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $noupload = 0;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        
        if ($docowner == "" || $docfiledate == "" || $docid == "") {
            $addcomment_error_message = "All Fields are required";
        } else {
            $sql = "INSERT INTO documentfeedback (feedback_content, feedback_date, feedback_docid, feedback_docowner, feedback_filedate) VALUES('$usercomments', '$datetime', '$docid', '$docowner', '$docfiledate')";
            // print_r($sql);
            $query = $dbh->prepare($sql);
            $result = $query->execute();
            
                   
            if ($result){
                $addcomment_success_message = "Comment Shared Successfully";
                //echo $data;
            } else {
                        
                $addcomment_error_message = "Failed to share comment";
            }
        }
        
    }

}  else {
    //echo "No Code";
}
?>


