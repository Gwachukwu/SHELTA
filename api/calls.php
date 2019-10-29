<?php

require_once 'dbconnect.php';
//require 'PHPMailerAutoload.php';
/**
 * 
 */

class calls{
	private $host = 'localhost';
    private $user = 'pacsol5_admin';
    private $db = 'pacsol5_pacsolicitors';
    private $pass = 'password@1.';
    private $conn;

	function __construct()
	{	 
		$this -> conn = new PDO("mysql:host=".$this -> host.";dbname=".$this -> db, $this -> user, $this -> pass);
      	//$this -> mail = new PHPMailer;
	}
    
    public function sendMail(){
        $from = "test@hostinger-tutorials.com";
        $to = "ayoolasama@gmail.com";
        $subject = "Checking PHP mail";
        $message = "PHP mail works just fine";
        $headers = "From:" . $from;
        return mail($to,$subject,$message, $headers);    
    }
    
    public function getMyDocuments($email){
        $sql = "SELECT doc_id, doc_name, doc_datecreated, doc_dateupdated, doc_ownerID, doc_url from documents WHERE (doc_ownerID = '$email')";
        $query= $this -> conn -> prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }
    
    public function getMyDocument($docID){
        $sql = "SELECT doc_name, doc_datecreated, doc_dateupdated, doc_ownerID, doc_url from documents WHERE (doc_id = '$docID')";
        $query= $this -> conn -> prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }
    
	public function getTotalDocuments($email){
        $sql = "SELECT doc_id, doc_name, doc_datecreated, doc_dateupdated, doc_ownerID, doc_url from documents WHERE (doc_ownerID = '$email')";
        $query= $this -> conn -> prepare($sql);
        $query->execute();
        $noOfDocs = $query->rowCount();
        return $noOfDocs;
    }

    public function deleteClient($id){
        $sql = "DELETE FROM users WHERE userEmail = '$id'";
        $query = $this -> conn -> prepare($sql);
        $result = $query->execute();
        return $result;
    }

    public function getSpecificUser($id){
        $sql = "SELECT userFirstName, userLastName, userEmail, userPhoneNumber from users WHERE (userID = '$id')";
        $query= $this -> conn -> prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        return $results;
    }

    public function password_generate() {
      $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz!?@#';
      return substr(str_shuffle($data), 0, 8);
    }
}

?>