<?php

class Video{

    private $conn;
    private $sqlData;
    private $userLoggedInObject;

    public function __construct($conn, $input, $userLoggedInObject){

        $this->conn = $conn;
        $this->userLoggedInObject = $userLoggedInObject;

        if(is_array($input)){
            $this->sqlData = $input;
        }
        else {
            $query = $this->conn->prepare("SELECT * FROM Videos WHERE id = :id");

            $query->bindParam(":id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC); 
            //built in tells that to fetch results in associative array(ie dictionary type)
        }    
    }

    public function getId(){
        return $this->sqlData['id'] ;
    }

    public function getUploadedBy(){
        return $this->sqlData['uploadedBy'] ;
    }

    public function getTitle(){
        return $this->sqlData['title'] ;
    }

    public function getDescription(){
        return $this->sqlData['description'] ;
    }

    public function getCategory(){
        return $this->sqlData['category'] ;
    }

    public function getPrivacy(){
        return $this->sqlData['privacy'] ;
    }

    public function getFilePath(){
        return $this->sqlData['filePath'] ;
    }

    public function getUploadDate(){
        $date =  $this->sqlData['uploadDate'] ;
        return date("M j, Y", strtotime($date));
    }

    public function getTimestamp(){
        $date =  $this->sqlData['uploadDate'] ;
        $time = strtotime($date . '+5.5 hours');
        $time_difference = time() - $time;

        if( $time_difference < 1 ) { return 'just now'; }
        $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
                    30 * 24 * 60 * 60       =>  'month',
                    24 * 60 * 60            =>  'day',
                    60 * 60                 =>  'hour',
                    60                      =>  'minute',
                    1                       =>  'second'
        );

        foreach( $condition as $secs => $str )
        {
            $d = $time_difference / $secs;

            if( $d >= 1 )
            {
                $t = round( $d );
                return  $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
            }
        }
    }

    public function getViews(){
        return $this->sqlData['views'] ;
    }

    public function getDuration(){
        return $this->sqlData['duration'] ;
    }

    public function incrementViewsCount(){

        $query = $this->conn->prepare("UPDATE Videos SET views=views+1 WHERE id=:id");

        $query->bindParam(":id", $videoId);
        $videoId = $this->getId();

        $query->execute();

        $this->sqlData['views'] = $this->sqlData['views'] + 1;   //becoz sql data mai previous value tha we need to update 

    }

    public function getLikes(){

        $query = $this->conn->prepare("SELECT count(*) as 'count' FROM Likes WHERE videoId = :videoId");

        $query->bindParam(":videoId", $videoId);
        $videoId = $this->getId();                     //we make variable as direct daala toh warning ayega
        $query->execute();

        $data = $query->fetch(PDO::FETCH_ASSOC);

        return $data['count'];
        
    }

    public function getDislikes(){

        $query = $this->conn->prepare("SELECT count(*) as 'count' FROM Dislikes WHERE videoId = :videoId");

        $query->bindParam(":videoId", $videoId);
        $videoId = $this->getId();                     //we make variable as direct daala toh warning ayega
        $query->execute();

        $data = $query->fetch(PDO::FETCH_ASSOC);

        return $data["count"];
        
    }

    public function like(){

        $username = $this->userLoggedInObject->getUsername();
        $id = $this->getId();
    
        if($this->wasLiked()){
             
            $query = $this->conn->prepare("DELETE FROM Likes WHERE username= :username AND videoId =  :videoId ");

            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);

            $query->execute();

            $result = array(
                "likes" => -1,
                "dislikes" => 0
            );            
        }
        else{

            $query = $this->conn->prepare("DELETE FROM Dislikes WHERE username =:username AND videoId =:videoId ");

            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);

            $query->execute();

            $count = $query->rowCount();
            
            $query = $this->conn->prepare("INSERT INTO Likes(username, videoId) VALUES(:username, :videoId) ");

            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);

            $query->execute();


            $result = array(
                "likes" => 1,
                "dislikes" => 0 - $count
            );
        }
        return json_encode($result);
    }

    public function wasLiked(){

        $id = $this->getId();

        $query = $this->conn->prepare("SELECT  * FROM Likes WHERE username = :un AND videoId = :vi");

        $query->bindParam(":un", $username);
        $query->bindParam(":vi", $id);

        $username = $this->userLoggedInObject->getUsername();
        $query->execute();

        return $query->rowCount() > 0 ;
    }

    public function dislike(){

        $username = $this->userLoggedInObject->getUsername();
        $id = $this->getId();
    
        if($this->wasDisliked()){
             
            $query = $this->conn->prepare("DELETE FROM Dislikes WHERE username =:username AND videoId =:videoId ");

            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);

            $query->execute();

            $result = array(
                "dislikes" => -1 ,
                "likes" => 0 
            );   
        }
        else{

        
            $query = $this->conn->prepare("DELETE FROM Likes WHERE username = :username AND videoId = :videoId ");

            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);

            $query->execute();

            $count = $query->rowCount(); 

            $query = $this->conn->prepare("INSERT INTO Dislikes(username, videoId) VALUES(:username, :videoId)");

            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);

            $query->execute();

            $result = array(
                "dislikes" => 1 ,
                "likes" => 0 - $count 
            );
        }
        return json_encode($result);
    }

    public function wasDisliked(){

        $id = $this->getId();

        $query = $this->conn->prepare("SELECT  * FROM Dislikes WHERE username = :un AND videoId = :vi");

        $query->bindParam(":un", $username);
        $query->bindParam(":vi", $id);

        $username = $this->userLoggedInObject->getUsername();
        $query->execute();

        return $query->rowCount() > 0 ;

    }

    public function getNumberOfComments(){

        $query = $this->conn->prepare("SELECT * FROM Comments WHERE videoId = :videoId");

        $query->bindParam(":videoId", $id);
        $id = $this->getId();

        $query->execute();

        return $query->rowCount();
    }

    public function getThumbnail(){

        $query = $this->conn->prepare("SELECT filePath FROM Thumbnails WHERE videoId = :videoId AND selected =1 ");

        $query->bindParam(":videoId", $videoId);
        $videoId = $this->getId();

        $query->execute();

        return $query->fetchColumn();
    }

    public function getComments(){

        $query = $this->conn->prepare("SELECT * FROM Comments WHERE videoId = :videoId AND responseTo = 0 ORDER BY datePosted DESC");

        $query->bindParam(":videoId", $id);
        $id = $this->getId();

        $query->execute(); 
        
        $comments= array();

        while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $comment = new Comments($this->conn, $row, $this->userLoggedInObject, $id);
            array_push($comments, $comment);
        }
        return $comments;
    }
}


?>