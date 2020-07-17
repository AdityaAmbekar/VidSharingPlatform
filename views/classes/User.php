<?php

class User{

    private $conn;
    private $sqlData;

    public function __construct($conn, $usernameVariable){

        $this->conn = $conn;

        $query = $this->conn->prepare("SELECT * FROM Users WHERE username = :username");

        $query->bindParam(":username", $usernameVariable);
        $query->execute();

        $this->sqlData = $query->fetch(PDO::FETCH_ASSOC); 
           //built in tells that to fetch results in associative array(ie dictionary type)
    }

    public static function isLoggedIn(){
        return isset($_SESSION["userLoggedIn"]);
    }

    public function getUsername(){
        return User::isLoggedIn() ? $this->sqlData["username"] : "";
    }

    public function getFirstname(){
        return $this->sqlData["firstName"] ;
    }

    public function getLastname(){
        return $this->sqlData["lastName"] ;
    }

    public function getName(){

        return $this->sqlData["firstName"] . " " . $this->sqlData["lastName"];
    }

    public function getEmail(){
        return $this->sqlData["email"] ;
    }

    public function getProfilePic(){
        return $this->sqlData["profilePic"] ;
    }

    public function getSignUpDate(){
        return $this->sqlData["signUpDate"] ;
    }

    public function isSubscribedTo($userTo){

        $query = $this->conn->prepare("SELECT * FROM Subscribers WHERE userTo = :userTo AND userFrom = :userFrom");

        $query->bindParam(":userTo", $userTo);
        $query->bindParam(":userFrom", $username);
        $username = $this->getUsername();

        $query->execute();

        return $query->rowCount() > 0;
    }

    public function getSubscriberCount(){

        $query = $this->conn->prepare("SELECT * FROM Subscribers WHERE userTo = :userTo");

        $query->bindParam(":userTo", $username);
        $username = $this->getUsername();

        $query->execute();

        return $query->rowCount();
    }

    public function getSubscriptions(){

        $query = $this->conn->prepare("SELECT userTo FROM Subscribers WHERE userFrom=:userFrom");

        $query->bindParam(":userFrom", $username);
        $username = $this->getUsername();

        $query->execute();

        $subs = array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $user = new User($this->conn, $row["userTo"]);
            array_push($subs, $user);
        }
        return $subs;
    }


}


?>