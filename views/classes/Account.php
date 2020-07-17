<?php

class Account {

    private $conn;
    private $errorArray =  array();

    public function  __construct($conn){

        $this->conn = $conn;
    }

    public function login($username, $password){

        $password = hash("sha256", $password);

        $query = $this->conn->prepare("SELECT  * FROM Users WHERE username = :username AND password = :password ");

        $query->bindParam(":username", $username);
        $query->bindParam(":password", $password);

        $query->execute();

        if($query->rowCount() == 1){
            return true;
        }

        array_push($this->errorArray, Constants::$loginFailed);
        return false;
    }

    public function register($firstName, $lastName, $username, $email, $password1, $password2){

        $this->validateFirstName($firstName);
        $this->validateLastName($lastName);
        $this->validateUserName($username);
        $this->validateEmail($email);
        $this->validatePassword($password1,$password2);

        if(empty($this->errorArray)){

            $password1 = hash("sha256", $password1);
            $profilePic = "assets/images/icons/Default-Profile.png";

            $query = $this->conn->prepare("INSERT INTO  Users(firstName, lastName, username, email, password, profilePic)
                                            VALUES(:fn, :ln, :un, :em, :pw, :pp)");

            $query->bindParam(":fn", $firstName);
            $query->bindParam(":ln", $lastName);
            $query->bindParam(":un", $username);
            $query->bindParam(":em", $email);
            $query->bindParam(":pw", $password1);
            $query->bindParam(":pp", $profilePic);
            
            return $query->execute();
            // return insertDetails($firstName, $lastName, $username, $email, $password1);
            
        }
        else{
            return false;
        }

    }

    public function updateDetails($firstName, $lastName, $email, $username){

        $this->validateFirstName($firstName);
        $this->validateLastName($lastName);
        $this->validateUpdatedEmail($email, $username);

        if(empty($this->errorArray)){
            //update
            $query = $this->conn->prepare("UPDATE Users SET firstName=:fn, lastName=:ln, email=:em WHERE username=:un"); 

            $query->bindParam(":fn", $firstName);
            $query->bindParam(":ln", $lastName);
            $query->bindParam(":em", $email);
            $query->bindParam(":un", $username);

            return $query->execute();
        }
        else{
            return false;
        }
    }

    public function updatePassword($oldPassword, $newPassword, $newPassword2, $username){

        $this->validateOldPassword($oldPassword, $username);
        $this->validatePassword($newPassword, $newPassword2);

        if(empty($this->errorArray)){
            //update
            $query = $this->conn->prepare("UPDATE Users SET password=:password WHERE username=:un"); 

            $password = hash("sha256", $newPassword);
            $query->bindParam(":password", $password);
            $query->bindParam(":un", $username);

            return $query->execute();
        }
        else{
            return false;
        }
    }

    private function validateOldPassword($password, $username){

        $password = hash("sha256", $password);

        $query = $this->conn->prepare("SELECT  * FROM Users WHERE username = :username AND password = :password ");

        $query->bindParam(":username", $username);
        $query->bindParam(":password", $password);

        $query->execute();

        if($query->rowCount() == 0){
            array_push($this->errorArray, Constants::$passwordIncorrect);
        }

    }


    // public function insertDetails($firstName, $lastName, $username, $email, $password1){

    //     //fuck this function for now agae dekha jaega

    // }

    private function validateFirstName($firstName){
        if(strlen($firstName) > 25 || strlen($firstName) < 2){
            array_push($this->errorArray, Constants::$firstNameCharacters ) ;
        }
    }

    private function validateLastName($lastName){
        if(strlen($lastName) > 25 || strlen($lastName) < 2){
            array_push($this->errorArray, Constants::$lastNameCharacters ) ;
        }    
    }

    private function validateUserName($username){
        if(strlen($username) > 25 || strlen($username) < 5){
            array_push($this->errorArray, Constants::$usernameCharacters ) ;
            return;
        }

        $query = $this->conn->prepare("SELECT username FROM Users WHERE username=:un");
        $query->bindParam(":un", $username);
        $query->execute();

        if($query->rowCount() !=0) {
            array_push($this->errorArray, Constants::$usernameTaken) ;
        }   
    }

    private function validateEmail($email){
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            array_push($this->errorArray, Constants::$emailNotValid ) ;
            return;

        }

        $query = $this->conn->prepare("SELECT email FROM Users WHERE email=:email");
        $query->bindParam(":email", $email);
        $query->execute();

        if($query->rowCount() !=0) {
            array_push($this->errorArray, Constants::$emailTaken) ;
        }     
    }

    private function validateUpdatedEmail($email, $username){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            array_push($this->errorArray, Constants::$emailNotValid ) ;
            return;

        }

        $query = $this->conn->prepare("SELECT email FROM Users WHERE email=:email AND username <> :username");
        $query->bindParam(":email", $email);
        $query->bindParam(":username", $username);
        $query->execute();

        if($query->rowCount() !=0) {
            array_push($this->errorArray, Constants::$emailTaken) ;
        }     
    }

    private function validatePassword($p1, $p2){

        if($p1 != $p2){
            array_push($this->errorArray, Constants::$passwordDoesntMatch) ;
            return;
        }

        if(preg_match("/[^A-Za-z0-9]/", $p1)) {    //using inbuilt regex function in php 
            array_push($this->errorArray, Constants::$passwordHasSpecial) ;
            return;
        }

        if(strlen($p1) > 30 || strlen($p1) < 6){
            array_push($this->errorArray, Constants::$passwordCharacters ) ;
            return;
        }
    }

    public function getError($error){
        if(in_array($error, $this->errorArray)){

            return "<span class= 'errorMessage'>$error</span>";
        }
    }

    public function getFirstError(){

        if(!empty($this->errorArray)){

            return $this->errorArray[0]; 
        }
        else{
            return "";
        }
    }



}


?>