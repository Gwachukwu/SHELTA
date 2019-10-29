<?php
session_start();

if(isset($_REQUEST['code'])){
    $code = $_REQUEST['code'];
    $response = array();

    //Check content of request if it posesses code
    if($code == 'login'){
        $username = filter_input(INPUT_POST,"username",FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST,"password",FILTER_SANITIZE_STRING);

        if ($username == "dammyadediran" || $username == "admin") {
            $_SESSION['username'] = $username;
            $_SESSION['id'] = $username;
            echo '<script>console.log("loggedin")</script>';
            header('Location:index.php');
        } else{
          $login_error_message = 'Username or Password Incorrect';
          header('Location:index.php');
        }
        
    } else if ($code == 'logout'){
        session_destroy();
        header('Location:index.php');
    }
}
?>