<?php

class LikedVideosProvider{

    private $conn, $userLoggedInObject;

    public function __construct($conn, $userLoggedInObject){

        $this->conn = $conn;
        $this->userLoggedInObject = $userLoggedInObject;
    }

    public function getVideos(){

        $videos = array();

        $query = $this->conn->prepare("SELECT videoId FROM Likes WHERE username= :username AND commentId=0
                                        ORDER BY id DESC");

        $query->bindParam(":username", $username);
        $username = $this->userLoggedInObject->getUsername();
        $query->execute();

        while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $video = new Video($this->conn, $row["videoId"], $this->userLoggedInObject);
            array_push($videos, $video);
        }

        return $videos;
    }

}

?>