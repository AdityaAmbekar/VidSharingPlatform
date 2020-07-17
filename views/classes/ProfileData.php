<?php

class ProfileData{

    private $conn, $profileUserObject;

    public function __construct($conn, $profileUsername){

        $this->conn = $conn;
        $this->profileUserObject = new User($conn, $profileUsername) ;
    }

    public function getProfileUserObject(){
        return $this->profileUserObject;
    }
    
    public function getProfileUsername(){
        return $this->profileUserObject->getUsername();
    }

    public function userExists(){

        $query = $this->conn->prepare("SELECT * FROM Users WHERE username = :username");
        $query->bindParam(":username", $profileUsername);
        $profileUsername = $this->getProfileUsername();

        $query->execute();

        return $query->rowCount() != 0;
    }

    public function getCoverPhoto(){

        return "assets/images/coverphoto/default-cover-photo.png";
    }

    public function getProfileUserFullname(){

        return $this->profileUserObject->getName();
    }

    public function getProfilePic(){

        return $this->profileUserObject->getProfilePic();
    }

    public function getSubscriberCount(){
        return $this->profileUserObject->getSubscriberCount();
    }

    public function getUsersVideos(){

        $query = $this->conn->prepare("SELECT * FROM Videos WHERE uploadedBy=:uploadedBy ORDER BY uploadDate DESC");
        $query->bindParam(":uploadedBy", $uploadedBy);
        $uploadedBy = $this->getProfileUsername();

        $query->execute();

        $videos = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $videos[] = new Video($this->conn, $row, $this->profileUserObject->getUsername());
        }

        return $videos;
    }

    public function getAllUserDetails(){

        return array(
            "Name" => $this->getProfileUserFullname(),
            "Username" => $this->getProfileUsername(),
            "Subscribers" => $this->getSubscriberCount(),
            "Total Views" => $this->getTotalViews(),
            "Joined" => $this->getSignUpDate()
        );
    }

    private function getTotalViews(){

        $query = $this->conn->prepare("SELECT sum(views) FROM Videos WHERE uploadedBy = :uploadedBy");
        $query->bindParam(":uploadedBy", $uploadedBy);
        $uploadedBy = $this->getProfileUsername();

        $query->execute();

        return $query->fetchColumn();
    }
    
    private function getSignUpDate(){

        $date = $this->profileUserObject->getSignUpDate();
        return date("F j, Y", strtotime($date));
    }
    
}

?>