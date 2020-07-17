<?php

class TrendingProvider{

    private $conn, $userLoggedInObject;

    public function __construct($conn, $userLoggedInObject){

        $this->conn = $conn;
        $this->userLoggedInObject = $userLoggedInObject;
    }

    public function getVideos(){

        $videos = array();

        $query = $this->conn->prepare("SELECT * FROM Videos WHERE uploadDate >= now() - INTERVAL 20 DAY ORDER BY views
                                        DESC LIMIT 15");

        $query->execute();

        while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $video = new Video($this->conn, $row, $this->userLoggedInObject);
            array_push($videos, $video);
        }

        return $videos;
    }

}

?>