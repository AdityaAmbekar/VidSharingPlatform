<?php

class SubscriptionsProvider{

    private $conn, $userLoggedInObject;

    public function __construct($conn, $userLoggedInObject){

        $this->conn = $conn;
        $this->userLoggedInObject = $userLoggedInObject;
    }

    public function getVideos(){

        $videos = array();
        $subscriptions =  $this->userLoggedInObject->getSubscriptions();

        if(sizeof($subscriptions) > 0){

            $condition ="";
            $i = 0;

            while($i < sizeof($subscriptions)){

                if( $i == 0){
                    $condition .= "WHERE uploadedBy = ?";
                }
                else{
                    $condition .= " OR uploadedBy = ?";
                }
                $i++;
            }

            $sql = "SELECT * FROM Videos $condition ORDER BY uploadDate DESC";
            $query = $this->conn->prepare($sql);

            $i = 1;
            foreach($subscriptions as $sub){
                $subUsername = $sub->getUsername();
                $query->bindValue($i, $subUsername);
                $i++;
            }

            $query->execute();

            while($row = $query->fetch(PDO::FETCH_ASSOC)){

                $video = new Video($this->conn, $row, $this->userLoggedInObject);
                array_push($videos, $video);
            }
        }

        return $videos;
    }
}

?>