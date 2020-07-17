<?php

class SearchResultsProvider{

    private $conn, $userLoggedInObject;

    public function __construct($conn, $userLoggedInObject){

        $this->conn = $conn;
        $this->userLoggedInObject = $userLoggedInObject;
    }

    public function getVideos($term, $orderBy){

        $query = $this->conn->prepare("SELECT * FROM Videos WHERE title LIKE CONCAT('%', :term, '%') 
                                        OR uploadedBy LIKE CONCAT('%', :term, '%')
                                        ORDER BY $orderBy DESC");

        $query->bindParam(":term", $term);
        // $query->bindParam(":orderBy", $orderBy);   //remember orederby mai error deta no need to bind param

        $query->execute();

        $videos = array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $video = new Video($this->conn, $row, $this->userLoggedInObject);
            array_push($videos, $video); 

        }

        return $videos;
    }



}

?>