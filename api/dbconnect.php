<?php
 
class dbconnect{
 
    private $host = 'localhost';
    private $user = 'root';
    private $db = 'shelta';
    private $pass = '';
    private $conn;

    public function __construct() {
     
       $this -> conn = new PDO("mysql:host=".$this -> host.";dbname=".$this -> db, $this -> user, $this -> pass);
     
    }

}

?>