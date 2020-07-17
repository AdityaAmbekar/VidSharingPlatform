<?php

require_once("ButtonProvider.php");
require_once("CommentControl.php");

class Comments{

    private $conn, $sqlData, $userLoggedInObject, $videoId;

    public function __construct($conn, $input, $userLoggedInObject, $videoId){

        if(!is_array($input)){
            $query = $conn->prepare("SELECT * FROM Comments WHERE id=:id");
            $query->bindParam(":id", $input);
            $query->execute();

            $input = $query->fetch(PDO::FETCH_ASSOC); 
        }
        $this->conn= $conn;
        $this->sqlData = $input;
        $this->userLoggedInObject = $userLoggedInObject;
        $this->videoId =$videoId;

    }

    public function create(){

        $id = $this->sqlData["id"];
        $videoId = $this->getVideoId();
        $body = $this->sqlData["body"];
        $postedBy = $this->sqlData["postedBy"];
        $profileButton = ButtonProvider::createProfileButton($this->conn, $postedBy);
        $timestamp = $this->getTimestamp();

        $commentControlObject = new CommentControl($this->conn, $this, $this->userLoggedInObject);
        $commentControl = $commentControlObject->create();

        $numResponses = $this->getNumberOfReplies();
 
        if($numResponses > 0){
            $viewRepliesText = "<span class='repliesSection viewReplies' onclick='getReplies($id, this, $videoId)'>$numResponses Replies</span>";
        }
        else{

            $viewRepliesText = "<div class='repliesSection'></div>"; 
        }

        return "<div class='itemContainer'>
                    <div class='comment'>
                        $profileButton

                        <div class='mainContainer'>

                            <div class='commentHeader'>
                                <a href='profile.php?username=$postedBy'>
                                    <span class='username'>$postedBy</span>
                                </a>
                                <span class='timestamp'>$timestamp</span> 
                            </div>

                            <div class='body'>
                                $body
                            </div>
                        </div>
                    </div>
                    $commentControl
                    $viewRepliesText
                </div>";
    }

    public function getId(){

        return $this->sqlData["id"];
    }

    public function getVideoId(){

        return $this->videoId ;
    }
    
    public function getTimestamp(){

        $date =  $this->sqlData['datePosted'] ;
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





        // $date =  $this->sqlData['datePosted'] ;
        // $full = false;
        // $now = new DateTime;
        // $ago = new DateTime($date);
        // $ago->modify('+5 hours');
        // $diff = $now->diff($ago);

        // $diff->w = floor($diff->d / 7);
        // $diff->d -= $diff->w * 7;

        // $string = array(
        //     'y' => 'year',
        //     'm' => 'month',
        //     'w' => 'week',
        //     'd' => 'day',
        //     'h' => 'hour',
        //     'i' => 'minute',
        //     's' => 'second',
        // );
        // foreach ($string as $k => &$v) {
        //     if ($diff->$k) {
        //         $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        //     } else {
        //         unset($string[$k]);
        //     }
        // }

        // if (!$full) $string = array_slice($string, 0, 1);
        // return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public function getNumberOfReplies(){

        $query = $this->conn->prepare("SELECT COUNT(*) FROM Comments WHERE responseTo = :responseTo");

        $query->bindParam(":responseTo", $id);
        $id = $this->sqlData["id"];

        $query->execute();

        return $query->fetchColumn();
    }

    public function getLikes(){

        $query = $this->conn->prepare("SELECT COUNT(*) AS 'count' FROM Likes WHERE commentId=:commentId");

        $query->bindParam(":commentId", $commentId);
        $commentId = $this->getId();

        $query->execute();

        $data = $query->fetch(PDO::FETCH_ASSOC);
        $numLikes = $data["count"];

        // $query = $this->conn->prepare("SELECT COUNT(*) AS 'count' FROM Dislikes WHERE commentId=:commentId");

        // $query->bindParam(":commentId", $commentId);
        // $commentId = $this->getId();

        // $query->execute();

        // $data = $query->fetch(PDO::FETCH_ASSOC);
        // $numDislikes = $data["count"];

        return $numLikes;
    }

    public function getDislikes(){

        $query = $this->conn->prepare("SELECT COUNT(*) AS 'count' FROM Dislikes WHERE commentId=:commentId");

        $query->bindParam(":commentId", $commentId);
        $commentId = $this->getId();

        $query->execute();

        $data = $query->fetch(PDO::FETCH_ASSOC);
        $numDislikes = $data["count"];

        return $numDislikes;
    }


    public function like(){

        $username = $this->userLoggedInObject->getUsername();
        $id = $this->getId();
    
        if($this->wasLiked()){
             
            $query = $this->conn->prepare("DELETE FROM Likes WHERE username= :username AND commentId= :commentId ");

            $query->bindParam(":username", $username);
            $query->bindParam(":commentId", $id);

            $query->execute();

            $result = array(
                "likes" => -1,
                "dislikes" => 0
            );            
        }
        else{

            $query = $this->conn->prepare("DELETE FROM Dislikes WHERE username =:username AND commentId =:commentId ");

            $query->bindParam(":username", $username);
            $query->bindParam(":commentId", $id);

            $query->execute();

            $count = $query->rowCount();
            
            $query = $this->conn->prepare("INSERT INTO Likes(username, commentId) VALUES(:username, :commentId) ");

            $query->bindParam(":username", $username);
            $query->bindParam(":commentId", $id);

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

        $query = $this->conn->prepare("SELECT  * FROM Likes WHERE username = :un AND commentId = :ci");

        $query->bindParam(":un", $username);
        $query->bindParam(":ci", $id);

        $username = $this->userLoggedInObject->getUsername();
        $query->execute();

        return $query->rowCount() > 0 ;
    }


    public function dislike(){

        $username = $this->userLoggedInObject->getUsername();
        $id = $this->getId();
    
        if($this->wasDisliked()){
             
            $query = $this->conn->prepare("DELETE FROM Dislikes WHERE username =:username AND commentId =:commentId ");

            $query->bindParam(":username", $username);
            $query->bindParam(":commentId", $id);

            $query->execute();

            $result = array(
                "dislikes" => -1 ,
                "likes" => 0 
            );   
        } 
        else{

            $query = $this->conn->prepare("DELETE FROM Likes WHERE username = :username AND commentId = :commentId ");

            $query->bindParam(":username", $username);
            $query->bindParam(":commentId", $id);

            $query->execute();

            $count = $query->rowCount(); 

            $query = $this->conn->prepare("INSERT INTO Dislikes(username, commentId) VALUES(:username, :commentId)");

            $query->bindParam(":username", $username);
            $query->bindParam(":commentId", $id);

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

        $query = $this->conn->prepare("SELECT  * FROM Dislikes WHERE username = :un AND commentId = :ci");

        $query->bindParam(":un", $username);
        $query->bindParam(":ci", $id);

        $username = $this->userLoggedInObject->getUsername();
        $query->execute();

        return $query->rowCount() > 0 ;

    }

    public function getReplies(){

        $query = $this->conn->prepare("SELECT * FROM Comments WHERE responseTo = :commentId ORDER BY datePosted ASC");

        $query->bindParam(":commentId", $id);
        $id = $this->getId();

        $query->execute(); 
        
        $comments= "";
        $videoId = $this->getVideoId();

        while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $comment = new Comments($this->conn, $row, $this->userLoggedInObject, $videoId);
            $comments .= $comment->create();
        }
        return $comments;
    }
}


?>